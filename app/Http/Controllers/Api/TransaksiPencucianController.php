<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksiPencuci;
use App\Models\MobilPelanggan;
use App\Models\TransaksiPencucian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDF;
use Illuminate\Support\Str;

class TransaksiPencucianController extends Controller
{
    private function generateUuid(){
        $isDuplicate = true;
        $duplicateArr = TransaksiPencucian::pluck('uuid')->toArray();

        while($isDuplicate){
            $uuid = Str::orderedUuid()->toString();

            if(!in_array($uuid, $duplicateArr)){
                $isDuplicate = false;
            }
        }    
        
        return $uuid;
    }

    private function generateDetailUuid(){
        $isDuplicate = true;
        $duplicateArr = DetailTransaksiPencuci::pluck('uuid')->toArray();

        while($isDuplicate){
            $uuid = Str::orderedUuid()->toString();

            if(!in_array($uuid, $duplicateArr)){
                $isDuplicate = false;
            }
        }    
        
        return $uuid;
    }

    private function generateMobilPelangganUuid(){
        $isDuplicate = true;
        $duplicateArr = MobilPelanggan::pluck('uuid')->toArray();

        while($isDuplicate){
            $uuid = Str::orderedUuid()->toString();

            if(!in_array($uuid, $duplicateArr)){
                $isDuplicate = false;
            }
        }    
        
        return $uuid;
    }

    private function generateNoPencucian(){
        $type = "CUCI";
        $currentTime = now()->format('dmy');
        $numberPrefix = $type.$currentTime.'-';
        $container = TransaksiPencucian::where('no_pencucian','like',$numberPrefix.'%')->orderBy('no_pencucian','desc')->first();

        if($container){
            $counter = (int)(explode($numberPrefix,$container->no_pencucian)[1]) + 1;
            return $numberPrefix.sprintf('%03d', $counter);
        }

        return $numberPrefix.'001';
    }

    public function create(Request $request){
        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'kendaraan_id' => 'required',
            'karyawan_id' => 'required',
            // 'no_pencucian' => ['required', Rule::unique('transaksi_pencucians')->whereNull('deleted_at')],
            'no_polisi' => 'required',
            'jenis_kendaraan' => 'required',
            'tarif_kendaraan' => 'required|numeric',
            'tgl_pencucian' => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $pencucianData = collect($request)->only(TransaksiPencucian::filters())->all();
        $pencucianData['no_pencucian'] =  $this->generateNoPencucian();
        $pencucianData['uuid'] = $this->generateUuid();

        $jmlPencuci = count($request->detail_transaksi_pencuci);
        $upahPencuci = ($pencucianData['tarif_kendaraan'] * 0.35) / $jmlPencuci;

        $pencucianPencucis = collect($request->detail_transaksi_pencuci)->map(function($pencuci) use($upahPencuci) {
            $pencuci['upah_pencuci'] = $upahPencuci;
            $pencuci['uuid'] = $this->generateDetailUuid();
            return collect($pencuci)->only(DetailTransaksiPencuci::filters())->all();
        });

        $transaksiPencucian = TransaksiPencucian::create($pencucianData);
        $transaksiPencucian->detail_transaksi_pencucis()->createMany($pencucianPencucis);

        return response([
            'message' => 'Berhasil Menambahkan Data Transaksi Pencucian',
            'data' => $transaksiPencucian,
        ], 200);
    }

