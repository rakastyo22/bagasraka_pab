<?php

namespace App\Midtrans;

use Carbon\Carbon;
use Midtrans\Snap;
use Midtrans\Config;
use App\Midtrans\Midtrans;

class CreateSnapTokenService {

    protected $transaksi;

    public function __construct($transaksi)
    {
        $this->transaksi = $transaksi;
        
        // Mengatur ServerKey Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');  // Pastikan ServerKey sudah diset
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');  // Jika perlu, set ClientKey
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false); // Set environment, misalnya true untuk production
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function getSnapToken() {
        $order_id = Carbon::parse($this->transaksi->tanggal_order)
            ->format('Y-m-d') . str_pad($this->transaksi->id, 4, '0', STR_PAD_LEFT);

        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => $this->transaksi->total_harga,
            ],
        ];

        try {
            // Mendapatkan Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            // Penanganan kesalahan
            throw new \RuntimeException('Gagal mendapatkan Snap Token: ' . $e->getMessage());
        }
    }
}