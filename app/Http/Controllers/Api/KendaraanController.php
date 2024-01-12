<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisKendaraan;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class KendaraanController extends Controller
{
    private function generateUuid(){
        $isDuplicate = true;
        $duplicateArr = Kendaraan::pluck('uuid')->toArray();

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
            'jenis_kendaraan_id' => 'required',
            'nama' => 'required',
            'harga' => 'required|numeric',
            'foto' => 'required|mimes:jpeg,bmp,png',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $kendaraanData = collect($request)->only(Kendaraan::filters())->all();
        
        $image_name = 'gambar'.\Str::random(5).str_replace(' ', '', $kendaraanData['nama']).\Str::random(5);
        $file = $kendaraanData['foto'];
        $extension = $file->getClientOriginalExtension();

        $uploadDoc = $request->foto->storeAs(
            'img_kendaraan',
            $image_name.'.'.$extension,
            ['disk' => 'public']
        );

        $kendaraanData['foto'] = $uploadDoc;
        $kendaraanData['uuid'] = $this->generateUuid();

        $kendaraan = Kendaraan::create($kendaraanData);

        return response([
            'message' => 'Berhasil Menambahkan Data Kendaraan',
            'data' => $kendaraan,
        ], 200);
    }

    public function update(Request $request, $id){
        $data = Kendaraan::where('uuid', $id)->first();
        
        if(is_null($data)){
            return response([
                'message' => 'Data Kendaraan Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();

        $validator = Validator::make($updateData, [
            'jenis_kendaraan_id' => 'required',
            'nama' => 'required',
            'harga' => 'required|numeric',
            'foto' => 'nullable|mimes:jpeg,bmp,png',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $kendaraanData = collect($request)->only(Kendaraan::filters())->all();

        if(isset($request->foto)){
            if(isset($data->foto)){
                Storage::delete("public/".$data->foto);
            }
            $image_name = 'gambar'.\Str::random(5).str_replace(' ', '', $kendaraanData['nama']).\Str::random(5);
            $file = $kendaraanData['foto'];
            $extension = $file->getClientOriginalExtension();

            $uploadDoc = $request->foto->storeAs(
                'img_kendaraan',
                $image_name.'.'.$extension,
                ['disk' => 'public']
            );
    
            $kendaraanData['foto'] = $uploadDoc;
        }

        $data->update($kendaraanData);

        return response([
            'message' => 'Berhasil Mengubah Data Kendaraan',
            'data' => $data,
        ], 200);
    }

    public function delete($id){
        $data = Kendaraan::where('uuid', $id)->first();

        if(is_null($data)){
            return response([
                'message' => 'Data Kendaraan Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $data->delete();

        return response([
            'message' => 'Berhasil Menghapus Data Kendaraan',
        ], 200);
    }

    public function get($id){
        $data = Kendaraan::with(['jenis_kendaraan'])->where('uuid', $id)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Tampil Data Kendaraan Berhasil!',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Data Kendaraan Tidak Ditemukan',
            'data' => null,
        ], 404);
    }

    public function getAll(Request $request){
        $per_page = (!is_null($request->per_page)) ? $request->per_page : 10;
        $keyword = $request->keyword;

        $data = Kendaraan::with(['jenis_kendaraan'])->select()
        ->orderBy("updated_at", "desc");

        if($keyword){
            $data->where(function ($q) use ($keyword){
				$q->where('nama', "like", "%" . $keyword . "%");
                $q->orWhereHas('jenis_kendaraan', function($qq) use($keyword){
                    $qq->where('nama', "like", "%" . $keyword . "%");
                });
            });
        }

        return $data->paginate($per_page);
    }

    public function listKendaraan(){
        $list = [];
        $kendaraans = Kendaraan::all();
        
        $list = $kendaraans->transform(function($kendaraan){
            return[
                'id' => $kendaraan->id,
                'uuid' => $kendaraan->uuid,
                'nama' => $kendaraan->nama,
                'harga' => $kendaraan->harga,
                'tipe' => $kendaraan->tipe,
            ];
        });
        return $list;
    }
}
