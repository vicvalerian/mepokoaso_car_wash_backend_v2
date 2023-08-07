<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        if(auth()->guard('karyawan')->attempt(['username' => request('username'), 'password' => request('password')])){
            config(['auth.guards.api.provider' => 'karyawan']);
  
            $karyawan = Karyawan::with('jabatan')->select('karyawans.*')->find(auth()->guard('karyawan')->user()->id);
            $success =  $karyawan;
            $success['token'] =  $karyawan->createToken('Authentication Token')->accessToken; 

            return response([
                'message' => 'Login Berhasil!',
                'data' => $success
            ], 200);
        } else{
            return response([
                'message' => 'Oopss, kata sandi Anda salah',
                'data' => null
            ], 400);
        }
    }

    public function logout(){
        $accessToken = auth()->user()->token();
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update([
                'revoked' => true
            ]);

        $accessToken->revoke();
        return response(['message' => 'Logout berhasil'], 200);
    }
}
