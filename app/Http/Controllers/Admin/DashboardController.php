<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaArisan;
use App\Models\SkemaArisan;
use App\Models\TransaksiPembayaran;
use App\Models\KegiatanSosial;
use App\Models\UndianArisan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Dasar
        $totalPeserta = PesertaArisan::count();
        $totalSkema = SkemaArisan::count();
        
        // 2. TOTAL KAS ARISAN (Seluruh uang masuk dari awal - Status 'sukses')
        // Ini disesuaikan dengan logika di TransaksiAdminController Anda
        $totalKasArisan = TransaksiPembayaran::where('status_pembayaran', 'sukses')
            ->sum('nominal');

        // 3. Pembayaran Lunas (Jumlah transaksi sukses)
        $pembayaranLunas = TransaksiPembayaran::where('status_pembayaran', 'sukses')
            ->count();

        // 4. Kegiatan Sosial Aktif (Status 'rencana' atau 'berlangsung')
        $kegiatanAktif = KegiatanSosial::whereIn('status_kegiatan', ['rencana', 'berlangsung', 'aktif'])
            ->count();

        // 5. Transaksi Terbaru (5 Data Terakhir)
        $transaksiTerbaru = TransaksiPembayaran::with(['peserta.skemaArisan'])
            ->latest()
            ->take(5)
            ->get();

        // 6. Pemenang Arisan Terbaru
        // Mengambil data dari tabel undian_arisan yang baru saja diproses
        $pemenangTerbaru = UndianArisan::with(['skema', 'peserta'])
            ->latest('tanggal_undian')
            ->take(4) // Menampilkan 4 pemenang terakhir
            ->get();

        return view('admin.dashboard', compact(
            'totalPeserta',
            'totalSkema',
            'totalKasArisan',
            'pembayaranLunas', 
            'kegiatanAktif',
            'transaksiTerbaru',
            'pemenangTerbaru'
        ));
    }
}