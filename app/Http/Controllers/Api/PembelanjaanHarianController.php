<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PembelanjaanHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PembelanjaanHarianController extends Controller
{
    private function generateUuid(){
        $isDuplicate = true;
        $duplicateArr = PembelanjaanHarian::pluck('uuid')->toArray();

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
            'tgl_belanja' => 'required',
            'nama' => 'required',
            'harga' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $pembelanjaanHarianData = collect($request)->only(PembelanjaanHarian::filters())->all();
        $pembelanjaanHarianData['uuid'] = $this->generateUuid();
        $pembelanjaanHarian = PembelanjaanHarian::create($pembelanjaanHarianData);

        return response([
            'message' => 'Berhasil Menambahkan Data Pembelanjaan Harian',
            'data' => $pembelanjaanHarian,
        ], 200);
    }

    public function update(Request $request, $id){
        $data = PembelanjaanHarian::where('uuid', $id)->first();

        if(is_null($data)){
            return response([
                'message' => 'Data Pembelanjaan Harian Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'tgl_belanja' => 'required',
            'nama' => 'required',
            'harga' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $pembelanjaanHarianData = collect($request)->only(PembelanjaanHarian::filters())->all();
        $data->update($pembelanjaanHarianData);

        return response([
            'message' => 'Berhasil Mengubah Data Pembelanjaan Harian',
            'data' => $data,
        ], 200);
    }

    public function delete($id){
        $data = PembelanjaanHarian::where('uuid', $id)->first();

        if(is_null($data)){
            return response([
                'message' => 'Data Pembelanjaan Harian Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $data->delete();

        return response([
            'message' => 'Berhasil Menghapus Data Pembelanjaan Harian',
        ], 200);
    }

    public function get($id){
        $data = PembelanjaanHarian::where('uuid', $id)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Tampil Data Pembelanjaan Harian Berhasil!',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Data Pembelanjaan Harian Tidak Ditemukan',
            'data' => null,
        ], 404);
    }

    public function getAll(Request $request){

        $per_page = (!is_null($request->per_page)) ? $request->per_page : 10;
        $keyword = $request->keyword;

        $data = PembelanjaanHarian::select()
        ->orderBy("updated_at", "desc");

        if($keyword){
            $data->where(function ($q) use ($keyword){
				$q->where('nama', "like", "%" . $keyword . "%");
				$q->orWhere('harga', "like", "%" . $keyword . "%");
				$q->orWhere('tgl_belanja', "like", "%" . $keyword . "%");
            });
        }

        return $data->paginate($per_page);
    }
}
