<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Imports\PembelanjaanHarianImport;
use App\Imports\TransaksiPencucianImport;

use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class ExcelController extends Controller
{
    public function downloadDocumentSample(Request $request){
        if ($request->type_doc == 'PENCUCIAN') {
            $file= public_path(). "/sample-import/contoh-pencucian-kendaraan.xlsx";

            $headers = array(
              'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            );

            return response()->download($file, 'contoh-pencucian-kendaraan.xlsx', $headers);
        }

        if ($request->type_doc == 'PEMBELANJAANHARIAN') {
            $file= public_path(). "/sample-import/contoh-pembelanjaan-harian.xlsx";

            $headers = array(
              'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            );

            return response()->download($file, 'contoh-pembelanjaan-harian.xlsx', $headers);
        }
    }

    public function importTransaksiPencucianDoc(Request $request){
        if (is_null($request->file('csv'))) {
            return response("Impor Gagal, File Tidak Tersedia!", 400);
        }

        $directory = "import/";

        $filename = $request->file('csv')->getClientOriginalName();
        $request->file('csv')->storeAs($directory, $filename);
        $filePath = $directory . $filename;

        Excel::import(new TransaksiPencucianImport, $filePath);
    }

    public function importPembelanjaanHarianDoc(Request $request){
        if (is_null($request->file('csv'))) {
            return response("Impor Gagal, File Tidak Tersedia!", 400);
        }

        $directory = "import/";

        $filename = $request->file('csv')->getClientOriginalName();
        $request->file('csv')->storeAs($directory, $filename);
        $filePath = $directory . $filename;

        Excel::import(new PembelanjaanHarianImport, $filePath);
    }
}
