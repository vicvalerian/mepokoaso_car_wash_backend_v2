<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class JabatanController extends Controller
{
    private function generateUuid(){
        $isDuplicate = true;
        $duplicateArr = Jabatan::pluck('uuid')->toArray();

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
            'nama' => ['required', Rule::unique('jabatans')->whereNull('deleted_at')],
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $jabatanData = collect($request)->only(Jabatan::filters())->all();
        $jabatanData['uuid'] = $this->generateUuid();
        $jabatan = Jabatan::create($jabatanData);

        return response([
            'message' => 'Berhasil Menambahkan Data Jabatan',
            'data' => $jabatan,
        ], 200);
    }

    public function update(Request $request, $id){
        $data = Jabatan::find($id);

        if(is_null($data)){
            return response([
                'message' => 'Data Jabatan Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'nama' => ['required', Rule::unique('jabatans')->ignore($data->id)->whereNull('deleted_at')],
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $jabatanData = collect($request)->only(Jabatan::filters())->all();
        $data->update($jabatanData);

        return response([
            'message' => 'Berhasil Mengubah Data Jabatan',
            'data' => $data,
        ], 200);
    }

    public function delete($id){
        $data = Jabatan::find($id);

        if(is_null($data)){
            return response([
                'message' => 'Data Jabatan Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $data->delete();

        return response([
            'message' => 'Berhasil Menghapus Data Jabatan',
        ], 200);
    }

    public function get($id){
        $data = Jabatan::find($id);

        if(!is_null($data)){
            return response([
                'message' => 'Tampil Data Jabatan Berhasil!',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Data Jabatan Tidak Ditemukan',
            'data' => null,
        ], 404);
    }

    public function getAll(){
        $data = Jabatan::get();

        return response([
            'message' => 'Tampil Data Jabatan Berhasil!',
            'data' => $data,
        ], 200);
    }

    public function listJabatan(){
        $list = [];
        $jabatans = Jabatan::all();
        
        $list = $jabatans->transform(function($jabatan){
            return[
                'id' => $jabatan->id,
                'nama' => $jabatan->nama
            ];
        });
        return $list;
    }
}
