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
use Illuminate\Support\Facades\Http;

class UndianArisanController extends Controller
{
    /**
     * Menampilkan Halaman Utama Undian
     */
    public function index(Request $request)
    {
        $skemas = SkemaArisan::all();
        
        // 1. Pagination Tab Pemenang
        $undians = UndianArisan::with(['peserta.kelompok', 'skema'])
                ->orderBy('tahun_pelaksanaan', 'desc')
                ->orderBy('id_skema', 'asc')
                ->orderBy('urutan_pemenang', 'asc')
                ->paginate(20, ['*'], 'page_pemenang');
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

        $sudahMenangIds = UndianArisan::pluck('id_pesertaarisan')->toArray();

        // 2. Query Tab Antrean Peserta Aktif (Server-Side Filter)
        $queryAntrean = PesertaArisan::with(['kelompok', 'skemaArisan'])
            ->whereHas('user', function($q){
                $q->where('status', 'aktif');
            })
            ->whereNotIn('id_pesertaarisan', $sudahMenangIds);

        // Proses Filter Skema jika ada request masuk dari dropdown select
        if ($request->filled('filter_skema') && $request->filter_skema !== 'all') {
            $queryAntrean->where('id_skema', str_replace('skema-', '', $request->filter_skema));
        }

        // Ubah menjadi Paginate dan pastikan Kelompok berada di atas sebelum Perorangan
        $antreanPaginator = $queryAntrean
            ->withCount(['transaksi as total_iuran_sukses' => function($query) {
                $query->where('status_pembayaran', 'sukses');
            }])
            ->orderByRaw("CASE WHEN id_kelompok IS NOT NULL THEN 0 ELSE 1 END")
            ->orderBy('id_kelompok', 'asc')
            ->paginate(10, ['*'], 'page_antrean') // Disamakan dengan tab pemenang menggunakan penamaan manual
            ->appends($request->all()); // Menjaga link parameter agar page_pemenang & filter_skema tidak hilang

        return view('admin.undian.index', compact(
            'skemas', 'undians', 'totalPeserta', 'menunggu', 'selesai', 'saldoKasGlobal', 'antreanPaginator'
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
            $namaKelompok = null;

            // ============================================================
            // 1. JALUR 1 TAHUN (POKOKNYA LUNAS = MENANG SEMUA)
            // ============================================================
            if ($skema->durasi_bulan <= 12) {
                $peserta = PesertaArisan::where('id_skema', $skema->id_skema)
                    ->whereHas('user', fn($q) => $q->where('status', 'aktif'))
                    ->get();

                $pemenangList = $peserta->filter(function ($p) use ($nominalTargetPerOrang) {
                    $pernahMenang = UndianArisan::where('id_pesertaarisan', $p->id_pesertaarisan)->exists();
                    $totalBayar = TransaksiPembayaran::where('id_pesertaarisan', $p->id_pesertaarisan)
                        ->where('status_pembayaran', 'sukses')->sum('nominal');
                    
                    return !$pernahMenang && $totalBayar >= $nominalTargetPerOrang;
                });

                if ($pemenangList->isEmpty()) {
                    return back()->with('error', 'Tidak ada peserta lunas yang tersedia.');
                }
                $pesanSukses = "Berhasil merealisasikan " . $pemenangList->count() . " peserta.";
            } 
            // ============================================================
            // 2. JALUR 3 TAHUN (LOGIKA KELOMPOK / PERORANGAN ACAK)
            // ============================================================
            else {
                $totalPeserta = PesertaArisan::where('id_skema', $skema->id_skema)->count();
                $durasiTahun = $skema->durasi_bulan / 12;
                $jatahPerTahun = ceil($totalPeserta / $durasiTahun);

                $sudahMenangTahunIni = UndianArisan::where('id_skema', $skema->id_skema)
                    ->where('tahun_pelaksanaan', $tahunRiil)
                    ->count();

                if ($sudahMenangTahunIni >= $jatahPerTahun) {
                    return back()->with('error', "Kuota pemenang tahun $tahunRiil sudah terpenuhi.");
                }

                // Cek apakah ada kelompok di skema ini
                $adaKelompok = PesertaArisan::where('id_skema', $skema->id_skema)->whereNotNull('id_kelompok')->exists();

                if ($adaKelompok) {
                    // --- LOGIKA KELOMPOK ---
                    $kelompokPernahMenang = UndianArisan::where('undian_arisan.id_skema', $skema->id_skema)
                        ->join('peserta_arisan', 'undian_arisan.id_pesertaarisan', '=', 'peserta_arisan.id_pesertaarisan')
                        ->whereNotNull('peserta_arisan.id_kelompok')
                        ->pluck('peserta_arisan.id_kelompok')->unique();

                    $kelompokTerpilih = KelompokArisan::whereHas('anggota', fn($q) => $q->where('id_skema', $skema->id_skema))
                        ->whereNotIn('id_kelompok', $kelompokPernahMenang)
                        ->inRandomOrder()->first();

                    if (!$kelompokTerpilih) return back()->with('error', 'Semua kelompok sudah menang.');

                    $pemenangList = PesertaArisan::where('id_kelompok', $kelompokTerpilih->id_kelompok)
                        ->where('id_skema', $skema->id_skema)->get();
                    $namaKelompok = $kelompokTerpilih->nama_kelompok;
                    $pesanSukses = "Kelompok $namaKelompok terpilih sebagai pemenang!";
                } else {
                    // --- LOGIKA PERORANGAN (3 TAHUN) ---
                    $sisaKuota = $jatahPerTahun - $sudahMenangTahunIni;
                    $pemenangList = PesertaArisan::where('id_skema', $skema->id_skema)
                        ->whereDoesntHave('undian') 
                        ->inRandomOrder()->take($sisaKuota)->get();

                    if ($pemenangList->isEmpty()) return back()->with('error', 'Semua peserta sudah menang.');
                    $pesanSukses = $pemenangList->count() . " Pemenang perorangan berhasil diundi.";
                }
            }

            // ============================================================
            // SIMPAN DATA & HITUNG NOMINAL
            // ============================================================
            foreach ($pemenangList as $index => $p) { // Tambahkan $index di sini
                // Jika ada kelompok, nominal dibagi. Jika tidak, nominal full.
                $nominalRealisasi = (!is_null($p->id_kelompok)) 
                    ? ($nominalTargetPerOrang / $pemenangList->count()) 
                    : $nominalTargetPerOrang;

                $undian = UndianArisan::create([
                    'id_skema'          => $skema->id_skema,
                    'id_pesertaarisan'  => $p->id_pesertaarisan,
                    'tahun_pelaksanaan' => $tahunRiil,
                    'urutan_pemenang'   => $index + 1, 
                    'tanggal_undian'    => now(),
                    'status_undian'     => 'pemenang'
                ]);

                PengeluaranArisan::create([
                    'id_undian'           => $undian->id_undian,
                    'order_id'            => 'OUT-' . strtoupper(bin2hex(random_bytes(3))),
                    'nominal'             => $nominalRealisasi,
                    'keterangan'          => 'Realisasi Qurban: ' . $p->nama . (!is_null($p->id_kelompok) ? ' (Anggota Kelompok)' : ''),
                    'tanggal_pengeluaran' => now()
                ]);
            }

            DB::commit();

            // --- KIRIM NOTIFIKASI WHATSAPP ---
            $this->kirimNotifPemenang($pemenangList, $skema, $tahunRiil, $namaKelompok);

            return back()->with('success', $pesanSukses);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Fungsi Private untuk isi pesan WhatsApp
     */
    private function kirimNotifPemenang($pemenangList, $skema, $tahun, $namaKelompok)
    {
        $pesanWA = "🎉 *PENGUMUMAN PEMENANG QURBAN " . $tahun . "* 🎉\n";
        $pesanWA .= "--------------------------------------------------\n\n";
        $pesanWA .= "Alhamdulillah, proses pengundian untuk skema *" . $skema->nama_skema . "* telah selesai dilakukan.\n\n";
        
        // --- LOGIKA PESAN DINAMIS ---
        if ($namaKelompok) {
            // Jika Jalur Kelompok
            $pesanWA .= "Selamat kepada *KELOMPOK: " . $namaKelompok . "*\n";
            $pesanWA .= "Daftar Anggota:\n";
        } else {
            // Jika Jalur Perorangan
            $pesanWA .= "Selamat kepada Bapak/Ibu pemenang tahun ini:\n";
        }

        foreach ($pemenangList as $index => $p) {
            $pesanWA .= ($index + 1) . ". *" . $p->nama . "*\n";
        }
        // ----------------------------

        $pesanWA .= "\nSemoga ibadahnya berkah dan menjadi amal jariyah bagi kita semua. Amin.\n\n";
        $pesanWA .= "_Pesan otomatis dari Sistem Masjid Nurul Huda_";

        try {
            Http::post(env('WA_BOT_URL'), [
                'groupId' => env('WA_GROUP_ID'),
                'message' => $pesanWA
            ]);
        } catch (\Exception $e) {
            \Log::error("Kirim WA Pemenang Gagal: " . $e->getMessage());
        }
    }
}