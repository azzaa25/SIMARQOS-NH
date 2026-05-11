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
        // 1. Ambil input filter dari dropdown
        $bulanFilter = $request->get('bulan');
        $tahunFilter = $request->get('tahun');

        $query = TransaksiPembayaran::with(['peserta.skemaArisan', 'peserta.kelompok']);

        // 2. LOGIKA FILTER: Mencocokkan dengan kolom 'bulan_iuran' (Bahasa Inggris)
        if ($bulanFilter && $tahunFilter) {
            // Pastikan $bulanFilter adalah integer
            $bulanAngka = (int)$bulanFilter; 
            
            // Paksa locale 'en' agar format 'F' menghasilkan "January", "February", dst.
            $namaBulanInggris = Carbon::createFromDate($tahunFilter, $bulanAngka, 1)
                                ->locale('en')
                                ->format('F');
                                
            $stringCari = $namaBulanInggris . ' ' . $tahunFilter; // Hasil: "January 2026"

            $query->where('bulan_iuran', $stringCari);
        }

        $transaksi = $query->latest()->get();

        // ================= TAMBAHAN FILTER UNTUK STATISTIK =================
        if ($bulanFilter && $tahunFilter) {

            $start = Carbon::createFromDate($tahunFilter, (int)$bulanFilter, 1)
                        ->subMonths(5)
                        ->startOfMonth();

            $end = Carbon::createFromDate($tahunFilter, (int)$bulanFilter, 1)
                        ->endOfMonth();

        } else {

            $start = Carbon::now()->subMonths(5)->startOfMonth();
            $end   = Carbon::now()->endOfMonth();
        }

        // Query khusus statistik (tidak ganggu query utama)
        $statistikQuery = TransaksiPembayaran::where('status_pembayaran', 'sukses')
            ->whereBetween('created_at', [$start, $end]);

        // --- Statistik tetap Global agar saldo ID 8-52 tetap terhitung ---
        $totalKas = TransaksiPembayaran::where('status_pembayaran', 'sukses')->sum('nominal');
        
        $totalTunai = TransaksiPembayaran::where('status_pembayaran', 'sukses')
            ->where('metode_pembayaran', 'Tunai')
            ->sum('nominal');

        $totalTransfer = TransaksiPembayaran::where('status_pembayaran', 'sukses')
            ->where('metode_pembayaran', '!=', 'Tunai')
            ->sum('nominal');

        $rataRata = $transaksi->where('status_pembayaran', 'sukses')->avg('nominal') ?? 0;

        $tunggakan = TransaksiPembayaran::with(['peserta.skemaArisan', 'peserta.kelompok'])
            ->where('status_pembayaran', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        // Data lainnya...
        $totalPeserta = PesertaArisan::whereHas('user', function ($q) {
            $q->whereIn('status', ['aktif', 'nonaktif']);
        })->count();

        $metodeFavorit = TransaksiPembayaran::where('status_pembayaran', 'sukses')
            ->select('metode_pembayaran', DB::raw('count(*) as total'))
            ->groupBy('metode_pembayaran')
            ->orderBy('total', 'desc')
            ->first();

        // ================= GRAFIK BERDASARKAN FILTER =================
        $grafikBulanan = $statistikQuery
            ->select(
                DB::raw('SUM(nominal) as total'),
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as bulan')
            )
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->pluck('total', 'bulan');

        $periode = collect();
        $startLoop = $start->copy();

        for ($i = 0; $i < 6; $i++) {
            $key = $startLoop->format('Y-m');
            $periode[$key] = $grafikBulanan[$key] ?? 0;
            $startLoop->addMonth();
        }

        $grafikBulanan = $periode;

        return view('admin.transaksi.index', compact(
            'transaksi', 'tunggakan', 'totalPeserta', 'totalKas', 
            'totalTunai', 'totalTransfer', 'rataRata', 'metodeFavorit', 'grafikBulanan'
        ));
    }
    public function callback(Request $request)
    {
        $serverKey = config('services.midtrans.serverKey');

        $hashed = hash(
            "sha512",
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        if ($hashed != $request->signature_key) {
            return response()->json(['status' => 'invalid signature'], 403);
        }

        $orderId = $request->order_id;
        $status = $request->transaction_status;

        /*
        ======================================
        TRANSAKSI ARISAN (Prefix: INV)
        ======================================
        */
        if (str_starts_with($orderId, 'INV')) {
            if ($status == 'capture' || $status == 'settlement') {
                $transaksi = TransaksiPembayaran::where('order_id', $orderId)->first();
                if ($transaksi) {
                    $transaksi->update([
                        'status_pembayaran' => 'sukses',
                        'metode_pembayaran' => $request->payment_type,
                    ]);
                }
            }
        }

        /*
        ======================================
        TRANSAKSI DONASI (Prefix: DONASI)
        ======================================
        */
        elseif (str_starts_with($orderId, 'DONASI')) {
            if ($status == 'capture' || $status == 'settlement') {
                try {
                    // Ambil Metadata dari Midtrans
                    $metadata = $request->metadata;
                    
                    // Jika metadata berbentuk string JSON (sering terjadi di Midtrans), kita decode
                    if (is_string($metadata)) {
                        $metadata = json_decode($metadata, true);
                    }

                    $namaDonatur = $metadata['nama_donatur'] ?? 'Hamba Allah';
                    $idKegiatan  = $metadata['id_kegiatan'] ?? null;
                    $keterangan  = $metadata['keterangan'] ?? 'Donasi Online';

                    // Simpan ke tabel dana_sosial
                    DB::table('dana_sosial')->updateOrInsert(
                        ['order_id' => $orderId],
                        [
                            'nama_donatur'         => $namaDonatur,
                            'id_kegiatan'          => $idKegiatan,
                            'tipe_dana'            => 'masuk',
                            'nominal'              => $request->gross_amount,
                            'metode_pembayaran'    => $request->payment_type,
                            'status_pembayaran'    => 'success',
                            'keterangan_transaksi' => $keterangan,
                            'tanggal_input'        => now(),
                        ]
                    );

                    \Log::info("Callback Donasi Berhasil: " . $orderId);

                } catch (\Exception $e) {
                    \Log::error('Gagal simpan donasi di Callback: ' . $e->getMessage());
                }
            }
        }

        return response()->json(['status' => 'OK']);
    }

    /**
     * Fungsi Inti: Generate Tagihan (Manual & Otomatis)
     */
    public function generateTagihan(Request $request = null)
    {
        try {
            $pesertas = PesertaArisan::whereHas('user', function($q) {
                    $q->where('status', 'aktif');
                })
                ->with(['skemaArisan', 'undian', 'transaksi' => function($q) {
                    // Filter ini tetap dipakai untuk keperluan "Auto-Nonaktif" (hanya yang sudah bayar lunas)
                    $q->where('status_pembayaran', 'sukses');
                }])->get();

            $bulanIni = Carbon::now()->translatedFormat('F Y');
            $count = 0;
            $daftarNama = []; 

            foreach ($pesertas as $p) {
                if (!$p->skemaArisan) continue;

                // --- PERBAIKAN LOGIKA TENOR ---
                
                // 1. Hitung TOTAL RECORD tagihan yang pernah dibuat (Sukses, Pending, Gagal semuanya dihitung)
                $totalTagihanPernahDibuat = \App\Models\TransaksiPembayaran::where('id_pesertaarisan', $p->id_pesertaarisan)->count();
                $tenorSkema = $p->skemaArisan->durasi_bulan; // Misal: 12
                // 2. Jika jumlah tagihan yang ada sudah mencapai atau melebihi tenor skema
                if ($totalTagihanPernahDibuat >= $tenorSkema) {
                    // Cek apakah dia benar-benar sudah lunas (untuk mematikan status user)
                    $jumlahCicilanLunas = $p->transaksi->count(); // Mengambil data 'sukses' dari eager loading
                    if ($jumlahCicilanLunas >= $tenorSkema && $p->undian) {
                        // Jika sudah lunas 12x DAN sudah menang, nonaktifkan agar tidak masuk query bulan depan
                        if ($p->user) {
                            $p->user->update(['status' => 'nonaktif']);
                        }
                    }
                    
                    // Loncat ke peserta berikutnya, JANGAN buatkan tagihan baru
                    continue; 
                }

                // 3. CEK APAKAH TAGIHAN BULAN INI SUDAH ADA (Double Check agar tidak duplikat di bulan yang sama)
                $exists = TransaksiPembayaran::where('id_pesertaarisan', $p->id_pesertaarisan)
                                ->where('bulan_iuran', $bulanIni)
                                ->exists();

                if (!$exists) {
                    $nominalSkema = $p->skemaArisan->nominal_iuran;
                    $nominalFinal = ($p->id_kelompok != null) ? ceil($nominalSkema / 7) : $nominalSkema;

                    TransaksiPembayaran::create([
                        'id_pesertaarisan' => $p->id_pesertaarisan,
                        'order_id' => 'INV-' . strtoupper(bin2hex(random_bytes(3))) . '-' . $p->id_pesertaarisan,
                        'nominal' => $nominalFinal,
                        'bulan_iuran' => $bulanIni,
                        'status_pembayaran' => 'pending'
                    ]);
                    
                    $daftarNama[] = $p->nama; 
                    $count++;
                }
            }

            // --- LOGIKA KIRIM WHATSAPP ---
            if ($count > 0) {
                $pesanWA = "🔔 *PEMBERITAHUAN TAGIHAN ARISAN*\n";
                $pesanWA .= "Periode: *" . $bulanIni . "*\n\n";
                $pesanWA .= "Assalamu'alaikum Wr. Wb.\nMohon perhatian Bapak/Ibu, tagihan arisan bulan ini telah diterbitkan untuk:\n\n";
                
                $limit = 10;
                foreach (array_slice($daftarNama, 0, $limit) as $index => $nama) {
                    $pesanWA .= ($index + 1) . ". " . $nama . "\n";
                }

                if (count($daftarNama) > $limit) {
                    $pesanWA .= "...dan " . (count($daftarNama) - $limit) . " peserta lainnya.\n";
                }

                $pesanWA .= "\nSilakan melakukan pembayaran melalui aplikasi atau setor tunai ke pengurus.\n";
                $pesanWA .= "Terima kasih atas partisipasinya. 🙏";

                try {
                    \Illuminate\Support\Facades\Http::post(env('WA_BOT_URL'), [
                        'groupId' => env('WA_GROUP_ID'),
                        'message' => $pesanWA
                    ]);
                } catch (\Exception $waEx) {
                    \Log::error("Gagal kirim WA Tagihan: " . $waEx->getMessage());
                }
            }

            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Berhasil menerbitkan $count tagihan baru dan mengirim notifikasi."
                ]);
            }

            return back()->with('success', "Tagihan berhasil dibuat dan notifikasi dikirim.");

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Gagal: " . $e->getMessage()
                ], 500);
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

    public function exportPDF(Request $request)
    {
        $bulanFilter = $request->get('bulan');
        $tahunFilter = $request->get('tahun');

        $query = TransaksiPembayaran::with(['peserta.kelompok'])
                    ->where('status_pembayaran', 'sukses');

        // Judul dinamis untuk di laporan
        $periodeTeks = "Semua Periode";

        if ($bulanFilter && $tahunFilter) {
            $namaBulanInggris = Carbon::createFromDate($tahunFilter, (int)$bulanFilter, 1)->locale('en')->format('F');
            $stringCari = $namaBulanInggris . ' ' . $tahunFilter;
            $query->where('bulan_iuran', $stringCari);
            
            // Buat teks untuk judul PDF dalam bahasa Indonesia
            $namaBulanIndo = Carbon::createFromDate($tahunFilter, (int)$bulanFilter, 1)->translatedFormat('F');
            $periodeTeks = $namaBulanIndo . ' ' . $tahunFilter;
        }

        $allData = $query->latest()->get();

        // Pisahkan data untuk mempermudah di view PDF
        $dataKelompok = $allData->whereNotNull('peserta.id_kelompok')->groupBy('peserta.id_kelompok');
        $dataIndividu = $allData->whereNull('peserta.id_kelompok');

        $pdf = Pdf::loadView('admin.transaksi.pdf', compact('dataKelompok', 'dataIndividu', 'periodeTeks'));
        
        // Nama file jadi dinamis: laporan-januari-2026.pdf
        $fileName = 'laporan-arisan-' . strtolower(str_replace(' ', '-', $periodeTeks)) . '.pdf';
        
        return $pdf->download($fileName);
    }
   public function kirimTagihanWa()
    {
        try {
            $tunggakan = TransaksiPembayaran::with('peserta')
                ->where('status_pembayaran', 'pending')
                ->get()
                ->groupBy('bulan_iuran');

            if ($tunggakan->count() == 0) {
                return back()->with('error', 'Tidak ada tunggakan.');
            }

            // 1. Pastikan Carbon menggunakan locale Indonesia
            \Carbon\Carbon::setLocale('id');
            $bulanSekarangIndo = \Carbon\Carbon::now()->translatedFormat('F Y'); 
            
            $pesanWA = "⚠️ *PENGINGAT IURAN ARISAN*\n";
            $pesanWA .= "--------------------------------\n\n";

            foreach ($tunggakan as $bulanDibuat => $daftarTrx) {
                
                // 2. Gunakan Carbon untuk memparsing string bulan dari database agar formatnya konsisten
                // Misal $bulanDibuat isinya "May 2026" atau "Mei 2026"
                try {
                    $bulanIndo = \Carbon\Carbon::parse($bulanDibuat)->translatedFormat('F Y');
                } catch (\Exception $e) {
                    // Jika parsing gagal, gunakan replace manual Anda sebagai cadangan
                    $bulanIndo = str_replace(
                        ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                        $bulanDibuat
                    );
                }

                $isBulanBerjalan = (trim($bulanIndo) === trim($bulanSekarangIndo));

                $label = (!$isBulanBerjalan) ? "*PERIODE $bulanIndo (TUNGGAKAN LAMA)*" : "PERIODE $bulanIndo (TAGIHAN BARU)";
                
                $pesanWA .= "$label:\n";

                foreach ($daftarTrx as $index => $item) {
                    $nama = $item->peserta->nama;
                    $nominal = number_format($item->nominal, 0, ',', '.');
                    
                    if (!$isBulanBerjalan) {
                        $pesanWA .= ($index + 1) . ". *" . $nama . "* (Rp " . $nominal . ")\n";
                    } else {
                        $pesanWA .= ($index + 1) . ". " . $nama . " (Rp " . $nominal . ")\n";
                    }
                }
                $pesanWA .= "\n"; 
            }

            $pesanWA .= "--------------------------------\n";
            $pesanWA .= "Segera bayar via aplikasi atau tunai. Terima kasih. 🙏";

            // Kirim WA...
            \Illuminate\Support\Facades\Http::post(env('WA_BOT_URL'), [
                'groupId' => env('WA_GROUP_ID'),
                'message' => $pesanWA
            ]);

            return back()->with('success', 'Notifikasi berhasil dikirim!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}