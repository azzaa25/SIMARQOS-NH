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

        $grafikBulanan = TransaksiPembayaran::where('status_pembayaran', 'sukses')
            ->select(DB::raw('SUM(nominal) as total'), DB::raw('MONTH(created_at) as bulan'))
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->pluck('total');

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
            })->with('skemaArisan')->get();

            $bulanIni = Carbon::now()->translatedFormat('F Y');
            $count = 0;
            $daftarNama = []; // Untuk menampung nama yang dibuatkan tagihan

            foreach ($pesertas as $p) {
                if (!$p->skemaArisan) continue;

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
                    
                    // Masukkan nama ke daftar untuk notifikasi
                    $daftarNama[] = $p->nama; 
                    $count++;
                }
            }

            // --- LOGIKA KIRIM WHATSAPP ---
            if ($count > 0) {
                $pesanWA = "🔔 *PEMBERITAHUAN TAGIHAN ARISAN*\n";
                $pesanWA .= "Periode: *" . $bulanIni . "*\n\n";
                $pesanWA .= "Assalamu'alaikum Wr. Wb.\nMohon perhatian Bapak/Ibu, tagihan arisan bulan ini telah diterbitkan untuk:\n\n";
                
                // Batasi tampilan nama jika terlalu banyak agar chat tidak kepanjangan
                $limit = 10;
                foreach (array_slice($daftarNama, 0, $limit) as $index => $nama) {
                    $pesanWA .= ($index + 1) . ". " . $nama . "\n";
                }

                if (count($daftarNama) > $limit) {
                    $pesanWA .= "...dan " . (count($daftarNama) - $limit) . " peserta lainnya.\n";
                }

                $pesanWA .= "\nSilakan melakukan pembayaran melalui aplikasi atau setor tunai ke pengurus.\n";
                $pesanWA .= "Terima kasih atas partisipasinya. 🙏";

                // Kirim ke Bot Node.js
                try {
                    Http::post(env('WA_BOT_URL'), [
                        'groupId' => env('WA_GROUP_ID'),
                        'message' => $pesanWA
                    ]);
                } catch (\Exception $waEx) {
                    // Jangan hentikan proses jika WA gagal, cukup log saja
                    \Log::error("Gagal kirim WA Tagihan: " . $waEx->getMessage());
                }
            }
            // --- END LOGIKA WA ---

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

            $bulanSekarangIndo = Carbon::now()->translatedFormat('F Y');
            
            $pesanWA = "⚠️ *PENGINGAT IURAN ARISAN*\n";
            $pesanWA .= "--------------------------------\n\n";

            foreach ($tunggakan as $bulanInggris => $daftarTrx) {
                // --- PROSES TERJEMAH BULAN ---
                $bulanIndo = str_replace(
                    ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    $bulanInggris
                );

                // Cek apakah periode ini adalah bulan berjalan (pakai nama bulan yang sudah Indo)
                $label = ($bulanIndo !== $bulanSekarangIndo) ? "*PERIODE $bulanIndo (TUNGGAKAN LAMA)*" : "PERIODE $bulanIndo (TAGIHAN BARU)";
                
                $pesanWA .= "$label:\n";

                foreach ($daftarTrx as $index => $item) {
                    $nama = $item->peserta->nama;
                    $nominal = number_format($item->nominal, 0, ',', '.');
                    
                    if ($bulanIndo !== $bulanSekarangIndo) {
                        $pesanWA .= ($index + 1) . ". *" . $nama . "* (Rp " . $nominal . ")\n";
                    } else {
                        $pesanWA .= ($index + 1) . ". " . $nama . " (Rp " . $nominal . ")\n";
                    }
                }
                $pesanWA .= "\n"; 
            }

            $pesanWA .= "--------------------------------\n";
            $pesanWA .= "Segera bayar via aplikasi atau tunai. Terima kasih. 🙏";

            Http::post(env('WA_BOT_URL'), [
                'groupId' => env('WA_GROUP_ID'),
                'message' => $pesanWA
            ]);

            return back()->with('success', 'Notifikasi berhasil dikirim!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}