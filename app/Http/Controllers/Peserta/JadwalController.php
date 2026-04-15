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
        
        // 1. Ambil Skema
        $skema = $peserta->skemaArisan; 

        if (!$skema) {
            return "Data skema tidak ditemukan. Pastikan id_skema terisi.";
        }

        $tenor = $skema->durasi_bulan; 

        // 2. Ambil semua transaksi tanpa mempedulikan urutan input (created_at)
        $semuaTransaksi = TransaksiPembayaran::where('id_pesertaarisan', $peserta->id_pesertaarisan)->get();

        // 3. LOGIKA PENTING: Cari Tanggal Mulai dari Nilai Bulan Iuran Paling Kecil
        if ($semuaTransaksi->isNotEmpty()) {
            // Kita map semua bulan_iuran ke Carbon, lalu cari yang paling minimal (paling lama)
            $startDate = $semuaTransaksi->map(function ($item) {
                return Carbon::parse($item->bulan_iuran)->startOfMonth();
            })->min();
        } else {
            $startDate = Carbon::now()->startOfMonth();
        }

        // 4. Normalisasi data transaksi ke dalam array agar gampang dicari (Key: "n-Y")
        // n = angka bulan tanpa nol (1-12), Y = tahun 4 digit
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

        // 5. Looping berdasarkan durasi_bulan skema
        $daftarBulan = [];
        Carbon::setLocale('id');

        for ($i = 0; $i < $tenor; $i++) {
            // Generate tanggal berurutan mulai dari yang paling lama
            $currentDate = $startDate->copy()->addMonths($i);
            
            $searchKey = $currentDate->month . '-' . $currentDate->year;
            
            // Cek apakah ada data di mapped transaksi
            $dataTagihan = $transaksiMapped[$searchKey] ?? null;

            $daftarBulan[] = [
                'nama'       => $currentDate->translatedFormat('F Y'),
                'bulan_nama' => $currentDate->translatedFormat('F'),
                'short_nama' => $currentDate->translatedFormat('M'),
                'tahun'      => $currentDate->year,
                'tagihan'    => $dataTagihan, 
            ];
        }

        // 6. Hitung Statistik
        $totalLunas = $semuaTransaksi->where('status_pembayaran', 'sukses')->count();
        $progresPersen = $tenor > 0 ? ($totalLunas / $tenor) * 100 : 0;
        $tahunIni = Carbon::now()->year;

        return view('peserta.jadwal.index', compact('daftarBulan', 'totalLunas', 'tenor', 'progresPersen', 'tahunIni'));
    }
}