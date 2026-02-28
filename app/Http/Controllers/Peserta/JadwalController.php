<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\TransaksiPembayaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index()
    {
        $peserta = Auth::user()->peserta;
        
        // Perbaikan: Gunakan nama fungsi relasi yang ada di Model PesertaArisan (skemaArisan)
        $skema = $peserta->skemaArisan; 

        if (!$skema) {
            return "Data skema tidak ditemukan untuk peserta ini. Pastikan kolom id_skema di tabel peserta_arisan sudah terisi dengan ID yang benar.";
        }

        // Gunakan nama kolom yang benar dari Model SkemaArisan
        $tenor = $skema->durasi_bulan; 

        // 2. Ambil semua transaksi peserta ini
        $semuaTransaksi = TransaksiPembayaran::where('id_pesertaarisan', $peserta->id_pesertaarisan)
            ->orderBy('created_at', 'asc')
            ->get();

        // 3. Tentukan tanggal mulai arisan
        if ($semuaTransaksi->isNotEmpty()) {
            // Ambil bulan iuran pertama sebagai acuan mulai
            $startDate = Carbon::parse($semuaTransaksi->first()->bulan_iuran)->startOfMonth();
        } else {
            // Jika belum ada tagihan sama sekali, mulai dari bulan sekarang
            $startDate = Carbon::now()->startOfMonth();
        }

        // 4. Normalisasi data transaksi (Key: "month-year")
        $transaksiMapped = $semuaTransaksi->mapWithKeys(function ($item) {
            try {
                $date = Carbon::parse($item->bulan_iuran);
                return [$date->month . '-' . $date->year => $item];
            } catch (\Exception $e) {
                return [];
            }
        });

        // 5. Looping berdasarkan durasi_bulan skema
        $daftarBulan = [];
        Carbon::setLocale('id');

        for ($i = 0; $i < $tenor; $i++) {
            // Menghasilkan tanggal berurutan (lintas tahun otomatis)
            $currentDate = $startDate->copy()->addMonths($i);
            
            $searchKey = $currentDate->month . '-' . $currentDate->year;
            $dataTagihan = $transaksiMapped->get($searchKey);

            $daftarBulan[] = [
                'nama'       => $currentDate->translatedFormat('F Y'),
                'bulan_nama' => $currentDate->translatedFormat('F'),
                'short_nama' => $currentDate->translatedFormat('M'),
                'tahun'      => $currentDate->year,
                'tagihan'    => $dataTagihan, 
            ];
        }

        $totalLunas = $semuaTransaksi->where('status_pembayaran', 'sukses')->count();
        
        // Hitung progres
        $progresPersen = $tenor > 0 ? ($totalLunas / $tenor) * 100 : 0;
        $tahunIni = date('Y');

        return view('peserta.jadwal.index', compact('daftarBulan', 'totalLunas', 'tenor', 'progresPersen', 'tahunIni'));
    }
}