<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Login
Route::post('login', 'Api\AuthController@login');

//Jabatan
Route::post('jabatan', 'Api\JabatanController@create');
Route::post('jabatan/{id}', 'Api\JabatanController@update');
Route::delete('jabatan/{id}', 'Api\JabatanController@delete');
Route::get('jabatan/{id}', 'Api\JabatanController@get');
Route::get('jabatan', 'Api\JabatanController@getAll');
Route::get('list-selection-jabatan', 'Api\JabatanController@listJabatan');

//Jenis Kendaraan
Route::post('jenis-kendaraan', 'Api\JenisKendaraanController@create');
Route::post('jenis-kendaraan/{id}', 'Api\JenisKendaraanController@update');
Route::delete('jenis-kendaraan/{id}', 'Api\JenisKendaraanController@delete');
Route::get('jenis-kendaraan/{id}', 'Api\JenisKendaraanController@get');
Route::get('jenis-kendaraan', 'Api\JenisKendaraanController@getAll');
Route::get('jenis-kendaraan', 'Api\JenisKendaraanController@getAll');
Route::get('list-selection-jenis-kendaraan', 'Api\JenisKendaraanController@listJenisKendaraan');
Route::get('list-card-jenis-kendaraan', 'Api\JenisKendaraanController@cardListJenisKendaraan');

//Kendaraan
Route::post('kendaraan', 'Api\KendaraanController@create');
Route::post('kendaraan/{id}', 'Api\KendaraanController@update');
Route::delete('kendaraan/{id}', 'Api\KendaraanController@delete');
Route::get('kendaraan/{id}', 'Api\KendaraanController@get');
Route::get('kendaraan', 'Api\KendaraanController@getAll');
Route::get('list-selection-kendaraan', 'Api\KendaraanController@listKendaraan');

//Karyawan
Route::post('karyawan', 'Api\KaryawanController@create');
Route::post('karyawan/{id}', 'Api\KaryawanController@update');
Route::delete('karyawan/{id}', 'Api\KaryawanController@delete');
Route::get('karyawan/{id}', 'Api\KaryawanController@get');
Route::get('karyawan', 'Api\KaryawanController@getAll');
Route::get('list-selection-karyawan', 'Api\KaryawanController@listKaryawan');
Route::get('list-selection-kasir', 'Api\KaryawanController@listKaryawanKasir');
Route::get('list-selection-penjaga-kedai', 'Api\KaryawanController@listKaryawanPenjagaKedai');
Route::get('list-selection-pencuci', 'Api\KaryawanController@listKaryawanPencuci');
Route::post('karyawan/profil/{id}', 'Api\KaryawanController@updateProfil');
Route::post('karyawan/photo/{id}', 'Api\KaryawanController@updateFoto');
Route::post('karyawan/password/{id}', 'Api\KaryawanController@updatePassword');

//Peminjaman Karyawan
Route::post('peminjaman-karyawan', 'Api\PeminjamanKaryawanController@create');
Route::post('peminjaman-karyawan/{id}', 'Api\PeminjamanKaryawanController@update');
Route::delete('peminjaman-karyawan/{id}', 'Api\PeminjamanKaryawanController@delete');
Route::get('peminjaman-karyawan/{id}', 'Api\PeminjamanKaryawanController@get');
Route::get('peminjaman-karyawan', 'Api\PeminjamanKaryawanController@getAll');

//Menu Kedai
Route::post('menu-kedai', 'Api\MenuKedaiController@create');
Route::post('menu-kedai/{id}', 'Api\MenuKedaiController@update');
Route::delete('menu-kedai/{id}', 'Api\MenuKedaiController@delete');
Route::get('menu-kedai/{id}', 'Api\MenuKedaiController@get');
Route::get('menu-kedai', 'Api\MenuKedaiController@getAll');
Route::get('list-selection-menu-kedai', 'Api\MenuKedaiController@listMenuKedai');

//Pengeluaran Kedai
Route::post('pengeluaran-kedai', 'Api\PengeluaranKedaiController@create');
Route::post('pengeluaran-kedai/{id}', 'Api\PengeluaranKedaiController@update');
Route::delete('pengeluaran-kedai/{id}', 'Api\PengeluaranKedaiController@delete');
Route::get('pengeluaran-kedai/{id}', 'Api\PengeluaranKedaiController@get');
Route::get('pengeluaran-kedai', 'Api\PengeluaranKedaiController@getAll');

//Mobil Pelanggan
Route::get('mobil-pelanggan/{id}', 'Api\MobilPelangganController@get');
Route::get('mobil-pelanggan', 'Api\MobilPelangganController@getAll');
Route::get('mobil-pelanggan-transaksi/{id}', 'Api\MobilPelangganController@getTransaksiByMobilPelanggan');
Route::get('riwayat/mobil-pelanggan', 'Api\MobilPelangganController@getRiwayatByPlat');

//Transaksi Pencucian
// Route::get('generate-nomor-pencucian', 'Api\TransaksiPencucianController@generateNoPencucian');
Route::post('transaksi-pencucian', 'Api\TransaksiPencucianController@create');
Route::post('transaksi-pencucian/{id}', 'Api\TransaksiPencucianController@update');
Route::delete('transaksi-pencucian/{id}', 'Api\TransaksiPencucianController@delete');
Route::get('transaksi-pencucian/{id}', 'Api\TransaksiPencucianController@get');
Route::get('transaksi-pencucian', 'Api\TransaksiPencucianController@getAll');
Route::put('transaksi-pencucian/cuci', 'Api\TransaksiPencucianController@prosesCuci');
Route::put('transaksi-pencucian/bayar', 'Api\TransaksiPencucianController@prosesBayar');
Route::put('transaksi-pencucian/finish', 'Api\TransaksiPencucianController@finish');
Route::get('transaksi-pencucian/nota/{id}', 'Api\TransaksiPencucianController@cetakNotaPencucian');
Route::get('chart/transaksi-pencucian', 'Api\TransaksiPencucianController@getByMonthYearDashboard');

//Gaji Karyawan
Route::post('gaji-karyawan', 'Api\GajiKaryawanController@syncGaji');
Route::put('gaji-karyawan/status/{id}', 'Api\GajiKaryawanController@updateStatus');
Route::get('gaji-karyawan/{id}', 'Api\GajiKaryawanController@get');
Route::get('gaji-karyawan', 'Api\GajiKaryawanController@getAll');

//Transaksi Kedai
// Route::get('generate-nomor-kedai', 'Api\TransaksiKedaiController@generateNoPenjualan');
Route::post('transaksi-kedai', 'Api\TransaksiKedaiController@create');
Route::post('transaksi-kedai/{id}', 'Api\TransaksiKedaiController@update');
Route::delete('transaksi-kedai/{id}', 'Api\TransaksiKedaiController@delete');
Route::get('transaksi-kedai/{id}', 'Api\TransaksiKedaiController@get');
Route::get('transaksi-kedai', 'Api\TransaksiKedaiController@getAll');
Route::get('chart/transaksi-kedai', 'Api\TransaksiKedaiController@getByMonthYearDashboard');

//Laporan
Route::get('laporan/transaksi-pencucian', 'Api\LaporanController@generateLaporanPencucian');
Route::get('laporan/transaksi-kedai', 'Api\LaporanController@generateLaporanKedai');
Route::get('laporan/pengeluaran-kedai', 'Api\LaporanController@generateLaporanPengeluaranKedai');