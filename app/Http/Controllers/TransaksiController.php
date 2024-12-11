<?php

namespace App\Http\Controllers;

use App\Libs;
use App\Models\Alamat;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Midtrans\CreateSnapTokenService;

class TransaksiController extends Controller
{
    // Menampilkan daftar produk
    public function daftar_produk()
    {
        if (Transaksi::where('status_transaksi', 'PESAN')
            ->where('user_id', Auth::id())
            ->exists()) {
            return redirect('/transaksi/keranjang');
        }

        $produks = Produk::paginate(4);
        return view('transaksi.daftar_produk', compact('produks'));
    }

    // Menambah produk ke keranjang
    public function tambah_keranjang(Request $request)
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
            'produk_id' => 'required|exists:produks,id',
        ]);

        $userId = Auth::id();
        $keranjang = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '')
            ->where('user_id', $userId)
            ->first();

        $checkedOut = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '<>', '')
            ->where('user_id', $userId)
            ->exists();

        if ($checkedOut) {
            return redirect('/home');
        }

        if ($keranjang && $keranjang->produk_id != $request->produk_id) {
            return redirect('/transaksi/keranjang');
        }

        $produk = Produk::findOrFail($request->produk_id);
        if (!$keranjang) {
            $alamat = Alamat::where('user_id', $userId)->firstOrFail();

            $keranjang = new Transaksi();
            $keranjang->fill([
                'tanggal_order' => Carbon::today(),
                'user_id' => $userId,
                'alamat_id' => $alamat->id,
                'produk_id' => $request->produk_id,
                'qty' => $request->qty,
                'status_transaksi' => 'PESAN',
                'rating' => 1,
                'courier' => '',
                'service' => '',
                'waktu_kirim' => 0,
                'ongkos_kirim' => 0,
                'total_harga' => 0,
            ]);
        } else {
            $keranjang->qty = $request->qty;
        }

        $keranjang->harga_barang = $produk->harga * $keranjang->qty;
        $keranjang->weight = $this->hitung_berat_kirim($keranjang->qty, $produk->berat);
        $keranjang->save();

        return redirect('/transaksi/keranjang');
    }

    // Menghapus keranjang
    public function hapus_keranjang()
    {
        $userId = Auth::id();
        $keranjang = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '')
            ->where('user_id', $userId)
            ->first();

        $checkedOut = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '<>', '')
            ->where('user_id', $userId)
            ->exists();

        if ($checkedOut) {
            return redirect('/home');
        }

        if ($keranjang) {
            $keranjang->delete();
        }

        return redirect('/home');
    }

    // Menghitung berat kirim
    private function hitung_berat_kirim($qty, $berat)
    {
        $berat_wadah = 50; // Berat tambahan (contoh: kemasan)
        return ceil((($qty * ($berat + $berat_wadah))) / 1000.0) * 1000;
    }

    // Menampilkan keranjang
    public function keranjang()
    {
        $userId = Auth::id();
        $keranjang = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '')
            ->where('user_id', $userId)
            ->first();

        if (!$keranjang) {
            return redirect('/transaksi/daftar_produk');
        }

        return view('transaksi.keranjang', ['transaksi' => $keranjang]);
    }

    // Proses checkout
    public function checkout()
    {
        $userId = Auth::id();
        $keranjang = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '')
            ->where('user_id', $userId)
            ->first();

        if (!$keranjang) {
            return redirect('/transaksi/daftar_produk');
        }

        $alamat = Alamat::findOrFail($keranjang->alamat_id);
        $keranjang->courier = 'pos';

        $raja_ongkir = Libs::hitung_ongkos_kirim(
            $keranjang->weight,
            env('RAJAONGKIR_ORIGIN'),
            $alamat->kota_id,
            $keranjang->courier
        );

        if ($raja_ongkir['code'] === '200') {
            $services = $raja_ongkir['services'];
            $pilihan = $services[0];
            $keranjang->fill([
                'service' => $pilihan['service'],
                'ongkos_kirim' => $pilihan['ongkos_kirim'],
                'total_harga' => $keranjang->harga_barang + $pilihan['ongkos_kirim'],
            ]);
            $keranjang->save();
        }

        return view('transaksi.checkout', [
            'transaksi' => $keranjang,
            'destination' => $alamat->kota_id,
            'couriers' => ['jne', 'pos', 'tiki'],
            'services' => $services ?? [],
        ]);
    }

    // Menyimpan ongkos kirim
    public function simpan_ongkir(Request $request)
    {
        $request->validate([
            'service' => 'required',
            'courier' => 'required',
            'ongkos_kirim' => 'required|integer',
            'total_harga' => 'required|integer',
        ]);

        $transaksi = Transaksi::findOrFail($request->id);
        $transaksi->update($request->only(['service', 'courier', 'ongkos_kirim', 'total_harga']));

        return redirect('/transaksi/bayar');
    }

    // Halaman pembayaran
    public function bayar()
    {
        $userId = Auth::id();
        $keranjang = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '')
            ->where('user_id', $userId)
            ->first();

        if ($keranjang) {
            return redirect('/transaksi/keranjang');
        }

        $unpaid = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '<>', '')
            ->where('user_id', $userId)
            ->firstOrFail();

        $order_id = Carbon::parse($unpaid->tanggal_order)
            ->format('Ymd') . str_pad($unpaid->id, 4, '0', STR_PAD_LEFT);

        $resp = Libs::status_midtrans($order_id);

        if ($resp['code'] === '200') {
            if (in_array($resp['message'], ['expire', 'cancel'])) {
                $unpaid->delete();
                return redirect('/home')->with('status_message', [
                    'type' => 'info',
                    'text' => 'Transaksi dihapus karena gagal bayar!',
                ]);
            }

            if ($resp['message'] === 'pending') {
                return redirect('/home')->with('status_message', [
                    'type' => 'info',
                    'text' => 'Harap bayar transaksi Anda!',
                ]);
            }
        }

        $service = new CreateSnapTokenService($unpaid);
        $token = $service->getSnapToken();

        return view('transaksi.bayar', compact('unpaid', 'token'));
    }
}
