<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransaksiPembayaran;
use App\Models\PesertaArisan;
use App\Models\SkemaArisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class TransaksiAdminController extends Controller
{
    /**
     * Tampilan Utama Dashboard Transaksi
     */
    public function index(Request $request)
    {
        $bulanFilter = $request->filled('bulan') ? (int)$request->get('bulan') : null;
        $tahunFilter = $request->filled('tahun') ? (int)$request->get('tahun') : null;
        $skemaFilter = $request->get('skema'); 

        $query = TransaksiPembayaran::with(['peserta.skemaArisan', 'peserta.kelompok']);

        if ($bulanFilter && $tahunFilter) {
            $namaBulanInggris = Carbon::createFromDate($tahunFilter, $bulanFilter, 1)->locale('en')->format('F');
            $query->where('bulan_iuran', $namaBulanInggris . ' ' . $tahunFilter);
        }

        if ($skemaFilter) {
            $query->whereHas('peserta', fn($q) => $q->where('id_skema', $skemaFilter));
        }

        $transaksi = $query->latest()->paginate(30)->withQueryString();

        // ── STATISTIK ──
        
        // 1. Total Kas Global (Tetap ada untuk melihat total uang masjid)
        $totalKas = TransaksiPembayaran::where('status_pembayaran', 'sukses')->sum('nominal');

        // 2. Buat Query Statistik yang ikut filter
        $statsQuery = TransaksiPembayaran::where('status_pembayaran', 'sukses');
        if ($bulanFilter && $tahunFilter) {
            $namaBulanInggris = Carbon::createFromDate($tahunFilter, $bulanFilter, 1)->locale('en')->format('F');
            $statsQuery->where('bulan_iuran', $namaBulanInggris . ' ' . $tahunFilter);
        }
        if ($skemaFilter) {
            $statsQuery->whereHas('peserta', fn($q) => $q->where('id_skema', $skemaFilter));
        }

        // Ambil Data Berdasarkan Filter
        $totalTunai    = (clone $statsQuery)->where('metode_pembayaran', 'Tunai')->sum('nominal');
        $totalTransfer = (clone $statsQuery)->where('metode_pembayaran', '!=', 'Tunai')->sum('nominal');
        
        // 🌟 INI TOTAL DINAMIS (Penjumlahan Tunai + Transfer periode tersebut)
        $totalPeriode  = $totalTunai + $totalTransfer;

        // Tunggakan (juga ikut skema filter jika ada)
        $tunggakanQuery = TransaksiPembayaran::where('status_pembayaran', 'pending');
        if ($skemaFilter) {
            $tunggakanQuery->whereHas('peserta', fn($q) => $q->where('id_skema', $skemaFilter));
        }
        $tunggakan = $tunggakanQuery->get();

        return view('admin.transaksi.index', compact(
            'transaksi', 'tunggakan', 'totalKas', 'totalPeriode', 'totalTunai', 'totalTransfer'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // CALLBACK MIDTRANS
    // ──────────────────────────────────────────────────────────────
    public function callback(Request $request)
    {
        $serverKey = config('services.midtrans.serverKey');
        $hashed = hash('sha512',
            $request->order_id . $request->status_code . $request->gross_amount . $serverKey
        );

        if ($hashed != $request->signature_key) {
            return response()->json(['status' => 'invalid signature'], 403);
        }

        $orderId = $request->order_id;
        $status  = $request->transaction_status;

        if (str_starts_with($orderId, 'INV')) {
            if (in_array($status, ['capture', 'settlement'])) {
                TransaksiPembayaran::where('order_id', $orderId)->first()?->update([
                    'status_pembayaran' => 'sukses',
                    'metode_pembayaran' => $request->payment_type,
                ]);
            }
        } elseif (str_starts_with($orderId, 'DONASI')) {
            if (in_array($status, ['capture', 'settlement'])) {
                try {
                    $metadata = is_string($request->metadata)
                        ? json_decode($request->metadata, true)
                        : $request->metadata;

                    DB::table('dana_sosial')->updateOrInsert(
                        ['order_id' => $orderId],
                        [
                            'nama_donatur'         => $metadata['nama_donatur'] ?? 'Hamba Allah',
                            'id_kegiatan'          => $metadata['id_kegiatan'] ?? null,
                            'tipe_dana'            => 'masuk',
                            'nominal'              => $request->gross_amount,
                            'metode_pembayaran'    => $request->payment_type,
                            'status_pembayaran'    => 'success',
                            'keterangan_transaksi' => $metadata['keterangan'] ?? 'Donasi Online',
                            'tanggal_input'        => now(),
                        ]
                    );
                    \Log::info("Callback Donasi Berhasil: $orderId");
                } catch (\Exception $e) {
                    \Log::error('Gagal simpan donasi: ' . $e->getMessage());
                }
            }
        }

        return response()->json(['status' => 'OK']);
    }

    // ──────────────────────────────────────────────────────────────
    // GENERATE TAGIHAN BULANAN (ADAPTIF TERHADAP LOGIKA BACK-COUNTING)
    // ──────────────────────────────────────────────────────────────
    public function generateTagihan(Request $request = null)
    {
        try {
            // Ambil semua peserta dengan user yang berstatus aktif
            $pesertas = PesertaArisan::whereHas('user', fn($q) => $q->where('status', 'aktif'))
                ->with(['skemaArisan', 'undian', 'transaksi'])
                ->get();

            $bulanIni = Carbon::now()->translatedFormat('F Y');
            $count = 0;
            $daftarNama = [];

            // Peta Bulan Idul Adha Konvensional
            $petaIdulAdha = [
                2026 => 5, 2027 => 5, 2028 => 4, 2029 => 4, 2030 => 3
            ];

            foreach ($pesertas as $p) {
                if (!$p->skemaArisan || !$p->tahun_periode) continue;

                $tenor = (int)$p->skemaArisan->durasi_bulan; // 12 atau 36
                
                // 1. Hitung rentang iuran valid berdasarkan tahun_periode peserta
                $tahunTarget = (int)$p->tahun_periode;
                $bulanIdulAdha = $petaIdulAdha[$tahunTarget] ?? 5;
                $bulanLunas = $bulanIdulAdha - 1;

                $tanggalJatuhTempoAkhir = Carbon::create($tahunTarget, $bulanLunas, 1);
                $tanggalMulaiTagihan = $tanggalJatuhTempoAkhir->copy()->subMonths($tenor - 1);
                $tanggalSelesaiTagihan = $tanggalJatuhTempoAkhir->copy();

                // 2. Batasan Proteksi: Jika bulan berjalan saat ini di luar timeline iuran peserta, LEWATI
                if (Carbon::now()->startOfMonth()->lessThan($tanggalMulaiTagihan->startOfMonth()) || 
                    Carbon::now()->startOfMonth()->greaterThan($tanggalSelesaiTagihan->startOfMonth())) {
                    
                    // Kondisi Tambahan: Jika waktu iurannya sudah lewat dan dia lunas + menang undian, nonaktifkan akun
                    $totalLunas = $p->transaksi->where('status_pembayaran', 'sukses')->count();
                    if ($totalLunas >= $tenor && $p->undian && $p->user) {
                        $p->user->update(['status' => 'nonaktif']);
                    }
                    continue;
                }

                // 3. Cek total baris tagihan yang sudah diterbitkan untuk peserta ini
                $totalTagihanTerbit = $p->transaksi->count();
                if ($totalTagihanTerbit >= $tenor) {
                    continue; // Stop generate jika kuota baris tagihan skema sudah terpenuhi
                }

                // 4. Cek apakah tagihan bulan ini sudah pernah diterbitkan sebelumnya
                $exists = TransaksiPembayaran::where('id_pesertaarisan', $p->id_pesertaarisan)
                    ->where('bulan_iuran', $bulanIni)
                    ->exists();

                if (!$exists) {
                    // Hitung nominal (jika kelompok, nominal iuran dibagi 7)
                    $nominalFinal = ($p->id_kelompok != null)
                        ? ceil($p->skemaArisan->nominal_iuran / 7)
                        : $p->skemaArisan->nominal_iuran;

                    TransaksiPembayaran::create([
                        'id_pesertaarisan' => $p->id_pesertaarisan,
                        'order_id'         => 'INV-' . strtoupper(bin2hex(random_bytes(3))) . '-' . $p->id_pesertaarisan,
                        'nominal'          => $nominalFinal,
                        'bulan_iuran'      => $bulanIni,
                        'status_pembayaran'=> 'pending',
                    ]);

                    $daftarNama[] = $p->nama;
                    $count++;
                }
            }

            // ── KIRIM NOTIFIKASI WA GRUP JIKA ADA TAGIHAN BARU TERBIT ──
            if ($count > 0) {
                $pesanWA  = "🔔 *PEMBERITAHUAN TAGIHAN ARISAN*\n";
                $pesanWA .= "Periode: *$bulanIni*\n\n";
                $pesanWA .= "Assalamu'alaikum Wr. Wb.\nTagihan arisan bulan ini telah diterbitkan untuk:\n\n";
                foreach (array_slice($daftarNama, 0, 10) as $i => $nama) {
                    $pesanWA .= ($i + 1) . ". $nama\n";
                }
                if (count($daftarNama) > 10) {
                    $pesanWA .= "...dan " . (count($daftarNama) - 10) . " peserta lainnya.\n";
                }
                $pesanWA .= "\nSilakan bayar melalui aplikasi atau setor tunai ke pengurus.\nTerima kasih. 🙏";

                try {
                    Http::post(env('WA_BOT_URL'), [
                        'target' => env('WA_GROUP_ID'),
                        'message' => $pesanWA,
                    ]);
                } catch (\Exception $waEx) {
                    \Log::error("Gagal kirim WA Tagihan: " . $waEx->getMessage());
                }
            }

            if (request()->wantsJson()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => "Berhasil menerbitkan $count tagihan baru untuk bulan ini.",
                ]);
            }

            return back()->with('success', "Berhasil memproses iuran bulanan. $count tagihan baru diterbitkan.");

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
    public function verifikasiManual($id)
    {
        $trx = TransaksiPembayaran::findOrFail($id);
        $trx->update([
            'status_pembayaran' => 'sukses',
            'metode_pembayaran' => 'Tunai'
        ]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi secara manual.');
    }

    // ──────────────────────────────────────────────────────────────
    // EXPORT PDF
    // ──────────────────────────────────────────────────────────────
    public function exportPDF(Request $request)
    {
        $bulanFilter = $request->get('bulan');
        $tahunFilter = $request->get('tahun');
        $skemaFilter = $request->get('skema'); // Ambil filter skema

        // Query dasar: Hanya yang sukses
        $query = TransaksiPembayaran::with(['peserta.kelompok', 'peserta.skemaArisan'])
                    ->where('status_pembayaran', 'sukses');

        $periodeTeks = "Semua Periode";
        
        // 1. Filter Bulan & Tahun
        if ($bulanFilter && $tahunFilter) {
            $namaBulanInggris = \Carbon\Carbon::createFromDate((int)$tahunFilter, (int)$bulanFilter, 1)->locale('en')->format('F');
            $query->where('bulan_iuran', $namaBulanInggris . ' ' . $tahunFilter);
            
            $periodeTeks = \Carbon\Carbon::createFromDate((int)$tahunFilter, (int)$bulanFilter, 1)->translatedFormat('F Y');
        }

        // 2. Filter Skema (Tambahan Baru)
        if ($skemaFilter) {
            $skema = \App\Models\SkemaArisan::find($skemaFilter);
            if ($skema) {
                $query->whereHas('peserta', function($q) use ($skemaFilter) {
                    $q->where('id_skema', $skemaFilter);
                });
                // Tambahkan keterangan skema ke judul laporan
                $periodeTeks .= " - Skema: " . $skema->nama_skema;
            }
        }

        $allData      = $query->latest()->get();
        $dataKelompok = $allData->whereNotNull('peserta.id_kelompok')->groupBy('peserta.id_kelompok');
        $dataIndividu = $allData->whereNull('peserta.id_kelompok');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.transaksi.pdf', compact('dataKelompok', 'dataIndividu', 'periodeTeks'));
        
        $fileName = 'laporan-arisan-' . \Str::slug($periodeTeks) . '.pdf';
        return $pdf->download($fileName);
    }
    public function kirimTagihanWaPersonal($id)
    {
        try {
            // Cari data transaksi tunggakan spesifik beserta profil pesertanya
            $item = TransaksiPembayaran::with(['peserta'])->findOrFail($id);
            $peserta = $item->peserta;

            if ($item->status_pembayaran !== 'pending') {
                return back()->with('error', 'Transaksi ini sudah lunas.');
            }

            if (!$peserta || !$peserta->no_hp) {
                return back()->with('error', 'Nomor HP peserta tidak ditemukan.');
            }

            Carbon::setLocale('id');

            // 🛠️ PERBAIKAN 1: Ambil data bulan iuran dari database dan konversi ke Bahasa Indonesia
            try {
                $bulanIndo = Carbon::createFromFormat('F Y', $item->bulan_iuran)
                                ->locale('id')
                                ->translatedFormat('F Y');
            } catch (\Exception $e) {
                $bulanIndo = $item->bulan_iuran; // Fallback jika format string di DB berbeda
            }

            $rawNoHp = $peserta->no_hp; 

            // 🛠️ PERBAIKAN 2: Mengubah 'continue' menjadi 'return back' karena ini bukan di dalam perulangan loop
            if (!$rawNoHp) {
                return back()->with('error', 'Format nomor HP kosong.');
            }

            // Mengubah awalan 08xxx menjadi 628xxx agar dikenali server Node.js & WhatsApp
            $noHp = preg_replace('/^0/', '62', trim($rawNoHp));

            // Nominal iuran diformat rupiah
            $nominal = number_format($item->nominal, 0, ',', '.');

            // Susun Pesan Personal
            $pesanWA  = "Assalamu'alaikum Wr. Wb. Bapak/Ibu *$peserta->nama*,\n\n";
            $pesanWA .= "Kami dari *Pengurus Masjid Nurul Huda* menginfokan tagihan iuran arisan qurban yang belum lunas:\n\n";
            $pesanWA .= "📌 Periode: *$bulanIndo*\n";
            $pesanWA .= "💰 Nominal: *Rp $nominal*\n";
            $pesanWA .= "Pembayaran dapat dilakukan melalui aplikasi atau setor tunai langsung ke pengurus.\n\n";
            $pesanWA .= "Terima kasih atas partisipasinya. Semoga menjadi amal jariyah. 🙏";

            // Kirim data ke Node.js Express Server
            try {
                Http::post(env('WA_BOT_URL'), [
                    'target'  => $noHp,      // Mengirim nomor berawalan 62 (contoh: 6285336391316)
                    'message' => $pesanWA,
                ]);
                return back()->with('success', "Notifikasi tagihan berhasil dikirim ke WhatsApp {$peserta->nama}!");
            } catch (\Exception $waEx) {
                \Log::error("Gagal kirim WA ke $noHp ($peserta->nama): " . $waEx->getMessage());
                return back()->with('error', 'Gagal terhubung ke Server Bot Node.js.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }
}