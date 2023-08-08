<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JenisKendaraanController extends Controller
{
    private function generateUuid(){
        $isDuplicate = true;
        $duplicateArr = JenisKendaraan::pluck('uuid')->toArray();

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
            'nama' => 'required',
            'logo' => 'required|mimes:jpeg,bmp,png',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $jenisKendaraanData = collect($request)->only(JenisKendaraan::filters())->all();
        
        $image_name = 'logo'.\Str::random(5).str_replace(' ', '', $jenisKendaraanData['nama']).\Str::random(5);
        $file = $jenisKendaraanData['logo'];
        $extension = $file->getClientOriginalExtension();

        $uploadDoc = $request->logo->storeAs(
            'logo_jenis_kendaraan',
            $image_name.'.'.$extension,
            ['disk' => 'public']
        );

        $jenisKendaraanData['logo'] = $uploadDoc;
        $jenisKendaraanData['uuid'] = $this->generateUuid();

        $jenisKendaraan = JenisKendaraan::create($jenisKendaraanData);

        return response([
            'message' => 'Berhasil Menambahkan Data Jenis Kendaraan',
            'data' => $jenisKendaraan,
        ], 200);
    }

    public function update(Request $request, $id){
        $data = JenisKendaraan::where('uuid', $id)->first();
        
        if(is_null($data)){
            return response([
                'message' => 'Data Jenis Kendaraan Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();

        $validator = Validator::make($updateData, [
            'nama' => 'required',
            'logo' => 'nullable|mimes:jpeg,bmp,png',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $jenisKendaraanData = collect($request)->only(JenisKendaraan::filters())->all();

        if(isset($request->logo)){
            if(isset($data->logo)){
                Storage::delete("public/".$data->logo);
            }
            $image_name = 'logo'.\Str::random(5).str_replace(' ', '', $jenisKendaraanData['nama']).\Str::random(5);
            $file = $jenisKendaraanData['logo'];
            $extension = $file->getClientOriginalExtension();

            $uploadDoc = $request->logo->storeAs(
                'logo_jenis_kendaraan',
                $image_name.'.'.$extension,
                ['disk' => 'public']
            );
    
            $jenisKendaraanData['logo'] = $uploadDoc;
        }

        $data->update($jenisKendaraanData);

        return response([
            'message' => 'Berhasil Mengubah Data Jenis Kendaraan',
            'data' => $data,
        ], 200);
    }

    public function delete($id){
        $data = JenisKendaraan::where('uuid', $id)->first();

        if(is_null($data)){
            return response([
                'message' => 'Data Jenis Kendaraan Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $data->delete();

        return response([
            'message' => 'Berhasil Menghapus Data Jenis Kendaraan',
        ], 200);
    }

    public function get($id){
        $data = JenisKendaraan::where('uuid', $id)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Tampil Data Jenis Kendaraan Berhasil!',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Data Jenis Kendaraan Tidak Ditemukan',
            'data' => null,
        ], 404);
    }

    public function getAll(){
        $data = JenisKendaraan::get();

        return response([
            'message' => 'Tampil Data Jenis Kendaraan Berhasil!',
            'data' => $data,
        ], 200);
    }

    public function listJenisKendaraan(){
        $list = [];
        $jenisKendaraans = JenisKendaraan::all();
        
        $list = $jenisKendaraans->transform(function($jenisKendaraan){
            return[
                'id' => $jenisKendaraan->id,
                'nama' => $jenisKendaraan->nama
            ];
        });
        return $list;
    }

    public function cardListJenisKendaraan(){
        $jenisKendaraans = JenisKendaraan::orderByRaw("FIELD(`nama`, 'Motor') ASC, `nama`")
        ->get();

        return $jenisKendaraans;
    }
}
