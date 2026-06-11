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
        $skema = $peserta->skemaArisan; 

        if (!$skema) {
            return "Data skema tidak ditemukan. Pastikan id_skema terisi.";
        }

        $tenor = $skema->durasi_bulan; 

        // 🌟 LOGIKA BARU: Tentukan Tanggal Mulai secara Dinamis lewat Hitung Mundur tahun_periode
        $tahunTarget = $peserta->tahun_periode ?? Carbon::now()->year;
        
        $petaIdulAdha = [
            2026 => 5, 2027 => 5, 2028 => 4, 2029 => 4, 2030 => 3
        ];
        $bulanIdulAdha = $petaIdulAdha[$tahunTarget] ?? 5;
        $bulanLunas = $bulanIdulAdha - 1;

        // Cari titik START awal menabung seharusnya
        $tanggalJatuhTempoAkhir = Carbon::create($tahunTarget, $bulanLunas, 1);
        $startDate = $tanggalJatuhTempoAkhir->copy()->subMonths($tenor - 1)->startOfMonth();

        // Ambil data transaksi yang sudah riil terjadi di database
        $semuaTransaksi = TransaksiPembayaran::where('id_pesertaarisan', $peserta->id_pesertaarisan)->get();

        // Normalisasi data transaksi ke dalam array (Key: "n-Y")
        $transaksiMapped = [];
        foreach ($semuaTransaksi as $item) {
            try {
                $dateTrx = Carbon::parse($item->bulan_iuran);
                $key = $dateTrx->month . '-' . $dateTrx->year;
                $transaksiMapped[$key] = $item;
            } catch (\Exception $e) {
                continue;
            }
        }

        // Looping simulasi berdasarkan durasi_bulan skema (12 atau 36)
        $daftarBulan = [];
        Carbon::setLocale('id');

        for ($i = 0; $i < $tenor; $i++) {
            $currentDate = $startDate->copy()->addMonths($i);
            $searchKey = $currentDate->month . '-' . $currentDate->year;
            
            // Jika tagihannya belum di-generate oleh admin di database, nilainya otomatis null
            $dataTagihan = $transaksiMapped[$searchKey] ?? null;

            $daftarBulan[] = [
                'nama'       => $currentDate->translatedFormat('F Y'),
                'bulan_nama' => $currentDate->translatedFormat('F'),
                'short_nama' => $currentDate->translatedFormat('M'),
                'tahun'      => $currentDate->year,
                'tagihan'    => $dataTagihan, // Jika null, di blade nanti tampilkan "Belum Terbit" atau "-"
            ];
        }

        // Hitung Statistik Progres
        $totalLunas = $semuaTransaksi->where('status_pembayaran', 'sukses')->count();
        $progresPersen = $tenor > 0 ? ($totalLunas / $tenor) * 100 : 0;
        $tahunIni = Carbon::now()->year;

        return view('peserta.jadwal.index', compact('daftarBulan', 'totalLunas', 'tenor', 'progresPersen', 'tahunIni'));
    }
}