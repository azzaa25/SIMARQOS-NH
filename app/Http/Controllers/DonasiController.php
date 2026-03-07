<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KegiatanSosial;
use Midtrans\Config;
use Midtrans\Snap;

class DonasiController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function checkout(Request $request)
    {
        $kegiatan = KegiatanSosial::findOrFail($request->id_kegiatan);
        $orderId = 'DONASI-' . time() . '-' . rand(10,99);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int)$request->nominal,
            ],
            'customer_details' => [
                'first_name' => $request->nama_donatur,
            ],
            // 1. PENTING: Kirim Metadata agar Controller Arisan bisa baca nama donatur & id kegiatan
            'metadata' => [
                'id_kegiatan' => $kegiatan->id_kegiatan,
                'nama_donatur' => $request->nama_donatur,
                'keterangan'   => 'Donasi untuk ' . $kegiatan->nama_kegiatan
            ],
            // 2. PENTING: Paksa Midtrans lapor ke URL callback yang sudah kita siapkan
            'callbacks' => [
                'notification_url' => 'https://refutable-supportable-sherlyn.ngrok-free.dev/api/midtrans-callback',
            ],
            'item_details' => [
                [
                    'id' => $kegiatan->id_kegiatan,
                    'price' => (int)$request->nominal,
                    'quantity' => 1,
                    'name' => 'Donasi: ' . substr($kegiatan->nama_kegiatan, 0, 40)
                ]
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json([
                'token' => $snapToken,
                'order_id' => $orderId
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}