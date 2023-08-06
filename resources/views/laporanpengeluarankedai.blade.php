<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
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
    <title>Laporan Pengeluaran Kedai</title>
</head>
<body>
    <h2>{{ $judul }} <br> {{ $subJudul }}</h2>
    <div class="table-wrapper">
        <table class="fl-table">
            <thead>
                <tr>
                    <th style="width: 50px">No</th>
                    <th style="width: 100px">Tanggal Pembelian</th>
                    <th style="width: 170px">Nama Barang</th>
                    <th>Jumlah Barang(pcs)</th>
                    <th>Harga Pembelian(Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $i=1 @endphp
                @foreach($files as $file)
                @php $harga = App\Http\Controllers\Api\LaporanController::formatRupiah($file->harga_pembelian) @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{date('d/m/Y',strtotime($file->tgl_pembelian))}}</td>
                    <td style="text-align: left">{{$file->nama_barang}}</td>
                    <td>{{$file->jumlah_barang}}</td>
                    <td style="text-align: right"> {{ $harga }} </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: center"><b>Total Pengeluaran</b></td>
                    @php $total = App\Http\Controllers\Api\LaporanController::formatRupiah($totalPengeluaran); @endphp
                    <td style="text-align: right"><b>Rp{{ $total }}</b></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>