    public function update(Request $request, $id){
        $data = TransaksiPencucian::find($id);

        if(is_null($data)){
            return response([
                'message' => 'Data Transaksi Pencucian Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        if($data->status != 'Baru'){
            return response([
                'message' => 'Data Transaksi Pencucian Sudah Diproses!',
                'data' => null
            ], 400);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'kendaraan_id' => 'required',
            'karyawan_id' => 'required',
            // 'no_pencucian' => ['required', Rule::unique('transaksi_pencucians')->ignore($data->id)->whereNull('deleted_at')],
            'no_polisi' => 'required',
            'jenis_kendaraan' => 'required',
            'tarif_kendaraan' => 'required|numeric',
            'tgl_pencucian' => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()){
            return response([
                'message' => $validator->messages()->all()
            ], 400);
        }

        $pencucianData = collect($request)->only(TransaksiPencucian::filters())->all();

        $jmlPencuci = count($request->detail_transaksi_pencuci);
        $upahPencuci = ($pencucianData['tarif_kendaraan'] * 0.35) / $jmlPencuci;

        $pencucianPencucis = collect($request->detail_transaksi_pencuci)->map(function($pencuci) use($upahPencuci) {
            $pencuci['upah_pencuci'] = $upahPencuci;
            $pencuci['uuid'] = $this->generateDetailUuid();
            return collect($pencuci)->only(DetailTransaksiPencuci::filters())->all();
        });

        $data->update($pencucianData);
        $data->detail_transaksi_pencucis()->delete();
        $data->detail_transaksi_pencucis()->createMany($pencucianPencucis);

        return response([
            'message' => 'Berhasil Mengubah Data Transaksi Pencucian',
            'data' => $data,
        ], 200);
    }

    public function delete($id){
        $data = TransaksiPencucian::where('id', $id)->first();

        if(is_null($data)){
            return response([
                'message' => 'Data Transaksi Pencucian Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        if($data->status != 'Baru'){
            return response([
                'message' => 'Data Transaksi Pencucian Sudah Diproses!',
                'data' => null
            ], 400);
        }

        $data->delete();
        $data->detail_transaksi_pencucis()->delete();

        return response([
            'message' => 'Berhasil Menghapus Data Transaksi Pencucian',
        ], 200);
    }

    public function get($id){
        $data = TransaksiPencucian::with(['kendaraan', 'karyawan', 'detail_transaksi_pencucis', 'karyawan_pencucis'])->where('id', $id)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Tampil Data Transaksi Pencucian Berhasil!',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Data Transaksi Pencucian Tidak Ditemukan',
            'data' => null,
        ], 404);
    }

    public function getAll(Request $request){
        $status = @$request->status;

		if($status){
			$data = TransaksiPencucian::with(['kendaraan', 'karyawan', 'detail_transaksi_pencucis', 'karyawan_pencucis'])->orderBy("updated_at", "desc")->where('status', $status)->get();
		} else{
            $data = TransaksiPencucian::with(['kendaraan', 'karyawan', 'detail_transaksi_pencucis', 'karyawan_pencucis'])->orderBy("updated_at", "desc")->get();
        }

        return response([
            'message' => 'Tampil Data Transaksi Pencucian Berhasil!',
            'data' => $data,
        ], 200);
    }

    public function prosesCuci(Request $request){
        $transaksi = TransaksiPencucian::with(['kendaraan'])->where('id', $request->id)->first();

        if($transaksi->jenis_kendaraan == 'Mobil'){
            $jml_transaksi = TransaksiPencucian::where('no_polisi', $transaksi->no_polisi)->count();

            $mobilPelanggan = MobilPelanggan::where('no_polisi', $transaksi->no_polisi)->where('nama_kendaraan', $transaksi->kendaraan->nama)->first();
            if($mobilPelanggan){
                $mobilPelanggan->update([
                    'jml_transaksi' => $jml_transaksi,    
                ]);

                $transaksi->update(['mobil_pelanggan_id' => $mobilPelanggan->id]);
            } else{
                $createMobilPelanggan = MobilPelanggan::create([
                    'uuid' => $this->generateMobilPelangganUuid(),
                    'no_polisi' => $transaksi->no_polisi,
                    'nama_kendaraan' => $transaksi->kendaraan->nama,
                    'jml_transaksi' => $jml_transaksi,
                ]);

                $transaksi->update(['mobil_pelanggan_id' => $createMobilPelanggan->id]);
            }
        }

        $transaksi->update(['status' => 'Proses Cuci']);

        return response([
            'message' => 'Berhasil Mengubah Status Transaksi Pencucian',
        ], 200);
    }

    public function prosesBayar(Request $request){
        $transaksi = TransaksiPencucian::with(['kendaraan', 'mobil_pelanggan'])->where('id', $request->id)->first();

        if($transaksi->mobil_pelanggan){
            $mobilPelanggan = $transaksi->mobil_pelanggan;

            if(($mobilPelanggan->jml_transaksi % 6) == 0){
                $transaksi->update([
                    'is_free' => true,
                    'total_pembayaran' => 0,
                ]);
            } else{
                $transaksi->update([
                    'total_pembayaran' => $transaksi->tarif_kendaraan
                ]);
            }
        } else{
            $transaksi->update([
                'total_pembayaran' => $transaksi->tarif_kendaraan
            ]);
        }

        $transaksi->update(['status' => 'Proses Bayar']);

        return response([
            'message' => 'Berhasil Mengubah Status Transaksi Pencucian',
        ], 200);
    }

    public function finish(Request $request){
        $transaksi = TransaksiPencucian::with(['kendaraan'])->where('id', $request->id)->first();

        if($transaksi->is_free == true){
            $transaksi->update(['keuntungan' => 0]);
        } else if($transaksi->is_free == false){
            $keuntungan = $transaksi->tarif_kendaraan * 0.65;
            $transaksi->update(['keuntungan' => $keuntungan]);
        }

        $transaksi->update(['status' => 'Selesai']);

        return response([
            'message' => 'Berhasil Mengubah Status Transaksi Pencucian',
        ], 200);
    }

    public function cetakNotaPencucian($id){
        $transaksi = TransaksiPencucian::with(['kendaraan', 'karyawan', 'detail_transaksi_pencucis', 'karyawan_pencucis'])->where('id', $id)->first();
        $tglWaktu = date('d-m-Y');

        $data = [
            'judul' => 'Nota Pencucian',
            'subJudul' => 'Mepokoaso Car Wash',
            'transaksi' => $transaksi,
            'tglWaktu' => $tglWaktu,
            'diskon' => $transaksi->is_free ? $transaksi->tarif_kendaraan : 0,
        ];
          
        $pdf = PDF::loadView('notapencucian', $data);
    
        return $pdf->stream('Nota Pencucian.pdf');
    }

    public function getByMonthYearDashboard(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        $datas = TransaksiPencucian::select('tgl_pencucian', DB::raw('count(*) as total'))
        ->whereMonth('tgl_pencucian', $bulan)
        ->whereYear('tgl_pencucian', $tahun)
        ->groupBy('tgl_pencucian')
        ->orderBy('tgl_pencucian', 'asc')
        ->get();

        foreach($datas as $data){
            $tgl = strtotime($data->tgl_pencucian);
            $formatTgl = date('d', $tgl) .' '. date('M', $tgl);
            $data->formatTgl = $formatTgl;
        }

        return $datas;
    }
}
