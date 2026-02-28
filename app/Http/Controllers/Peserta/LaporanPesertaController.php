<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\TransaksiPembayaran;
use App\Models\PesertaArisan;
use Illuminate\Support\Facades\Auth;

class LaporanPesertaController extends Controller
{
    public function index()
    {
        $peserta = PesertaArisan::with(['skemaArisan', 'kelompok'])
                    ->where('id_user', Auth::id())
                    ->first();

        $skema = $peserta->skemaArisan;
        $kelompok = $peserta->kelompok;

        // 1. Ambil transaksi PRIBADI (untuk tabel & card iuran saya)
        $transaksi = TransaksiPembayaran::where('id_pesertaarisan', $peserta->id_pesertaarisan)
                            ->orderBy('bulan_iuran', 'desc')
                            ->get();

        $totalDibayar = $transaksi->where('status_pembayaran', 'sukses')->sum('nominal');
        $totalPending = $transaksi->where('status_pembayaran', 'pending')->sum('nominal');

        // 2. LOGIKA TARGET & SISA KELOMPOK
        if ($skema->tipe_skema == 'kelompok' && $kelompok) {
            // Target Kelompok = Nominal Iuran Bulanan * Durasi Bulan
            // Contoh: 1.900.000 * 12 bulan = 22.800.000
            $totalTargetArisan = $skema->nominal_iuran * $skema->durasi_bulan;

            // Ambil SEMUA uang yang sudah masuk dari SEMUA anggota di kelompok ini
            $idAnggotaKelompok = PesertaArisan::where('id_kelompok', $kelompok->id_kelompok)
                                ->pluck('id_pesertaarisan');

            $totalUangMasukKelompok = TransaksiPembayaran::whereIn('id_pesertaarisan', $idAnggotaKelompok)
                                    ->where('status_pembayaran', 'sukses')
                                    ->sum('nominal');

            // Sisa yang tampil di dashboard (Kolektif)
            $sisaTagihan = $totalTargetArisan - $totalUangMasukKelompok;
        } else {
            // Jika individu (Tabungan)
            $totalTargetArisan = $skema->nominal_iuran * $skema->durasi_bulan;
            $sisaTagihan = $totalTargetArisan - $totalDibayar;
        }

        // Persentase kelunasan kelompok
        $persenLunas = ($totalTargetArisan > 0) ? (($totalTargetArisan - $sisaTagihan) / $totalTargetArisan) * 100 : 0;

        return view('peserta.laporan.index', compact(
            'skema', 'transaksi', 'totalDibayar', 
            'totalPending', 'totalTargetArisan', 'sisaTagihan', 'persenLunas'
        ));
    }
}