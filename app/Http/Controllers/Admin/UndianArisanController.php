<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UndianArisan;
use App\Models\SkemaArisan;
use App\Models\PesertaArisan;
use App\Models\KelompokArisan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UndianArisanController extends Controller
{
    /* =========================
        HALAMAN INDEX UNDIAN
    ========================== */
    public function index()
    {
        $skemas = SkemaArisan::all();
        
        // Load undian dengan relasi peserta, skema, dan kelompok agar bisa dikelompokkan di view
        $undians = UndianArisan::with(['peserta.kelompok', 'skema'])
                    ->latest()
                    ->paginate(20);
        
        // Statistik Peserta Aktif
        $totalPeserta = PesertaArisan::whereHas('user', function($q) {
                            $q->where('status', 'aktif');
                        })->count();
                        
        $selesai = UndianArisan::count();
        $menunggu = max(0, $totalPeserta - $selesai);

        return view('admin.undian.index', compact('skemas', 'undians', 'totalPeserta', 'menunggu', 'selesai'));
    }

    /* =========================
        PROSES UNDIAN LOGIC
    ========================== */
    public function prosesUndian(Request $request)
    {
        $request->validate([
            'id_skema' => 'required|exists:skema_arisan,id_skema'
        ]);

        $skema = SkemaArisan::findOrFail($request->id_skema);

        DB::beginTransaction();
        try {
            // Tentukan periode Tahun Ke- berapa sekarang
            $tahunKe = UndianArisan::where('id_skema', $skema->id_skema)
                        ->max('tahun_ke') ?? 0;
            $tahunKe++;

            $pemenangList = collect();

            if ($skema->tipe_skema === 'kelompok') {
                /* --- LOGIKA UNDIAN KELOMPOK --- */
                // Ambil 1 kelompok yang SUDAH LENGKAP anggotanya dan BELUM PERNAH MENANG
                $kelompokTerpilih = KelompokArisan::where('status_kelompok', 'lengkap')
                    ->whereHas('peserta', function($q) use ($skema) {
                        $q->where('id_skema', $skema->id_skema);
                    })
                    ->whereNotIn('id_kelompok', function($q) {
                        $q->select('peserta_arisan.id_kelompok')
                          ->from('undian_arisan')
                          ->join('peserta_arisan', 'undian_arisan.id_pesertaarisan', '=', 'peserta_arisan.id_pesertaarisan')
                          ->whereNotNull('peserta_arisan.id_kelompok');
                    })
                    ->inRandomOrder()
                    ->first();

                if (!$kelompokTerpilih) {
                    return back()->with('error', 'Tidak ada kelompok lengkap yang tersedia untuk diundi pada skema ini.');
                }

                // Semua anggota di dalam kelompok tersebut menjadi pemenang
                $pemenangList = PesertaArisan::where('id_kelompok', $kelompokTerpilih->id_kelompok)->get();
                $pesanSukses = "Kelompok " . $kelompokTerpilih->kode_kelompok . " terpilih sebagai pemenang Tahun Ke-" . $tahunKe;

            } else {
                /* --- LOGIKA UNDIAN INDIVIDU --- */
                $pesertaReady = PesertaArisan::where('id_skema', $skema->id_skema)
                    ->whereHas('user', function($q) { $q->where('status', 'aktif'); })
                    ->whereNotIn('id_pesertaarisan', function($q) use ($skema) {
                        $q->select('id_pesertaarisan')->from('undian_arisan')->where('id_skema', $skema->id_skema);
                    })
                    ->inRandomOrder()
                    ->get();

                if ($pesertaReady->isEmpty()) {
                    return back()->with('error', 'Semua peserta individu sudah menang.');
                }

                // Kuota default individu: Jika 1 thn ambil semua, jika 3 thn ambil 10
                $kuota = ($skema->durasi_tahun == 1) ? $pesertaReady->count() : 10;
                $pemenangList = $pesertaReady->take($kuota);
                $pesanSukses = count($pemenangList) . " Peserta terpilih sebagai pemenang Tahun Ke-" . $tahunKe;
            }

            // Simpan ke Tabel Undian
            foreach ($pemenangList as $index => $p) {
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
            return back()->with('success', $pesanSukses);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memproses undian: ' . $e->getMessage());
        }
    }
}