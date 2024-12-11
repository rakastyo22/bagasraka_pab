<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\TransaksiController;


Route::get('/alamat', [AlamatController::class, 'index']);
 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware(['auth', 'toko'])->get('/user',
[UserController::class, 'index']);
Route::middleware(['auth', 'toko'])->get('/user/create',
[UserController::class, 'create']);
Route::middleware(['auth', 'toko'])->post('/user/store',
[UserController::class, 'store']);
Route::middleware(['auth', 'toko'])->get('/user/edit/{id}',
[UserController::class, 'edit']);
Route::middleware(['auth', 'toko'])->post('/user/update/{id}',
[UserController::class, 'update']);
Route::middleware(['auth', 'toko'])->post('/user/destroy/{id}',
[UserController::class, 'destroy']);

Route::get('/alamat', [AlamatController::class, 'index'])->name('alamat');
Route::middleware(['auth', 'toko'])->get('/alamat', 
[AlamatController::class, 'index']);
Route::middleware(['auth', 'toko'])->post('/alamat/sync_province', 
[AlamatController::class, 'sync_province']);
Route::middleware(['auth', 'toko'])->post('/alamat/sync_city', 
[AlamatController::class, 'sync_city']);

Route::middleware(['auth'])->get('/alamat/edit/{id}',
[AlamatController::class, 'edit']);
Route::middleware(['auth'])->post('/alamat/update/{id}',
[AlamatController::class, 'update']);
Route::middleware(['auth','toko'])->post('/alamat/destroy/{id}',
[AlamatController::class, 'destroy']);

Route::middleware(['auth', 'konsumen'])->get('/alamat/create',
[AlamatController::class, 'create']);
Route::middleware(['auth', 'konsumen'])->post('/alamat/store',
[AlamatController::class, 'store']);
Route::middleware(['auth', 'konsumen'])->get('/alamat/show/{id}',
[AlamatController::class, 'show']);

Route::middleware(['auth', 'konsumen'])->get('/transaksi/daftar_produk',
[TransaksiController::class, 'daftar_produk']);
Route::middleware(['auth', 'konsumen'])->post('/transaksi/tambah_keranjang',
[TransaksiController::class, 'tambah_keranjang']);
Route::middleware(['auth', 'konsumen'])->get('/transaksi/keranjang',
[TransaksiController::class, 'keranjang']);
Route::middleware(['auth', 'konsumen'])->post('/transaksi/hapus_keranjang',
[TransaksiController::class, 'hapus_keranjang']);
Route::middleware(['auth', 'konsumen'])->post('/transaksi/checkout',
[TransaksiController::class, 'checkout']);
Route::middleware(['auth', 'konsumen'])->post('/transaksi/simpan_ongkir',
[TransaksiController::class, 'simpan_ongkir']);
Route::middleware(['auth', 'konsumen'])->get('/transaksi/bayar',
[TransaksiController::class, 'bayar']);

Route::post('/transaksi/checkout', [TransaksiController::class, 'checkout']);

