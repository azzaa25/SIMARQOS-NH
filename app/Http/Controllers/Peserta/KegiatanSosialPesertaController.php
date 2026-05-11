<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KegiatanSosial;
use App\Models\DanaSosial;
use App\Models\KategoriSosial;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KegiatanSosialPesertaController extends Controller
{
    /**
     * Tampilkan semua daftar kegiatan sosial aktif untuk peserta
     */
    public function index()
    {
        $kategori = KategoriSosial::all();
        
        // Ambil agenda yang masih aktif (belum selesai)
        $agendas = KegiatanSosial::with('kategori')
                ->latest()
                ->paginate(6);

        return view('peserta.sosial.index', compact('agendas', 'kategori'));
    }

    /**
     * Tampilkan detail kegiatan dan riwayat donatur
     */
    public function detail($id)
    {
        $item = KegiatanSosial::with('kategori')->findOrFail($id);
        
        // Logika tambahan: Jika sudah lewat tanggalnya, anggap selesai secara visual
        if ($item->tanggal_kegiatan < Carbon::now()->startOfDay() && $item->status_kegiatan != 'selesai') {
            $item->status_kegiatan = 'selesai';
        }
        
        // Riwayat donasi khusus agenda ini (Hanya yang sukses)
        $donatur = DanaSosial::where('id_kegiatan', $id)
                    ->whereIn('status_pembayaran', ['success', 'settlement', 'sukses', 'capture'])
                    ->orderBy('tanggal_input', 'desc')
                    ->get();

        return view('peserta.sosial.detail', compact('item', 'donatur'));
    }
}