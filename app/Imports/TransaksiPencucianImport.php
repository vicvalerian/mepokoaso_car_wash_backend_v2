<?php

namespace App\Imports;

use App\Models\DetailTransaksiPencuci;
use App\Models\Karyawan;
use App\Models\Kendaraan;
use App\Models\TransaksiPencucian;

use Illuminate\Support\Str;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TransaksiPencucianImport implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation
{
    use Importable;
    
    public function rules(): array
    {
        return [
            'kendaraan' => [
                'required',
            ],
            'harga' => [
                'required',
            ],
            'tanggal_pencucian' => [
                'required',
            ],
            'tanggal_lunas' => [
                'required',
            ],
            'pencuci' => [
                'required',
            ],
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $user = Karyawan::find(auth()->user()->id);
        $kendaraan = Kendaraan::where('nama', $row['kendaraan'])->first();
        $tarifKendaraan = $row['harga'] != 0 ? $row['harga'] : $kendaraan->harga;

        $pencucis = explode(',', $row['pencuci']);
        $jmlPencuci = count($pencucis);
        $upahPencuci = ($tarifKendaraan * 0.35) / $jmlPencuci;

        $tglPencucian = $this->excelTimeToDate($row['tanggal_pencucian']);
        $tglPelunasan = $row['tanggal_lunas'] != '-' ? $this->excelTimeToDate($row['tanggal_lunas']) : null;

        $keuntungan = 0;
        if($row['harga'] != 0){
            $keuntungan = $tarifKendaraan * 0.65;
        }

        $insertData = [
            'uuid' => $this->generateUuid(),
            'kendaraan_id' => $kendaraan->id,
            'karyawan_id' => $user->id,
            'no_pencucian' => $this->generateNoPencucian(),
            'jenis_kendaraan' => $kendaraan->tipe,
            'tarif_kendaraan' => $row['harga'] != 0 ? $row['harga'] : $kendaraan->harga,
            'tgl_pencucian' => $tglPencucian,
            'waktu_pencucian' => now()->format('H:i:s'),
            'status' => is_null($tglPelunasan) ? 'Belum Bayar' : 'Lunas',
            'total_pembayaran' => $row['harga'] != 0 ? $row['harga'] : 0,
            'is_free' => $row['harga'] == 0 ? true : false,
            'keuntungan' => $keuntungan,
            'created_at'=> now()->format('Y-m-d H:i:s'),
            'updated_at'=> now()->format('Y-m-d H:i:s'),
            'paid_at' => is_null($tglPelunasan) ? null : $tglPelunasan,
        ];

        $karyawanPencuci = [];
        foreach($pencucis as $pencuci){
            $karyawan = Karyawan::where('nama', $pencuci)->first();

            $insertDetail = [
                'uuid' => $this->generateDetailUuid(),
                'karyawan_id' => $karyawan->id,
                'upah_pencuci' => $upahPencuci,
            ];

            $karyawanPencuci[] = $insertDetail;
        }

        $transaksiPencucian = TransaksiPencucian::create($insertData);
        $transaksiPencucian->detail_transaksi_pencucis()->createMany($karyawanPencuci);

        return $transaksiPencucian;
    }

    private function excelTimeToDate($time)
    {
        $excelDate = (int) $time;
        $unixDate  = ($excelDate - 25569) * 86400;
        return gmdate("Y-m-d", $unixDate);
    }

    function generateNoPencucian(){
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

    function generateUuid(){
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

    function generateDetailUuid(){
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
}
