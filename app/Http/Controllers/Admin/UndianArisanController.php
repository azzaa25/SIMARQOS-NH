<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UndianArisan;
use App\Models\SkemaArisan;
use App\Models\PesertaArisan;
use App\Models\KelompokArisan;
use App\Models\TransaksiPembayaran;
use App\Models\PengeluaranArisan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UndianArisanController extends Controller
{
    /**
     * Menampilkan Halaman Utama Undian
     */
    public function index()
    {
        $skemas = SkemaArisan::all();
        
        $undians = UndianArisan::with(['peserta.kelompok', 'skema'])
                    ->orderBy('tahun_pelaksanaan', 'desc')
                    ->orderBy('urutan_pemenang', 'asc')
                    ->paginate(20);
        
        // Statistik untuk Dashboard Undian
        $totalPeserta = PesertaArisan::whereHas('user', function($q) {
                            $q->where('status', 'aktif');
                        })->count();
                        
        $selesai = UndianArisan::count();
        $menunggu = max(0, $totalPeserta - $selesai);

        // Menghitung Saldo Kas Riil (Total Masuk - Total Keluar)
        $totalMasuk = TransaksiPembayaran::where('status_pembayaran', 'sukses')->sum('nominal');
        $totalKeluar = PengeluaranArisan::sum('nominal');
        $saldoKasGlobal = $totalMasuk - $totalKeluar;

        return view('admin.undian.index', compact(
            'skemas', 'undians', 'totalPeserta', 'menunggu', 'selesai', 'saldoKasGlobal'
        ));
    }

    /**
     * Proses Algoritma Undian & Otomatisasi Pengeluaran
     */
    public function prosesUndian(Request $request)
    {
        $request->validate([
            'id_skema' => 'required|exists:skema_arisan,id_skema'
        ]);

        $skema = SkemaArisan::findOrFail($request->id_skema);
        $tahunRiil = date('Y');
        $nominalTargetPerOrang = $skema->nominal_iuran * $skema->durasi_bulan;

        DB::beginTransaction();
        try {
            $pemenangList = collect();

            // ============================================================
            // 1. SKEMA 1 TAHUN (WAJIB LUNAS)
            // ============================================================
            if ($skema->durasi_bulan <= 12) {
                $peserta = PesertaArisan::where('id_skema', $skema->id_skema)
                    ->whereHas('user', fn($q) => $q->where('status', 'aktif'))
                    ->get();

                $pemenangList = $peserta->filter(function ($p) use ($nominalTargetPerOrang) {
                    $totalBayar = TransaksiPembayaran::where('id_pesertaarisan', $p->id_pesertaarisan)
                        ->where('status_pembayaran', 'sukses')
                        ->sum('nominal');
                    return $totalBayar >= $nominalTargetPerOrang;
                });

                if ($pemenangList->isEmpty()) {
                    $listBelumLunas = $peserta->map(function($p) use ($nominalTargetPerOrang) {
                        $total = TransaksiPembayaran::where('id_pesertaarisan', $p->id_pesertaarisan)
                                ->where('status_pembayaran', 'sukses')->sum('nominal');
                        return $p->nama . " (Rp " . number_format($total) . ")";
                    })->take(3);

                    return back()->with('error_pembayaran', [
                        'title' => 'Gagal Mengundi!',
                        'message' => 'Tidak ada peserta yang lunas untuk skema ini.',
                        'detail' => $listBelumLunas->implode(', ')
                    ]);
                }
                $pesanSukses = "Berhasil merealisasikan " . $pemenangList->count() . " peserta.";
            } 
            // ============================================================
            // 2. SKEMA 3 TAHUN (KELOMPOK)
            // ============================================================
            else {
                $totalPesertaSkema = PesertaArisan::where('id_skema', $skema->id_skema)->count();
                $durasiTahun = $skema->durasi_bulan / 12;
                $jatahPerTahun = ceil($totalPesertaSkema / $durasiTahun);

                $sudahMenangTahunIni = UndianArisan::where('id_skema', $skema->id_skema)
                    ->where('tahun_pelaksanaan', $tahunRiil)
                    ->count();

                if ($sudahMenangTahunIni >= $jatahPerTahun) {
                    return back()->with('error', "Kuota pemenang tahun $tahunRiil untuk skema ini sudah terpenuhi.");
                }

                $kelompokPernahMenang = UndianArisan::where('undian_arisan.id_skema', $skema->id_skema)
                    ->join('peserta_arisan', 'undian_arisan.id_pesertaarisan', '=', 'peserta_arisan.id_pesertaarisan')
                    ->whereNotNull('peserta_arisan.id_kelompok')
                    ->pluck('peserta_arisan.id_kelompok')
                    ->unique();

                $kelompokTerpilih = KelompokArisan::whereHas('anggota', function ($q) use ($skema) {
                        $q->where('id_skema', $skema->id_skema);
                    })
                    ->whereNotIn('id_kelompok', $kelompokPernahMenang)
                    ->inRandomOrder()
                    ->first();

                if (!$kelompokTerpilih) {
                    return back()->with('error', 'Semua kelompok dalam skema ini sudah pernah memenangkan undian.');
                }

                $pemenangList = PesertaArisan::where('id_kelompok', $kelompokTerpilih->id_kelompok)
                    ->where('id_skema', $skema->id_skema)
                    ->get();

                $pesanSukses = "Kelompok {$kelompokTerpilih->nama_kelompok} terpilih sebagai pemenang!";
            }

            // ============================================================
            // SIMPAN DATA & HITUNG NOMINAL REALISASI
            // ============================================================
            $jumlahAnggotaMenang = $pemenangList->count();
            
            // Perbaikan: Jika Kelompok (3 Tahun), Nominal iuran dibagi jumlah anggota
            // Jika Individu (1 Tahun), Nominal iuran tetap (Target Full)
            $nominalPerOrang = ($skema->durasi_bulan > 12) 
                                ? ($nominalTargetPerOrang / $jumlahAnggotaMenang) 
                                : $nominalTargetPerOrang;

            foreach ($pemenangList as $p) {
                $undian = UndianArisan::create([
                    'id_skema'          => $skema->id_skema,
                    'id_pesertaarisan'  => $p->id_pesertaarisan,
                    'tahun_pelaksanaan' => $tahunRiil,
                    'urutan_pemenang'   => UndianArisan::where('id_skema', $skema->id_skema)->count() + 1,
                    'tanggal_undian'    => now(),
                    'status_undian'     => 'pemenang'
                ]);

                PengeluaranArisan::create([
                    'id_undian'           => $undian->id_undian,
                    'order_id'            => 'OUT-' . strtoupper(bin2hex(random_bytes(3))),
                    'nominal'             => $nominalPerOrang,
                    'keterangan'          => 'Realisasi Qurban: ' . $p->nama . ($skema->durasi_bulan > 12 ? ' (Anggota Kelompok)' : ''),
                    'tanggal_pengeluaran' => now()
                ]);
            }

            DB::commit();
            return back()->with('success', $pesanSukses ?? 'Undian berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}