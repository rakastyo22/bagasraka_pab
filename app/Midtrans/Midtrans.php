<?php
namespace App\Midtrans;

use Midtrans\Config;

class Midtrans {
    protected $serverKey;
    protected $isProduction;
    protected $isSanitized;
    protected $is3ds;

    public function __construct() {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', false);
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);

        // Mengonfigurasi Midtrans
        $this->configureMidtrans();
    }

    public function configureMidtrans() {
        Config::$serverKey = $this->serverKey;
        Config::$isProduction = $this->isProduction;
        Config::$isSanitized = $this->isSanitized;
        Config::$is3ds = $this->is3ds;
    }
}