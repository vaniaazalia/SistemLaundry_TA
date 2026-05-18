<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota - {{ $order->kode_order }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 80mm;
            padding: 4mm;
        }

        .center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }

        table { width: 100%; }
        td { vertical-align: top; padding: 1px 0; }
        .label { width: 45%; }

        .barcode-container {
            text-align: center;
            margin: 6px 0;
        }

        .barcode-container svg {
            max-width: 100%;
        }

        .kode-text {
            font-size: 10px;
            letter-spacing: 2px;
            margin-top: 3px;
        }

        .footer {
            font-size: 10px;
            text-align: center;
            margin-top: 4px;
        }

        .btn-print {
            display: block;
            margin: 15px auto;
            padding: 8px 24px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        @media print {
            @page {
                width: 80mm;
                margin: 0;
            }
            body { padding: 3mm; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="center bold" style="font-size:14px;">LAUNDRY SMEA BOSKU</div>
    <div class="center">Jl. Smea No. 4, Surabaya</div>
    <div class="center">Telp: 089678333548</div>

    <div class="divider"></div>

    {{-- Data Pelanggan --}}
    <table>
        <tr>
            <td class="label">Kode Order</td>
            <td>: <b>{{ $order->kode_order }}</b></td>
        </tr>
        <tr>
            <td class="label">Nama</td>
            <td>: {{ $order->nama_pelanggan }}</td>
        </tr>
        <tr>
            <td class="label">No. HP</td>
            <td>: {{ $order->no_hp }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td>: {{ $order->alamat ?? '-' }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- Detail Laundry --}}
    <table>
        <tr>
            <td class="label">Layanan</td>
            <td>: {{ match((int)$order->layanan_id) {
                1 => 'Reguler Cuci Kering',
                2 => 'Express Sehari Jadi',
                3 => 'Cuci Kering Saja',
                4 => 'Setrika Saja',
                default => '-'
            } }}</td>
        </tr>
        <tr>
            <td class="label">Berat</td>
            <td>: {{ $order->berat_kg }} Kg</td>
        </tr>
        <tr>
            <td class="label">Tgl Masuk</td>
            <td>: {{ \Carbon\Carbon::parse($order->tanggal_masuk)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Estimasi Selesai</td>
            <td>: {{ \Carbon\Carbon::parse($order->estimasi_selesai)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- Total --}}
    <table>
        <tr>
            <td class="label bold">TOTAL HARGA</td>
            <td><b>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</b></td>
        </tr>
    </table>

    @if($order->catatan)
        <div class="divider"></div>
        <div><b>Catatan:</b> {{ $order->catatan }}</div>
    @endif

    <div class="divider"></div>

    {{-- Barcode --}}
    <div class="barcode-container">
        {!! DNS1D::getBarcodeHTML($order->barcode_data, 'C128', 1.5, 45) !!}
        <div class="kode-text">{{ $order->barcode_data }}</div>
    </div>

    <div class="divider"></div>

    <div class="footer">Terima kasih atas kepercayaan Anda!</div>
    <div class="footer">Simpan nota ini untuk pengambilan</div>

    {{-- Tombol Print (tidak ikut tercetak) --}}
    <div class="no-print" style="text-align:center; margin-top: 15px;">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Nota</button>
    </div>

</body>
</html>