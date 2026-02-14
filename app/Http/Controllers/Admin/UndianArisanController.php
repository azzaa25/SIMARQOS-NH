<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UndianArisan;
use App\Models\SkemaArisan;
use App\Models\PesertaArisan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UndianArisanController extends Controller
{
    public function index()
    {
        $skemas = SkemaArisan::all();
        $undians = UndianArisan::with(['peserta', 'skema'])->latest()->paginate(10);
        
        // Hitung Statistik: Menggunakan join karena 'status' ada di tabel users
        $totalPeserta = PesertaArisan::join('users', 'peserta_arisan.id_user', '=', 'users.id_user')
                        ->where('users.status', 'aktif')
                        ->count();
                        
        $selesai = UndianArisan::count();
        $menunggu = max(0, $totalPeserta - $selesai);

        return view('admin.undian.index', compact('skemas', 'undians', 'totalPeserta', 'menunggu', 'selesai'));
    }

    public function prosesUndian(Request $request)
    {
        $request->validate(['id_skema' => 'required']);
        $skema = SkemaArisan::findOrFail($request->id_skema);

        // Ambil peserta yang BELUM PERNAH menang di skema ini dan akunnya AKTIF
        // Menggunakan join untuk mengakses kolom status di tabel users
        
        $pesertaReady = PesertaArisan::join('users', 'peserta_arisan.id_user', '=', 'users.id_user')
            ->where('peserta_arisan.id_skema', $skema->id_skema)
            ->where('users.status', 'aktif') // Status diambil dari tabel users
            ->whereNotIn('peserta_arisan.id_pesertaarisan', function($q) use ($skema) {
                $q->select('id_pesertaarisan')
                  ->from('undian_arisan')
                  ->where('id_skema', $skema->id_skema);
            })
            ->select('peserta_arisan.*') // Pastikan hanya mengambil kolom milik peserta_arisan
            ->inRandomOrder()
            ->get();

        if ($pesertaReady->isEmpty()) {
            return back()->with('error', 'Semua peserta pada skema ini sudah menang atau tidak ada akun aktif.');
        }

        // Tentukan Kuota: 1 thn = Semua, 3 thn = 10 orang
        $kuota = ($skema->durasi_tahun == 1) ? $pesertaReady->count() : 10;
        $pemenang = $pesertaReady->take($kuota);

        // Hitung periode Tahun Ke- berapa sekarang
        $tahunKe = UndianArisan::where('id_skema', $skema->id_skema)
                    ->distinct('tahun_ke')
                    ->count() + 1;

        DB::beginTransaction();
        try {
            foreach ($pemenang as $index => $p) {
                UndianArisan::create([
                    'id_skema' => $skema->id_skema,
                    'id_pesertaarisan' => $p->id_pesertaarisan,
                    'tahun_ke' => $tahunKe,
                    'urutan_pemenang' => $index + 1,
                    'tanggal_undian' => Carbon::now(),
                    'status_undian' => 'pemenang'
                ]);
            }
            DB::commit();
            return back()->with('success', count($pemenang) . ' Peserta terpilih untuk Qurban Tahun Ke-' . $tahunKe);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}