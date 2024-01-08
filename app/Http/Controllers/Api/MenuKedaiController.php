<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuKedai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MenuKedaiController extends Controller
{
    private function generateUuid(){
        $isDuplicate = true;
        $duplicateArr = MenuKedai::pluck('uuid')->toArray();

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
            'jenis' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $menuKedaiData = collect($request)->only(MenuKedai::filters())->all();
        $menuKedaiData['uuid'] = $this->generateUuid();
        $menuKedai = MenuKedai::create($menuKedaiData);

        return response([
            'message' => 'Berhasil Menambahkan Data Menu Kedai',
            'data' => $menuKedai,
        ], 200);
    }

    public function update(Request $request, $id){
        $data = MenuKedai::where('uuid', $id)->first();

        if(is_null($data)){
            return response([
                'message' => 'Data Menu Kedai Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'nama' => 'required',
            'jenis' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $menuKedaiData = collect($request)->only(MenuKedai::filters())->all();
        $data->update($menuKedaiData);

        return response([
            'message' => 'Berhasil Mengubah Data Menu Kedai',
            'data' => $data,
        ], 200);
    }

    public function delete($id){
        $data = MenuKedai::where('uuid', $id)->first();

        if(is_null($data)){
            return response([
                'message' => 'Data Menu Kedai Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $data->delete();

        return response([
            'message' => 'Berhasil Menghapus Data Menu Kedai',
        ], 200);
    }

    public function get($id){
        $data = MenuKedai::where('uuid', $id)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Tampil Data Menu Kedai Berhasil!',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Data Menu Kedai Tidak Ditemukan',
            'data' => null,
        ], 404);
    }

    public function getAll(Request $request){
        $per_page = (!is_null($request->per_page)) ? $request->per_page : 10;
        $keyword = $request->keyword;
        $data = MenuKedai::select()
        ->orderBy("updated_at", "desc");
        if($keyword){
            $data->where(function ($q) use ($keyword){
				$q->where('nama', "like", "%" . $keyword . "%");
				$q->orWhere('harga', "like", "%" . $keyword . "%");
				$q->orWhere('jenis', "like", "%" . $keyword . "%");
            });
        }

        return $data->paginate($per_page);
    }

    public function listMenuKedai(){
        $list = [];
        $menus = MenuKedai::where('is_stok', true)->get();
        
        $list = $menus->transform(function($menu){
            return[
                'id' => $menu->id,
                'nama' => $menu->nama,
                'harga' => $menu->harga,
            ];
        });
        return $list;
    }

    public function getAllByJenis(Request $request){
        $jenis = @$request->jenis;
        $keyword = $request->keyword;

        if($jenis){
            $data = MenuKedai::where('jenis', $jenis)->orderBy("nama", "asc");
        } else{
            $data = MenuKedai::orderBy("nama", "asc");
        }

        if($keyword){
            $data->where(function ($q) use ($keyword){
				$q->where('nama', "like", "%" . $keyword . "%");
            });
        }

        $data = $data->get();

        return response([
            'message' => 'Tampil Data Menu Kedai Berhasil!',
            'data' => $data,
        ], 200);
    }
}
