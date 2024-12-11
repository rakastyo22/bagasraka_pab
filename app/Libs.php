<?php
namespace App;

use Illuminate\Support\Facades\App;

class Libs {
    // Menghitung berat kirim dengan pembulatan ke atas dalam gram
    public static function hitung_berat_kirim($qty, $berat_satuan) {
        return ceil((($qty * $berat_satuan)) / 1000.0) * 1000;
    }

    // Menghitung ongkos kirim menggunakan API RajaOngkir
    public static function hitung_ongkos_kirim($weight, $origin, $destination, $courier) {
        $err_message = '';
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "key: " . env('RAJAONGKIR_KEY'),
                "Content-Type: application/x-www-form-urlencoded"
            ],
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.rajaongkir.com/starter/cost',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query([
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier
            ]),
        ]);
        
        $resp = curl_exec($curl);
        if (curl_errno($curl)) {
            $err_message = 'Error: "' . curl_error($curl) . '" - Code:' . curl_errno($curl);
        }
        curl_close($curl);

        if ($err_message == '') {
            $json = json_decode($resp, TRUE);
            if (isset($json['rajaongkir']['results'][0]['costs'])) {
                $services = [];
                foreach ($json['rajaongkir']['results'][0]['costs'] as $cost) {
                    $services[] = [
                        'service' => $cost['service'],
                        'ongkos_kirim' => $cost['cost'][0]['value'],
                        'waktu_kirim' => $cost['cost'][0]['etd']
                    ];
                }
                return ['code' => '200', 'services' => $services];
            } else {
                return ['code' => '500', 'text' => 'No costs data found in RajaOngkir response'];
            }
        } else {
            return ['code' => '500', 'text' => $err_message];
        }
    }

    // Mengecek status transaksi di Midtrans
    public static function status_midtrans($order_id) {
        $err_message = '';
        $cred = base64_encode(env('MIDTRANS_SERVER_KEY') . ':');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Basic " . $cred
            ],
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.sandbox.midtrans.com/v2/' . $order_id . '/status',
            CURLOPT_POST => 0,
        ]);

        $resp = curl_exec($curl);
        if (curl_errno($curl)) {
            $err_message = 'Error: "' . curl_error($curl) . '" - Code:' . curl_errno($curl);
        }
        curl_close($curl);

        if ($err_message == '') {
            $json = json_decode($resp, TRUE);
            if (isset($json['status_code']) && in_array($json['status_code'], ['407', '201', '200'])) {
                return ['code' => '200', 'message' => $json['transaction_status']];
            } else {
                return [
                    'code' => '400',
                    'message' => $json['status_message'] ?? 'Unknown error'
                ];
            }
        } else {
            return ['code' => '500', 'message' => $err_message];
        }
    }
}
