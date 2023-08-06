<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobilPelanggan;
use App\Models\TransaksiPencucian;
use Illuminate\Http\Request;

class MobilPelangganController extends Controller
{
    public function get($id){
        $data = MobilPelanggan::with(['transaksis'])->where('id', $id)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Tampil Data Mobil Pelanggan Berhasil!',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Data Mobil Pelanggan Tidak Ditemukan',
            'data' => null,
        ], 404);
    }

    public function getAll(){

        $data = MobilPelanggan::with(['transaksis'])->get();

        return response([
            'message' => 'Tampil Data Mobil Pelanggan Berhasil!',
            'data' => $data,
        ], 200);
    }

    public function getTransaksiByMobilPelanggan($id){
        $data = TransaksiPencucian::where('mobil_pelanggan_id', $id)->get();

        return $data;
    }

    public function getRiwayatByPlat(Request $request){
        $no_polisi = $request->no_polisi;

        if(!$no_polisi){
            return response([
                'message' => 'Silahkan Isi Nomor Plat Terlebih Dahulu!',
                'data' => null,
            ], 400);
        }

        $data = MobilPelanggan::with(['transaksis'])->where('no_polisi', $no_polisi)->first();

        if(!$data){
            return response([
                'message' => 'Data Mobil Tidak Ditemukan!',
                'data' => $data,
            ], 404);
        }

        foreach($data->transaksis as $transaksi){
            $tgl = strtotime($transaksi->tgl_pencucian);
            $formatTgl = date('d', $tgl) .' '. $this->fetctMonthIndonesia(date('m', $tgl)) .' '. date('Y', $tgl);
            $transaksi->formatTgl = $formatTgl;
        }

        return response([
            'message' => 'Tampil Data Mobil Pelanggan Berhasil!',
            'data' => $data,
        ], 200);
    }

    private function fetctMonthIndonesia($value){
        $month = '';
        switch ($value) {
            case 1:
                $month = 'Januari';
                break;
            case 2:
                $month = 'Februari';
                break;
            case 3:
                $month = 'Maret';
                break;
            case 4:
                $month = 'April';
                break;
            case 5:
                $month = 'Mei';
                break;
            case 6:
                $month = 'Juni';
                break;
            case 7:
                $month = 'Juli';
                break;
            case 8:
                $month = 'Agustus';
                break;
            case 9:
                $month = 'September';
                break;
            case 10:
                $month = 'Oktober';
                break;
            case 11:
                $month = 'November';
                break;
            case 12:
                $month = 'Desember';
                break;
            default:
                # code...
                break;
        }
        return $month;
    }
}
