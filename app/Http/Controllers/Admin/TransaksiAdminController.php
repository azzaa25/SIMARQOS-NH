<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransaksiPembayaran;
use App\Models\PesertaArisan;
use App\Models\SkemaArisan;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
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
        // Gunakan try-catch agar jika error, kita tahu penyebabnya
        try {
            $pesertas = PesertaArisan::whereHas('user', function($q) {
                $q->where('status', 'aktif');
            })->with('skemaArisan')->get();

            $bulanIni = Carbon::now()->translatedFormat('F Y');
            $count = 0;

            foreach ($pesertas as $p) {
                // Pastikan peserta punya skema arisan sebelum dibuat tagihannya
                if (!$p->skemaArisan) {
                    continue; // Lewati jika data skema tidak ada
                }

                $exists = TransaksiPembayaran::where('id_pesertaarisan', $p->id_pesertaarisan)
                            ->where('bulan_iuran', $bulanIni)
                            ->exists();

                if (!$exists) {
                    $nominalSkema = $p->skemaArisan->nominal_iuran;
                    // Pastikan pembagian kelompok aman (tidak bagi nol)
                    $nominalFinal = ($p->id_kelompok != null) ? ceil($nominalSkema / 7) : $nominalSkema;

                    TransaksiPembayaran::create([
                        'id_pesertaarisan' => $p->id_pesertaarisan,
                        'order_id' => 'INV-' . strtoupper(bin2hex(random_bytes(3))) . '-' . $p->id_pesertaarisan,
                        'nominal' => $nominalFinal,
                        'bulan_iuran' => $bulanIni,
                        'status_pembayaran' => 'pending'
                    ]);
                    $count++;
                }
            }

            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Berhasil menerbitkan $count tagihan baru."
                ]);
            }

            return back()->with('success', "Tagihan berhasil dibuat.");

        } catch (\Exception $e) {
            // Jika error, kirim pesan error yang sebenarnya ke tampilan
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
}