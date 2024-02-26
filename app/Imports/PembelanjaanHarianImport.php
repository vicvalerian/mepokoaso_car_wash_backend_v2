<?php

namespace App\Imports;

use App\Models\PembelanjaanHarian;

use Illuminate\Support\Str;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PembelanjaanHarianImport implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation
{
    use Importable;
    
    public function rules(): array
    {
        return [
            'tanggal_pembelanjaan' => [
                'required',
            ],
            'nama_barang' => [
                'required',
            ],
            'harga' => [
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
        return new PembelanjaanHarian([
            'uuid' => $this->generateUuid(),
            'tgl_belanja' => $this->excelTimeToDate($row['tanggal_pembelanjaan']),
            'nama' => $row['nama_barang'],
            'harga' => $row['harga'],
        ]);
    }

    private function excelTimeToDate($time)
    {
        $excelDate = (int) $time;
        $unixDate  = ($excelDate - 25569) * 86400;
        return gmdate("Y-m-d", $unixDate);
    }

    function generateUuid(){
        $isDuplicate = true;
        $duplicateArr = PembelanjaanHarian::pluck('uuid')->toArray();

        while($isDuplicate){
            $uuid = Str::orderedUuid()->toString();

            if(!in_array($uuid, $duplicateArr)){
                $isDuplicate = false;
            }
        }    
        
        return $uuid;
    }
}
