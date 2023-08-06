<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksiKedai;
use App\Models\MenuKedai;
use App\Models\TransaksiKedai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiKedaiController extends Controller
{
    private function generateNoPenjualan(){
        $type = "KEDAI";
        $currentTime = now()->format('dmy');
        $numberPrefix = $type.$currentTime.'-';
        $container = TransaksiKedai::where('no_penjualan','like',$numberPrefix.'%')->orderBy('no_penjualan','desc')->first();

        if($container){
            $counter = (int)(explode($numberPrefix,$container->no_penjualan)[1]) + 1;
            return $numberPrefix.sprintf('%03d', $counter);
        }

        return $numberPrefix.'001';
    }
    
    public function create(Request $request){
        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'karyawan_id' => 'required',
            // 'no_penjualan' => ['required', Rule::unique('transaksi_kedais')->whereNull('deleted_at')],
            'total_penjualan' => 'required|numeric',
            'tgl_penjualan' => 'required',
            'waktu_penjualan' => 'required',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $kedaiData = collect($request)->only(TransaksiKedai::filters())->all();
        $kedaiData['no_penjualan'] = $this->generateNoPenjualan();
        $kedaiMenus = collect($request->detail_transaksi_kedai)->map(function($menu) {
            return collect($menu)->only(DetailTransaksiKedai::filters())->all();
        });

        if($this->validateKedaiStok($kedaiMenus)){
            return response([
                'message' => 'Jumlah melebihi stok tersedia!'
            ], 400);
        }

        $transaksiKedai = TransaksiKedai::create($kedaiData);
        $transaksiKedai->detail_transaksi_kedais()->createMany($kedaiMenus);

        $this->calculateKedaiStokDekremen($kedaiMenus);

        return response([
            'message' => 'Berhasil Menambahkan Data Transaksi Kedai',
            'data' => $transaksiKedai,
        ], 200);
    }

    public function update(Request $request, $id){
        $data = TransaksiKedai::with(['detail_transaksi_kedais'])->where('id', $id)->first();
        $detailTransaksi = $data->detail_transaksi_kedais;

        if(is_null($data)){
            return response([
                'message' => 'Data Transaksi Kedai Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'karyawan_id' => 'required',
            // 'no_penjualan' => ['required', Rule::unique('transaksi_kedais')->ignore($data->id)->whereNull('deleted_at')],
            'total_penjualan' => 'required|numeric',
            'tgl_penjualan' => 'required',
            'waktu_penjualan' => 'required',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $kedaiData = collect($request)->only(TransaksiKedai::filters())->all();
        $kedaiMenus = collect($request->detail_transaksi_kedai)->map(function($menu) {
            return collect($menu)->only(DetailTransaksiKedai::filters())->all();
        });
        
        if($this->validateKedaiStok($kedaiMenus)){
            return response([
                'message' => 'Jumlah melebihi stok tersedia!'
            ], 400);
        }

        $data->update($kedaiData);
        $data->detail_transaksi_kedais()->delete();
        $data->detail_transaksi_kedais()->createMany($kedaiMenus);

        $this->calculateKedaiStokInkremen($detailTransaksi);
        $this->calculateKedaiStokDekremen($kedaiMenus);

        return response([
            'message' => 'Berhasil Mengubah Data Transaksi Kedai',
            'data' => $data,
        ], 200);
    }

    public function delete($id){
        $data = TransaksiKedai::with(['detail_transaksi_kedais'])->where('id', $id)->first();

        if(is_null($data)){
            return response([
                'message' => 'Data Transaksi Kedai Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        foreach($data->detail_transaksi_kedais as $detail_transaksi_kedai){
            $menuKedai = MenuKedai::find($detail_transaksi_kedai->menu_kedai_id);
            
            if($menuKedai && $menuKedai->is_stok){
                $stokKedai = $menuKedai->stok;
                $menuKedai->update([
                    'stok' => $stokKedai + $detail_transaksi_kedai->kuantitas,
                ]);
            }
        }

        $data->delete();
        $data->detail_transaksi_kedais()->delete();

        return response([
            'message' => 'Berhasil Menghapus Data Transaksi Kedai',
        ], 200);
    }

    public function get($id){
        $data = TransaksiKedai::with(['karyawan', 'detail_transaksi_kedais', 'menu_kedai'])->where('id', $id)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Tampil Data Transaksi Kedai Berhasil!',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Data Transaksi Kedai Tidak Ditemukan',
            'data' => null,
        ], 404);
    }

    public function getAll(){
        $data = TransaksiKedai::with(['karyawan', 'detail_transaksi_kedais', 'menu_kedai'])->orderBy("updated_at", "desc")->get();

        return response([
            'message' => 'Tampil Data Transaksi Kedai Berhasil!',
            'data' => $data,
        ], 200);
    }

    private function calculateKedaiStokDekremen($datas){
        foreach($datas as $data){
            $menuKedaiId = $data['menu_kedai_id'];
            $menuKedai = MenuKedai::find($menuKedaiId);

            if($menuKedai->is_stok){
                $stokKedai = $menuKedai->stok;
                $menuKedai->update([
                    'stok' => $stokKedai - $data['kuantitas']
                ]);
            }
        }
    }

    private function calculateKedaiStokInkremen($datas){
        foreach($datas as $data){
            $menuKedaiId = $data['menu_kedai_id'];
            $menuKedai = MenuKedai::find($menuKedaiId);
            
            if($menuKedai->is_stok){
                $stokKedai = $menuKedai->stok;
                $menuKedai->update([
                    'stok' => $stokKedai + $data['kuantitas']
                ]);
            }
        }
    }

    private function validateKedaiStok($datas){
        foreach($datas as $data){
            $menuKedaiId = $data['menu_kedai_id'];
            $menuKedai = MenuKedai::find($menuKedaiId);

            if($menuKedai->is_stok){
                $stokKedai = $menuKedai->stok;
                $requestStok = $data['kuantitas'];
    
                if($requestStok > $stokKedai){
                    return true;
                }
            }
        }

        return false;
    }

    public function getByMonthYearDashboard(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $datas = TransaksiKedai::select('tgl_penjualan', DB::raw('count(*) as total'))
        ->whereMonth('tgl_penjualan', $bulan)
        ->whereYear('tgl_penjualan', $tahun)
        ->groupBy('tgl_penjualan')
        ->orderBy('tgl_penjualan', 'asc')
        ->get();

        foreach($datas as $data){
            $tgl = strtotime($data->tgl_penjualan);
            $formatTgl = date('d', $tgl) .' '. date('M', $tgl);
            $data->formatTgl = $formatTgl;
        }

        return $datas;
    }
}
