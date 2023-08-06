<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengeluaranKedai;
use App\Models\TransaksiKedai;
use App\Models\TransaksiPencucian;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function generateLaporanPencucian(Request $request){
        $tglMulai = $request->tglMulai;
        $tglSelesai = $request->tglSelesai;

        $files = TransaksiPencucian::with(['kendaraan', 'karyawan_pencucis'])
        ->whereBetween('tgl_pencucian', [$tglMulai, $tglSelesai])
        ->where('status', 'Selesai')
        ->orderBy('tgl_pencucian', 'asc')
        ->get();

        $totalPendapatan = TransaksiPencucian::with(['kendaraan', 'karyawan_pencucis'])
        ->whereBetween('tgl_pencucian', [$tglMulai, $tglSelesai])
        ->where('status', 'Selesai')
        ->orderBy('tgl_pencucian', 'asc')
        ->sum('keuntungan');

        $data = [
            'judul' => 'Laporan Pencucian',
            'subJudul' => 'Mepokoaso Car Wash',
            'files' => $files,
            'totalPendapatan' => $totalPendapatan,
        ];
          
        $pdf = PDF::loadView('laporanpencucian', $data);
    
        return $pdf->stream('Laporan Transaksi Pencucian.pdf');
    }

    public function generateLaporanKedai(Request $request){
        $tglMulai = $request->tglMulai;
        $tglSelesai = $request->tglSelesai;

        $files = TransaksiKedai::with(['menu_kedai'])
        ->whereBetween('tgl_penjualan', [$tglMulai, $tglSelesai])
        ->orderBy('tgl_penjualan', 'asc')
        ->get();

        $totalPendapatan = TransaksiKedai::with(['menu_kedai'])
        ->whereBetween('tgl_penjualan', [$tglMulai, $tglSelesai])
        ->orderBy('tgl_penjualan', 'asc')
        ->sum('total_penjualan');

        $data = [
            'judul' => 'Laporan Penjualan Kedai',
            'subJudul' => 'Mepokoaso Car Wash',
            'files' => $files,
            'totalPendapatan' => $totalPendapatan,
        ];
          
        $pdf = PDF::loadView('laporantransaksikedai', $data);
    
        return $pdf->stream('Laporan Transaksi Kedai.pdf');
    }

    public function generateLaporanPengeluaranKedai(Request $request){
        $tglMulai = $request->tglMulai;
        $tglSelesai = $request->tglSelesai;

        $files = PengeluaranKedai::whereBetween('tgl_pembelian', [$tglMulai, $tglSelesai])        
        ->orderBy('tgl_pembelian', 'asc')
        ->get();

        $totalPengeluaran = PengeluaranKedai::whereBetween('tgl_pembelian', [$tglMulai, $tglSelesai])        
        ->orderBy('tgl_pembelian', 'asc')
        ->sum('harga_pembelian');

        $data = [
            'judul' => 'Laporan Pengeluaran Kedai',
            'subJudul' => 'Mepokoaso Car Wash',
            'files' => $files,
            'totalPengeluaran' => $totalPengeluaran,
        ];
          
        $pdf = PDF::loadView('laporanpengeluarankedai', $data);
    
        return $pdf->stream('Laporan Pengeluaran Kedai.pdf');
    }

    public static function formatRupiah($angka){
        $hasil_rupiah = number_format($angka,0,'','.');
        return $hasil_rupiah;
    }
}
