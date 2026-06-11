<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KegiatanSosial;
use App\Models\DanaSosial;
use App\Models\KategoriSosial; // 1. Pastikan Model KategoriSosial di-import

class WelcomeController extends Controller
{
    public function index() {
        // Menghitung total peserta arisan yang status USER-nya aktif
        $totalPeserta = \App\Models\PesertaArisan::whereHas('user', function($query) {
            $query->where('status', 'aktif');
        })->count();
        // Ambil kegiatan yang belum selesai untuk ditampilkan di hero/slider (opsional)
        $kegiatan = KegiatanSosial::with('kategori')
                    ->where('status_kegiatan', '!=', 'selesai')
                    ->latest()
                    ->take(3)
                    ->get();

        // PERBAIKAN: Total dana terkumpul hanya dari agenda yang MASIH BERLANGSUNG
        $totalDonasi = DanaSosial::whereHas('kegiatan', function($query) {
                            $query->where('status_kegiatan', '!=', 'selesai');
                        })
                        ->where('tipe_dana', 'masuk') // Hanya uang masuk (donasi)
                        ->whereIn('status_pembayaran', ['success', 'settlement'])
                        ->sum('nominal');

        return view('welcome', compact('kegiatan', 'totalDonasi', 'totalPeserta'));
    }

    // Halaman Semua Agenda
    public function semuaAgenda() {
        // 2. Ambil data kategori untuk tombol filter
        $kategori = KategoriSosial::all(); 
        
        // 3. Gunakan paginate agar variabel $agendas->links() di Blade tidak error
        // Pastikan nama variabelnya '$agendas' agar cocok dengan Blade Anda
        $agendas = KegiatanSosial::with('kategori')->latest()->paginate(8);

        // 4. Kirim variabel $agendas dan $kategori ke view
        return view('semua_agenda', compact('agendas', 'kategori'));
    }

    // Halaman Detail
    public function detailAgenda($id) {
        $item = KegiatanSosial::with('kategori')->findOrFail($id);
        
        // Logika tambahan: Jika sudah lewat tanggalnya, pastikan status di objek tersebut adalah 'selesai'
        // (Walaupun sudah ada auto-update di index admin, ini untuk keamanan tampilan publik)
        if ($item->tanggal_kegiatan < now()->startOfDay() && $item->status_kegiatan != 'selesai') {
            $item->status_kegiatan = 'selesai';
        }

        $donatur = DanaSosial::where('id_kegiatan', $id)
                    ->where('tipe_dana', 'masuk') // Ambil hanya donasi masuk
                    ->whereIn('status_pembayaran', ['success', 'settlement'])
                    ->orderBy('tanggal_input', 'desc')
                    ->get();
                    
        return view('detail_agenda', compact('item', 'donatur')); 
    }
}