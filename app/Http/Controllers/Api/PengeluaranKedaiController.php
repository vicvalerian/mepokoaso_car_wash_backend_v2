<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuKedai;
use App\Models\PengeluaranKedai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PengeluaranKedaiController extends Controller
{
    private function generateUuid(){
        $isDuplicate = true;
        $duplicateArr = PengeluaranKedai::pluck('uuid')->toArray();

        while($isDuplicate){
            $uuid = Str::orderedUuid()->toString();

            if(!in_array($uuid, $duplicateArr)){
                $isDuplicate = false;
            }
        }    
        
        return $uuid;
    }

    public function create(Request $request){
        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'menu_kedai_id' => 'nullable',
            'nama_barang' => 'required',
            'tgl_pembelian' => 'required',
            'jumlah_barang' => 'required|numeric',
            'harga_pembelian' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $pengeluaranKedaiData = collect($request)->only(PengeluaranKedai::filters())->all();
        $pengeluaranKedaiData['uuid'] = $this->generateUuid();

        if(isset($request->menu_kedai_id)){
            $menuKedai = MenuKedai::find($request->menu_kedai_id);
            if($menuKedai->is_stok){
                $stokKedai = $menuKedai->stok;
                $menuKedai->update([
                    'stok' => $stokKedai + $pengeluaranKedaiData['jumlah_barang']
                ]);
            }
        }

        $pengeluaranKedai = PengeluaranKedai::create($pengeluaranKedaiData);

        return response([
            'message' => 'Berhasil Menambahkan Data Pengeluaran Kedai',
            'data' => $pengeluaranKedai,
        ], 200);
    }

    public function update(Request $request, $id){
        $data = PengeluaranKedai::where('uuid', $id)->first();

        if(is_null($data)){
            return response([
                'message' => 'Data Pengeluaran Kedai Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'menu_kedai_id' => 'nullable',
            'nama_barang' => 'required',
            'tgl_pembelian' => 'required',
            'jumlah_barang' => 'required|numeric',
            'harga_pembelian' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $pengeluaranKedaiData = collect($request)->only(PengeluaranKedai::filters())->all();

        if(!is_null($data->menu_kedai_id)){
            $menuKedai = MenuKedai::find($data->menu_kedai_id);

            if($menuKedai->is_stok){
                $stokKedai = $menuKedai->stok;

                $oldStok = $data->jumlah_barang;
                $menuKedai->update([
                    'stok' => $stokKedai - $oldStok
                ]);
    
                $menuKedaiNew = MenuKedai::find($request->menu_kedai_id);
                $stokKedaiNew = $menuKedaiNew->stok;
                $menuKedaiNew->update([
                    'stok' => $stokKedaiNew + $pengeluaranKedaiData['jumlah_barang']
                ]);
            }
        }

        $data->update($pengeluaranKedaiData);

        return response([
            'message' => 'Berhasil Mengubah Data Pengeluaran Kedai',
            'data' => $data,
        ], 200);
    }

    public function delete($id){
        $data = PengeluaranKedai::where('uuid', $id)->first();

        if(is_null($data)){
            return response([
                'message' => 'Data Pengeluaran Kedai Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $data->delete();

        return response([
            'message' => 'Berhasil Menghapus Data Pengeluaran Kedai',
        ], 200);
    }

    public function get($id){
        $data = PengeluaranKedai::with(['menu_kedai'])->where('uuid', $id)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Tampil Data Pengeluaran Kedai Berhasil!',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Data Pengeluaran Kedai Tidak Ditemukan',
            'data' => null,
        ], 404);
    }

    public function getAll(){

        $data = PengeluaranKedai::with(['menu_kedai'])->get();

        return response([
            'message' => 'Tampil Data Pengeluaran Kedai Berhasil!',
            'data' => $data,
        ], 200);
    }
}
