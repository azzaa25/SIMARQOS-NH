<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\UndianArisan;
use Illuminate\Http\Request;

class UndianController extends Controller
{
    public function index()
    {
        // Peserta hanya bisa MELIHAT daftar pemenang yang sudah diundi oleh admin
        // Kita urutkan dari tahun terbaru (desc) agar yang paling atas adalah pemenang terakhir
        $undians = UndianArisan::with(['peserta.kelompok', 'skema'])
                    ->orderBy('tahun_pelaksanaan', 'desc')
                    ->orderBy('urutan_pemenang', 'asc')
                    ->get();

        return view('peserta.undian.index', compact('undians'));
    }
}