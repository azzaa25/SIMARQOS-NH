<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KegiatanSosial;
use App\Models\DanaSosial;
use App\Models\KategoriSosial; // 1. Pastikan Model KategoriSosial di-import

class WelcomeController extends Controller
{
    public function index() {
        $kegiatan = KegiatanSosial::with('kategori')->latest()->take(3)->get();
        $totalDonasi = DanaSosial::whereIn('status_pembayaran', ['success', 'settlement'])->sum('nominal');
        return view('welcome', compact('kegiatan', 'totalDonasi'));
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
        
        $donatur = DanaSosial::where('id_kegiatan', $id)
                    ->whereIn('status_pembayaran', ['success', 'settlement'])
                    ->orderBy('tanggal_input', 'desc')
                    ->get();
                    
        return view('detail_agenda', compact('item', 'donatur')); 
    }
}