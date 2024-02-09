<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        br {
            display: block;
            content: ""; /* clears default height */
            margin-top: 2; /* change this to whatever height you want it */
        }
        h2, h3{
            text-align: center;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #316291;
            padding: 30px 0;
        }

        h3{
            text-align: left;
            font-size: 16px;
            padding: 0;
            letter-spacing: -0.1px;
        }

        .margin-top-64{
            margin-top: 64px;
        }

        .table-wrapper{
            box-shadow: 0px 35px 50px rgba( 0, 0, 0, 0.2 );
            width: 100%;
            margin-left:auto; 
            margin-right:auto;
        }

        .fl-table {
            border-radius: 5px;
            font-size: 14px;
            font-weight: normal;
            border: none;
            border-collapse: collapse;
            width: 100%;
            max-width: 100%;
            white-space: nowrap;
            background-color: white;
        }

        .fl-table td, .fl-table th {
            text-align: center;
            padding: 8px;
        }

        .fl-table td {
            border-right: 1px solid #f8f8f8;
            font-size: 14px;
        }

        .fl-table thead th {
            color: #ffffff;
            background: #274E74;
        }


        .fl-table thead th:nth-child(odd) {
            color: #ffffff;
            background: #316291;
        }

        .fl-table tr:nth-child(odd) {
            background: #E0E0E0;
        }

        .fl-table tr:nth-child(even) {
            background: #FCFDEF;
        }

        .fl-table tfoot td {
            color: #ffffff;
            background: #316291;
        }
    </style>
    <title>Laporan Pencucian</title>
</head>
<body>
    <h2>{{ $judul }} <br> {{ $subJudul }}</h2>
    <div class="table-wrapper">
        {{-- Laporan Pencucian Kendaraan --}}
        <h3>Laporan Kendaraan</h3>
        <table class="fl-table">
            <thead>
                <tr>
                    <th style="width: 50px">Nomor Transaksi</th>
                    {{-- <th style="width: 50px">No</th> --}}
                    <th style="width: 120px">Tanggal Pencucian</th>
                    <th>Nama Kendaraan</th>
                    <th>Pencuci</th>
                    <th>Total(Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $i=1 @endphp
                @foreach($files as $file)
                <tr>
                    <td style="text-align: left">{{ $file->no_pencucian }}</td>
                    {{-- <td>{{ $i++ }}</td> --}}
                    <td>{{date('d/m/Y',strtotime($file->tgl_pencucian))}} <br> {{$file->waktu_pencucian}} WITA</td>
                    <td style="text-align: left">{{$file->kendaraan->nama}}</td>

                    @php
                    echo '<td style="text-align: left">';
                        foreach($file->karyawan_pencucis as $pencuci){
                            echo $pencuci->nama . '<br/>';
                        }
                    echo '</td>';
                    @endphp

                    @php 
                        $pembayaran = App\Http\Controllers\Api\LaporanController::formatRupiah($file->total_pembayaran ?? 0);
                    @endphp
                    <td style="text-align: right">{{$pembayaran}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: center"><b>Total Pendapatan</b></td>
                    @php $total = App\Http\Controllers\Api\LaporanController::formatRupiah($totalPendapatan) @endphp
                    <td style="text-align: right"><b>Rp{{ $total }}</b></td>
                </tr>
            </tfoot>
        </table>

        {{-- Laporan Pembelanjaan --}}
        <h3 class="margin-top-64">Laporan Pembelanjaan</h3>
        <table class="fl-table">
            <thead>
                <tr>
                    <th style="width: 50px">No</th>
                    <th style="width: 120px">Tanggal Belanja</th>
                    <th>Nama Barang</th>
                    <th>Harga(Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $i=1 @endphp
                @foreach($expenses as $expense)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{date('d/m/Y',strtotime($expense->tgl_belanja))}}</td>
                    <td style="text-align: left">{{$expense->nama}}</td>

                    @php 
                        $harga = App\Http\Controllers\Api\LaporanController::formatRupiah($expense->harga ?? 0);
                    @endphp
                    <td style="text-align: right">{{$harga}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: center"><b>Total Pengeluaran</b></td>
                    @php $total = App\Http\Controllers\Api\LaporanController::formatRupiah($totalPengeluaran) @endphp
                    <td style="text-align: right"><b>Rp{{ $total }}</b></td>
                </tr>
            </tfoot>
        </table>

        {{-- Laporan Peminjaman --}}
        <h3 class="margin-top-64">Laporan Peminjaman</h3>
        <table class="fl-table">
            <thead>
                <tr>
                    <th style="width: 50px">No</th>
                    <th style="width: 120px">Tanggal Peminjaman</th>
                    <th>Nama Karyawan</th>
                    <th>Nominal Peminjaman(Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $i=1 @endphp
                @foreach($loans as $loan)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{date('d/m/Y',strtotime($loan->tgl_peminjaman))}} WITA</td>
                    <td style="text-align: left">{{$loan->karyawan->nama}}</td>

                    @php 
                        $nominal = App\Http\Controllers\Api\LaporanController::formatRupiah($loan->nominal ?? 0);
                    @endphp
                    <td style="text-align: right">{{$nominal}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: center"><b>Total Peminjaman</b></td>
                    @php $total = App\Http\Controllers\Api\LaporanController::formatRupiah($totalPeminjaman) @endphp
                    <td style="text-align: right"><b>Rp{{ $total }}</b></td>
                </tr>
            </tfoot>
        </table>

        {{-- Total Keseluruhan --}}
        <h3 class="margin-top-64">Total Keseluruhan</h3>
        <table class="fl-table">
            <thead>
                <tr>
                    <th style="width: 50px">No</th>
                    <th>Nama Pemasukan / Pengeluaran</th>
                    <th>Nominal(Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>A</td>
                    <td style="text-align: left">Total Pencucian Kendaraan</td>
                    @php $totalPendapatanRupiah = App\Http\Controllers\Api\LaporanController::formatRupiah($totalPendapatan) @endphp
                    <td style="text-align: right">{{ $totalPendapatanRupiah }}</td>
                </tr>
                <tr>
                    <td>B</td>
                    <td style="text-align: left">Total Pembelanjaan</td>
                    @php $totalPengeluaranRupiah = App\Http\Controllers\Api\LaporanController::formatRupiah($totalPengeluaran) @endphp
                    <td style="text-align: right">{{ $totalPengeluaranRupiah }}</td>
                </tr>
                <tr>
                    <td>C</td>
                    <td style="text-align: left">Total Peminjaman</td>
                    @php $totalPeminjamanRupiah = App\Http\Controllers\Api\LaporanController::formatRupiah($totalPeminjaman) @endphp
                    <td style="text-align: right">{{ $totalPeminjamanRupiah }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: center"><b>Total Keseluruhan ( A - B - C )</b></td>
                    @php 
                        $totalSemua = $totalPendapatan - $totalPengeluaran - $totalPeminjaman;
                        $totalKeseluruhan = App\Http\Controllers\Api\LaporanController::formatRupiah($totalSemua) 
                    @endphp
                    <td style="text-align: right"><b>Rp{{ $totalKeseluruhan }}</b></td>
                </tr>
            </tfoot>
        </table>        

    </div>
</body>
</html>