<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaArisan;
use App\Models\SkemaArisan;
use App\Models\TransaksiPembayaran;
use App\Models\KegiatanSosial;
use App\Models\UndianArisan;
use App\Models\PengeluaranArisan; // Import model Pengeluaran
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Dasar
        $totalPeserta = PesertaArisan::count();
        $totalSkema = SkemaArisan::count();
        
        // 2. LOGIKA SISA SALDO KAS (Iuran Masuk - Pengeluaran Keluar)
        $totalIuranMasuk = TransaksiPembayaran::where('status_pembayaran', 'sukses')->sum('nominal');
        $totalPengeluaran = PengeluaranArisan::sum('nominal');
        $sisaSaldoKas = $totalIuranMasuk - $totalPengeluaran;

        // 3. Pembayaran Lunas
        $pembayaranLunas = TransaksiPembayaran::where('status_pembayaran', 'sukses')->count();

        // 4. Kegiatan Sosial Aktif
        $kegiatanAktif = KegiatanSosial::whereIn('status_kegiatan', ['rencana', 'berlangsung', 'aktif'])->count();

        // 5. Transaksi Terbaru
        $transaksiTerbaru = TransaksiPembayaran::with(['peserta.skemaArisan'])
            ->latest()
            ->take(5)
            ->get();

        // 6. Pemenang Arisan Terbaru
        $pemenangTerbaru = UndianArisan::with(['skema', 'peserta'])
            ->latest('tanggal_undian')
            ->take(4) 
            ->get();

        return view('admin.dashboard', compact(
            'totalPeserta',
            'totalSkema',
            'sisaSaldoKas', // Variabel baru
            'pembayaranLunas', 
            'kegiatanAktif',
            'transaksiTerbaru',
            'pemenangTerbaru'
        ));
    }
}