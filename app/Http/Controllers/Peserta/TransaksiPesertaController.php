<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\TransaksiPembayaran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Midtrans\Config;
use Midtrans\Snap;

class TransaksiPesertaController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans dari file config/services.php
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');
    }

    /**
     * Menampilkan daftar tagihan iuran milik peserta yang sedang login
     */
    public function index()
    {
        // Mengambil data transaksi berdasarkan id_user yang login
        $tagihans = TransaksiPembayaran::whereHas('peserta', function($query) {
            $query->where('id_user', auth()->id());
        })->latest()->get();

        return view('peserta.transaksi.index', compact('tagihans'));
    }

    /**
     * Mendapatkan Snap Token dari Midtrans
     */
    public function getToken(int $id): JsonResponse
    {
        $trx = TransaksiPembayaran::findOrFail($id);

        // Jika transaksi sudah memiliki token, gunakan yang sudah ada
        if ($trx->snap_token) {
            return response()->json(['snap_token' => $trx->snap_token]);
        }

        $params = [
            'transaction_details' => [
                'order_id' => $trx->order_id,
                'gross_amount' => (int)$trx->nominal,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $trx->update(['snap_token' => $snapToken]);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}