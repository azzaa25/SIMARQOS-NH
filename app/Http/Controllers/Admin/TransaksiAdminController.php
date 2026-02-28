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
    public function index()
    {
        $transaksi = TransaksiPembayaran::with('peserta')->latest()->get();

        // --- FITUR TUNGGAKAN ---
        $tunggakan = TransaksiPembayaran::with(['peserta.skemaArisan', 'peserta.kelompok'])
        ->where('status_pembayaran', 'pending')
        ->orderBy('created_at', 'asc')
        ->get();

        // Hitung total peserta yang aktif & nonaktif saja
        $totalPeserta = PesertaArisan::whereHas('user', function ($q) {
            $q->whereIn('status', ['aktif', 'nonaktif']);
        })->count();

        $totalSkema = SkemaArisan::count();
        $totalKas = TransaksiPembayaran::where('status_pembayaran', 'sukses')->sum('nominal');

        $rataRata = TransaksiPembayaran::where('status_pembayaran', 'sukses')->avg('nominal');
        $tertinggi = TransaksiPembayaran::where('status_pembayaran', 'sukses')->max('nominal');
        
        $metodeFavorit = TransaksiPembayaran::where('status_pembayaran', 'sukses')
            ->select('metode_pembayaran', DB::raw('count(*) as total'))
            ->groupBy('metode_pembayaran')
            ->orderBy('total', 'desc')
            ->first();

        $grafikBulanan = TransaksiPembayaran::where('status_pembayaran', 'sukses')
            ->select(DB::raw('COUNT(*) as total'), DB::raw('MONTH(created_at) as bulan'))
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->take(6)
            ->pluck('total');

        return view('admin.transaksi.index', compact(
            'transaksi', 'tunggakan', 'totalPeserta', 'totalSkema', 'totalKas', 
            'rataRata', 'tertinggi', 'metodeFavorit', 'grafikBulanan'
        ));
    }

    public function callback(Request $request)
    {
        $serverKey = config('services.midtrans.serverKey');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $transaksi = TransaksiPembayaran::where('order_id', $request->order_id)->first();
                if ($transaksi) {
                    $transaksi->update([
                        'status_pembayaran' => 'sukses',
                        'metode_pembayaran' => $request->payment_type,
                    ]);
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

    public function exportPDF()
    {
        $data = TransaksiPembayaran::with('peserta')->where('status_pembayaran', 'sukses')->get();
        $pdf = Pdf::loadView('admin.transaksi.pdf', compact('data'));
        return $pdf->download('laporan-keuangan-arisan.pdf');
    }
}