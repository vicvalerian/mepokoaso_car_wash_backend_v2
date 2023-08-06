<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuKedai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuKedaiController extends Controller
{
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
        $menuKedai = MenuKedai::create($menuKedaiData);

        return response([
            'message' => 'Berhasil Menambahkan Data Menu Kedai',
            'data' => $menuKedai,
        ], 200);
    }

    public function update(Request $request, $id){
        $data = MenuKedai::find($id);

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
        $data = MenuKedai::where('id', $id)->first();

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
        $data = MenuKedai::where('id', $id)->first();

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
        $jenis = @$request->jenis;

        if($jenis){
            $data = MenuKedai::where('jenis', $jenis)->orderBy("nama", "asc")->get();
        } else{
            $data = MenuKedai::orderBy("nama", "asc")->get();
        }

        return response([
            'message' => 'Tampil Data Menu Kedai Berhasil!',
            'data' => $data,
        ], 200);
    }

    public function listMenuKedai(){
        $list = [];
        $menus = MenuKedai::get();
        
        $list = $menus->transform(function($menu){
            return[
                'id' => $menu->id,
                'nama' => $menu->nama,
                'harga' => $menu->harga,
            ];
        });
        return $list;
    }
}
