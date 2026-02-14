<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\PesertaArisan;
use App\Models\UndianArisan;
use Illuminate\Support\Facades\Auth;

class DashboardPesertaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        /** * Perbaikan: Nama relasi harus 'skemaArisan' sesuai yang ada di Model PesertaArisan
         */
        $peserta = PesertaArisan::with(['skemaArisan', 'kelompok'])
            ->where('id_user', $user->id_user)
            ->first();

        /** * Safety Check: Pastikan data peserta ditemukan sebelum mengambil data lainnya
         */
        if (!$peserta) {
            return redirect()->route('welcome')->with('error', 'Data peserta tidak ditemukan.');
        }

        // Ambil info anggota kelompok jika peserta memiliki id_kelompok
        $anggotaKelompok = null;
        if ($peserta->id_kelompok) {
            $anggotaKelompok = PesertaArisan::where('id_kelompok', $peserta->id_kelompok)->get();
        }

        // Ambil hasil undian jika sudah ada
        $hasilUndian = UndianArisan::where('id_pesertaarisan', $peserta->id_pesertaarisan)->first();

        return view('peserta.dashboard', compact('peserta', 'anggotaKelompok', 'hasilUndian'));
    }
}