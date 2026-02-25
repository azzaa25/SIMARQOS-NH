<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PesertaArisan;
use App\Models\TransaksiPembayaran;
use Carbon\Carbon;

class GenerateTagihanBulanan extends Command
{
    // Nama perintah yang nanti dipanggil di scheduler
    protected $signature = 'arisan:generate-tagihan';

    // Deskripsi agar admin tidak bingung
    protected $description = 'Otomatis buat tagihan iuran setiap tanggal 1 sesuai skema';

    public function handle()
    {
        // 1. Ambil semua peserta yang statusnya aktif
        $pesertas = PesertaArisan::whereHas('user', function($q) {
            $q->where('status', 'aktif');
        })->with('skemaArisan')->get();

        // 2. Tentukan bulan tagihan (Contoh: "Maret 2026")
        $bulanIuran = Carbon::now()->translatedFormat('F Y');

        $count = 0;

        foreach ($pesertas as $p) {
            // 3. Cek apakah tagihan bulan ini sudah ada (cegah duplikat)
            $exists = TransaksiPembayaran::where('id_pesertaarisan', $p->id_pesertaarisan)
                        ->where('bulan_iuran', $bulanIuran)
                        ->exists();

            if (!$exists) {
                $nominalSkema = $p->skemaArisan->nominal_iuran;
                
                // 4. LOGIKA PEMBAGIAN: Jika kelompok, nominal iuran dibagi 7
                if ($p->id_kelompok != null) {
                    $nominalFinal = ceil($nominalSkema / 7); // Pembulatan ke atas
                } else {
                    $nominalFinal = $nominalSkema;
                }

                // 5. Simpan ke database
                TransaksiPembayaran::create([
                    'id_pesertaarisan' => $p->id_pesertaarisan,
                    'order_id' => 'INV-' . strtoupper(bin2hex(random_bytes(3))) . '-' . $p->id_pesertaarisan,
                    'nominal' => $nominalFinal,
                    'bulan_iuran' => $bulanIuran,
                    'status_pembayaran' => 'pending'
                ]);

                $count++;
            }
        }

        $this->info("Berhasil membuat $count tagihan untuk bulan $bulanIuran.");
    }
}