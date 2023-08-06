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
        h2{
            text-align: center;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #316291;
            padding: 30px 0;
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
    <title>Laporan Transaksi Kedai</title>
</head>
<body>
    <h2>{{ $judul }} <br> {{ $subJudul }}</h2>
    <div class="table-wrapper">
        <table class="fl-table">
            <thead>
                <tr>
                    <th style="width: 50px">Nomor Transaksi</th>
                    {{-- <th style="width: 50px">No</th> --}}
                    <th style="width: 120px">Tanggal Penjualan</th>
                    <th>Nama Menu</th>
                    <th>Kuantitas</th>
                    <th style="width: 60px">Subtotal(Rp)</th>
                    <th>Total(Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $i=1 @endphp
                @foreach($files as $file)
                <tr>
                    <td style="text-align: left;">{{ $file->no_penjualan }}</td>
                    {{-- <td>{{ $i++ }}</td> --}}
                    <td>{{date('d/m/Y',strtotime($file->tgl_penjualan))}} <br> {{$file->waktu_penjualan}} WITA</td>

                    @php
                    echo '<td style="text-align: left;">';
                        foreach($file->menu_kedai as $menu){
                            echo $menu->nama . '<br/>';
                        }
                    echo '</td>';
                    @endphp

                    @php
                    echo '<td>';
                        foreach($file->menu_kedai as $menu){
                            echo $menu->pivot->kuantitas . '<br/>';
                        }
                    echo '</td>';
                    @endphp

                    @php
                    echo '<td style="text-align: right;">';
                        foreach($file->menu_kedai as $menu){
                            $sub_total = App\Http\Controllers\Api\LaporanController::formatRupiah($menu->pivot->sub_total);
                            echo $sub_total . '<br/>';
                        }
                    echo '</td>';
                    @endphp

                    @php $total_penjualan = App\Http\Controllers\Api\LaporanController::formatRupiah($file->total_penjualan) @endphp
                    <td  style="text-align: right;">{{$total_penjualan}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: center"><b>Total Pendapatan</b></td>
                    @php $total = App\Http\Controllers\Api\LaporanController::formatRupiah($totalPendapatan) @endphp
                    <td style="text-align: right"><b>Rp{{ $total }}</b></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>