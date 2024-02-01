<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\TransaksiKedai;
use App\Models\TransaksiPencucian;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function sumKaryawan(){
        $karyawan = Karyawan::whereHas('jabatan', function($q){
            $q->where('nama', 'Pencuci');
        })
        ->where('status', 'Aktif')
        ->count();

        return $karyawan;
    }

    public function sumKendaraanThisMonth(){
        $now = now()->format('m');

        $kendaraan = TransaksiPencucian::whereMonth('tgl_pencucian', $now)->count();

        return $kendaraan;
    }

    public function sumKendaraanBelumBayar(){
        $kendaraan = TransaksiPencucian::where('status', 'Belum Bayar')->count();

        return $kendaraan;
    }

    public function sumPemasukanPencucianBulan(){
        $now = now()->format('m');

        $transaksi = TransaksiPencucian::whereMonth('paid_at', $now)->where('status', 'Lunas')->sum('keuntungan');

        return $transaksi;
    }

    public function sumPemasukanKedaiBulan(){
        $now = now()->format('m');

        $transaksi = TransaksiKedai::whereMonth('tgl_penjualan', $now)->sum('total_penjualan');

        return $transaksi;
    }
}
