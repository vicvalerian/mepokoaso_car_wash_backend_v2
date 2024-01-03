<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PeminjamanKaryawanController extends Controller
{
    private function generateUuid(){
        $isDuplicate = true;
        $duplicateArr = PeminjamanKaryawan::pluck('uuid')->toArray();

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
            'karyawan_id' => 'required',
            'tgl_peminjaman' => 'required',
            'nominal' => 'required|numeric',
            // 'alasan' => 'required',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $peminjamanKaryawanData = collect($request)->only(PeminjamanKaryawan::filters())->all();
        $peminjamanKaryawanData['uuid'] = $this->generateUuid();
        $peminjamanKaryawan = PeminjamanKaryawan::create($peminjamanKaryawanData);

        return response([
            'message' => 'Berhasil Menambahkan Data Peminjaman Karyawan',
            'data' => $peminjamanKaryawan,
        ], 200);
    }

    public function update(Request $request, $id){
        $data = PeminjamanKaryawan::where('uuid', $id)->first();

        if(is_null($data)){
            return response([
                'message' => 'Data Peminjaman Karyawan Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'karyawan_id' => 'required',
            'tgl_peminjaman' => 'required',
            'nominal' => 'required|numeric',
            // 'alasan' => 'required',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $peminjamanKaryawanData = collect($request)->only(PeminjamanKaryawan::filters())->all();
        $data->update($peminjamanKaryawanData);

        return response([
            'message' => 'Berhasil Mengubah Data Peminjaman Karyawan',
            'data' => $data,
        ], 200);
    }

    public function delete($id){
        $data = PeminjamanKaryawan::with(['karyawan'])->where('uuid', $id)->first();

        if(is_null($data)){
            return response([
                'message' => 'Data Peminjaman Karyawan Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $data->delete();

        return response([
            'message' => 'Berhasil Menghapus Data Peminjaman Karyawan',
        ], 200);
    }

    public function get($id){
        $data = PeminjamanKaryawan::with(['karyawan'])->where('uuid', $id)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Tampil Data Peminjaman Karyawan Berhasil!',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Data Peminjaman Karyawan Tidak Ditemukan',
            'data' => null,
        ], 404);
    }

    public function getAll(Request $request){
        $per_page = (!is_null($request->per_page)) ? $request->per_page : 10;
        $keyword = $request->keyword;

        $data = PeminjamanKaryawan::with(['karyawan'])->select()
        ->orderBy("updated_at", "desc");

        if($keyword){
            $data->where(function ($q) use ($keyword){
				$q->where('tgl_peminjaman', "like", "%" . $keyword . "%");
				$q->orWhere('nominal', "like", "%" . $keyword . "%");
                $q->orWhereHas('karyawan', function($qq) use($keyword){
                    $qq->where('nama', "like", "%" . $keyword . "%");
                });
            });
        }

        return $data->paginate($per_page);
    }
}
