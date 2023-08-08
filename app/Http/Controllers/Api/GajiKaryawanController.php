<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksiPencuci;
use App\Models\GajiKaryawan;
use App\Models\Karyawan;
use App\Models\PeminjamanKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GajiKaryawanController extends Controller
{
    private function generateUuid(){
        $isDuplicate = true;
        $duplicateArr = GajiKaryawan::pluck('uuid')->toArray();

        while($isDuplicate){
            $uuid = Str::orderedUuid()->toString();

            if(!in_array($uuid, $duplicateArr)){
                $isDuplicate = false;
            }
        }    
        
        return $uuid;
    }

    public function syncGaji(Request $request){
        $karyawan_id = $request->karyawan_id;
        $bulan = $this->fetchMonth($request->bulan);
        $tahun = $request->tahun;

        $findGaji = GajiKaryawan::where('karyawan_id', $karyawan_id)
        ->where('bulan', $request->bulan)
        ->where('tahun', $tahun)
        ->first();

        if($findGaji){
            return response([
                'message' => 'Gaji Karyawan Sudah Terhitung!',
                'data' => null,
            ], 400);
        }

        $karyawan = Karyawan::with(['jabatan'])->where('id', $karyawan_id)->first();
        $jabatan = $karyawan->jabatan;

        if($jabatan->nama != 'Pencuci'){
            $gajiKotor = $karyawan->gaji;
        } else{
            $gajiKotor = DetailTransaksiPencuci::whereHas('transaksi_pencucian', function($q) use($bulan, $tahun){
                $q->whereMonth('tgl_pencucian', $bulan);
                $q->whereYear('tgl_pencucian', $tahun);
            })
            ->where('karyawan_id', $karyawan_id)
            ->sum('upah_pencuci');
        }

        $totalUtang = PeminjamanKaryawan::where('karyawan_id', $karyawan_id)
        ->whereMonth('tgl_peminjaman', $bulan)
        ->whereYear('tgl_peminjaman', $tahun)
        ->sum('nominal');

        $utangSebelum = GajiKaryawan::where('karyawan_id', $karyawan_id)
        ->where('total_gaji_bersih', '<', 0)
        ->first();

        if(!is_null($utangSebelum)){
            $totalUtang-=$utangSebelum->total_gaji_bersih;
            
            $utangSebelum->update([
                'total_gaji_bersih' => 0,
                'status' => 'Sudah Diterima',
            ]);
        }

        $gajiBersih = $gajiKotor - $totalUtang;

        if($gajiBersih < 0){
            $status = 'Utang';
        } else if($gajiBersih == 0){
            $status = 'Sudah Diterima';
        } else{
            $status = 'Belum Diterima';
        }

        $gaji = GajiKaryawan::create([
            'uuid' => $this->generateUuid(),
            'karyawan_id' => $karyawan_id,
            'bulan' => $request->bulan,
            'tahun' => $tahun,
            'total_gaji_kotor' => $gajiKotor ?? 0,
            'total_utang' => $totalUtang ?? 0,
            'total_gaji_bersih' => $gajiBersih  ?? 0,
            'status' => $status,
        ]);

        return response([
            'message' => 'Berhasil Menghitung Gaji Karyawan',
            'data' => $gaji,
        ], 200);
    }

    public function updateStatus(Request $request, $id){
        $data = GajiKaryawan::where('uuid', $id)->first();

        if(is_null($data)){
            return response([
                'message' => 'Data Transaksi Kedai Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'status' => 'required',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $gajiData = collect($request)->only(GajiKaryawan::filters())->all();
        $data->update($gajiData);

        return response([
            'message' => 'Berhasil Mengubah Status Gaji Karyawan',
            'data' => $data,
        ], 200);
    }

    public function get($id){
        $data = GajiKaryawan::with(['karyawan'])->where('uuid', $id)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Tampil Data Gaji Karyawan Berhasil!',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Data Gaji Karyawan Tidak Ditemukan',
            'data' => null,
        ], 404);
    }

    public function getAll(Request $request){
        $karyawan = @$request->karyawan;

        if($karyawan){
            $data = GajiKaryawan::with(['karyawan'])
            ->whereHas('karyawan', function($q) use($karyawan){
                $q->where('nama', $karyawan);
            })->get();
        } else{
            $data = GajiKaryawan::with(['karyawan'])->get();
        }

        foreach($data as $gaji){
            $gaji->formatBulan = $this->fetchMonth($gaji->bulan);
        }

        $data = $data->sortByDesc('formatBulan')->sortByDesc('tahun')->values();

        return response([
            'message' => 'Tampil Data Gaji Karyawan Berhasil!',
            'data' => $data,
        ], 200);
    }

    private function fetchMonth($value){
        $month = '';
        switch ($value) {
            case 'Januari':
                $month = '01';
                break;
            case 'Februari':
                $month = '02';
                break;
            case 'Maret':
                $month = '03';
                break;
            case 'April':
                $month = '04';
                break;
            case 'Mei':
                $month = '05';
                break;
            case 'Juni':
                $month = '06';
                break;
            case 'Juli':
                $month = '07';
                break;
            case 'Agustus':
                $month = '08';
                break;
            case 'September':
                $month = '09';
                break;
            case 'Oktober':
                $month = '10';
                break;
            case 'November':
                $month = '11';
                break;
            case 'Desember':
                $month = '12';
                break;
            case 'Januari':
                $month = '01';
                break;
            default:
                # code...
                break;
        }
        return $month;
    }
}
