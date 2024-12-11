<?php

namespace App\Http\Controllers\Api;

use App\Models\Alamat; 
use App\Models\Transaksi; 
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; 

use App\Http\Controllers\Api\TransaksiController;


class TransaksiController extends Controller
{
    public function get_ongkir(Request $request)
    {
        $weight = $request->get('weight', 0);
        $origin = env('RAJAONGKIR_ORIGIN');
        $destination = $request->get('destination', 0);
        $courier = $request->get('courier', '');
        $raja_ongkir = Libs::hitung_ongkos_kirim($weight, $origin, $destination
        , $courier);
        if($raja_ongkir['code'] == '200'){
        return response()->json(['services' => $raja_ongkir['services']]
        , 200);
        }else{
        return response()->json(['text' => $raja_ongkir['text']], 500);

        }
    }
}
