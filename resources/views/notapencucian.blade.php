<!DOCTYPE html>
<html lang="en">
	<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Nota Transaksi Pencucian</title>
		<link rel="license" href="https://www.opensource.org/licenses/mit-license/">
        <style>
            /* reset */
            *
            {
                border: 0;
                box-sizing: content-box;
                color: inherit;
                font-family: inherit;
                font-size: inherit;
                font-style: inherit;
                font-weight: inherit;
                line-height: inherit;
                list-style: none;
                margin: 0;
                padding: 0;
                text-decoration: none;
                vertical-align: top;
            }

            /* content editable */
            *[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; }

            *[contenteditable]:hover, *[contenteditable]:focus, td:hover *[contenteditable], td:focus *[contenteditable], img.hover { background: #DEF; box-shadow: 0 0 1em 0.5em #DEF; }

            /* heading */
            h1 { font: bold 100% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; }

            /* table */
            table { font-size: 75%; table-layout: fixed; width: 100%; }
            table { border-collapse: separate; border-spacing: 2px; }
            th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; }
            th, td { border-radius: 0.25em; border-style: solid; }
            th { background: #FCFDEF; border-color: #BBB;}
            td { border-color: #DDD; }

            /* page */
            html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; padding: 0.5in; }
            html { background: #999; cursor: default; }

            body { box-sizing: border-box; height: 11in; margin: 0 auto; overflow: hidden; padding: 0.5in; }
            body { background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }

            /* header */
            header { margin: 0 0 3em; }
            header:after { clear: both; content: ""; display: table; }

            header h1 { background: #316291; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
            header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
            header address p { margin: 0 0 0.25em; }
            header span, header img { display: block; float: right; }
            header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
            header img { max-height: 100%; max-width: 100%; }
            header input { cursor: pointer; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

            /* article */
            article, article address, table.meta, table.inventory { margin: 0 0 3em; }
            article:after { clear: both; content: ""; display: table; }
            article h1 { clip: rect(0 0 0 0); position: absolute; }

            article address { float: left; font-size: 125%; font-weight: bold; }

            /* table meta & balance */
            table.meta, table.balance { float: right; width: 36%; }
            table.meta:after, table.balance:after { clear: both; content: ""; display: table; }

            /* table meta */
            table.meta th { width: 40%; }
            table.meta td { width: 60%; }

            /* table items */
            table.inventory { clear: both; width: 100%; }
            table.inventory th { font-weight: bold; text-align: center; }

            table.inventory td:nth-child(1) { width: 26%; }
            table.inventory td:nth-child(2) { width: 38%; }
            table.inventory td:nth-child(3) { text-align: right; width: 12%; }
            table.inventory td:nth-child(4) { text-align: right; width: 12%; }
            table.inventory td:nth-child(5) { text-align: right; width: 12%; }

            /* table balance */
            table.balance th, table.balance td { width: 50%; }
            table.balance td { text-align: right; }

            /* javascript */
            .add, .cut
            {
                border-width: 1px;
                display: block;
                font-size: .8rem;
                padding: 0.25em 0.5em;	
                float: left;
                text-align: center;
                width: 0.6em;
            }

            .add, .cut
            {
                background: #9AF;
                box-shadow: 0 1px 2px rgba(0,0,0,0.2);
                background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
                background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
                border-radius: 0.5em;
                border-color: #0076A3;
                color: #FFF;
                cursor: pointer;
                font-weight: bold;
                text-shadow: 0 -1px 2px rgba(0,0,0,0.333);
            }

            .add { margin: -2.5em 0 0; }

            .add:hover { background: #00ADEE; }

            .cut { opacity: 0; position: absolute; top: 0; left: -1.5em; }
            .cut { -webkit-transition: opacity 100ms ease-in; }

            img {
    width: 125px;
    height: 125px;
}
        </style>
	</head>
	<body>
		<header>
			<h1>Nota Pencucian</h1>
			<address contenteditable>
				<p>Mepokoaso Car Wash</p>
				<p>Jl. Singa No.1 BTN Batu Marupa</p>
				<p>Kendari, Sulawesi Tenggara</p>
			</address>
            <table class="balance">
                <img src="./assets/logo.png" alt="Logo Mepokoaso Car Wash">
            </table>
		</header>
		<article>
			<table class="balance">
				<tr>
					<th><span contenteditable>Nomor Pencucian</span></th>
					<td><span data-prefix></span><span>{{ $transaksi->no_pencucian }}</span></td>
				</tr>
				<tr>
					<th><span contenteditable>Tanggal</span></th>
					<td><span data-prefix></span><span>{{ $tglWaktu }}</span></td>
				</tr>
			</table>
			<table class="inventory">
				<thead>
					<tr>
                        <th><span contenteditable>Nama Kendaraan</span></th>
						<th><span contenteditable>Nomor Plat</span></th>
						<th><span contenteditable>Tarif</span></th>
					</tr>
				</thead>
				<tbody>
					<tr>
                        <td><span contenteditable>{{ $transaksi->kendaraan->nama }}</span></td>
						<td><span contenteditable>{{ $transaksi->no_polisi }}</span></td>
						<td><span contenteditable>Rp{{ $transaksi->tarif_kendaraan }}</span></td>
					</tr>
				</tbody>
			</table>
			<table class="balance">
				<tr>
					<th><span contenteditable>Subtotal</span></th>
					<td><span data-prefix>Rp</span><span>{{ $transaksi->tarif_kendaraan }}</span></td>
				</tr>
				<tr>
					<th><span contenteditable>Diskon</span></th>
					<td><span data-prefix>Rp</span><span contenteditable>{{ $diskon }}</span></td>
				</tr>
				<tr>
					<th><span contenteditable>Total</span></th>
					<td><span data-prefix>Rp</span><span>{{ $transaksi->total_pembayaran }}</span></td>
				</tr>
			</table>
		</article>
	</body>
</html>