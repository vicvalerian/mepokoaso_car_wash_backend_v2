<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $loginData = $request->all();
        $validator = Validator::make($loginData, [
            'username' => 'required',
            'password' => 'required'
        ]);
    
        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $username = $request->username;
        $password = $request->password;
        $user = Karyawan::with('jabatan')->where('username', $username)->first();

        if(!$user){
            return response([
                'message' => 'Data Pengguna Tidak Ditemukan!',
                'data' => null
            ], 404);
        }

        if($user->jabatan->nama == 'Pencuci'){
            return response([
                'message' => 'Maaf, Anda Tidak Memiliki Akses Ke Dalam Sistem!',
                'data' => null
            ], 400);
        }

        if($user->status != 'Aktif'){
            return response([
                'message' => 'Maaf, Status Anda Tidak Aktif!',
                'data' => null
            ], 400);
        }

        $checkedPass = Hash::check($password, $user->password);
        if(!$checkedPass){
            return response([
                'message' => 'Password Anda Salah!',
                'data' => null
            ], 400);
        }

        return response([
            'message' => 'Login Berhasil!',
            'data' => $user
        ], 200);
    }
}
