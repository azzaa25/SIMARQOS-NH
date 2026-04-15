<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\PesertaArisan;
use App\Models\UndianArisan;
use App\Models\TransaksiPembayaran; 
use Illuminate\Support\Facades\Auth;

class DashboardPesertaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ambil data peserta beserta skema dan kelompoknya
        $peserta = PesertaArisan::with(['skemaArisan', 'kelompok'])
            ->where('id_user', $user->id_user)
            ->first();

        // Safety Check jika user belum terdaftar sebagai peserta arisan
        if (!$peserta) {
            return redirect()->route('welcome')->with('error', 'Data peserta tidak ditemukan.');
        }

        // 1. Info anggota kelompok (menghitung jumlah orang di kelompok yang sama)
        $anggotaKelompok = null;
        if ($peserta->id_kelompok) {
            $anggotaKelompok = PesertaArisan::where('id_kelompok', $peserta->id_kelompok)->get();
        }

        // 2. Info hasil undian (apakah peserta sudah menang atau belum)
        $hasilUndian = UndianArisan::where('id_pesertaarisan', $peserta->id_pesertaarisan)->first();

        // 3. Riwayat Transaksi (Hanya 5 transaksi terbaru milik peserta ini)
        $riwayatTransaksi = TransaksiPembayaran::where('id_pesertaarisan', $peserta->id_pesertaarisan)
            ->latest()
            ->take(5)
            ->get();

        // 4. Hitung Status Iuran Dinamis
        /**
         * PERBAIKAN: Menambahkan 'sukses' (huruf kecil) sesuai data di database Anda
         * agar angka "Bulan Terbayar" tidak lagi bernilai 0.
         */
        $totalBulanLunas = TransaksiPembayaran::where('id_pesertaarisan', $peserta->id_pesertaarisan)
            ->whereIn('status_pembayaran', ['success', 'settlement', 'capture', 'sukses', 'SUKSES'])
            ->count();
            
        // Mengecek transaksi yang masih pending atau belum dibayar
        $jumlahPending = TransaksiPembayaran::where('id_pesertaarisan', $peserta->id_pesertaarisan)
            ->whereIn('status_pembayaran', ['pending', 'PENDING', 'belum bayar'])
            ->count();

        // 5. Data Biaya & Durasi dari Skema untuk ditampilkan di Blade
        $biaya = $peserta->skemaArisan->nominal_iuran ?? 0;
        $durasi = $peserta->skemaArisan->durasi_bulan ?? 0;

        return view('peserta.dashboard', compact(
            'peserta', 
            'anggotaKelompok', 
            'hasilUndian', 
            'riwayatTransaksi', 
            'jumlahPending',
            'totalBulanLunas',
            'biaya',
            'durasi'
        ));
    }
}