<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Opname - PT. Erickman Sarana Abadi</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            padding: 0;
            font-size: 18px;
            letter-spacing: 1px;
            font-weight: 700;
        }
        .header p {
            margin: 3px 0;
            font-size: 10px;
            color: #555555;
        }
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #777777;
            padding: 8px;
            font-size: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
        }
        td.left {
            text-align: left;
        }
        .text-danger {
            color: #ef4444;
            font-weight: bold;
        }
        .text-success {
            color: #10b981;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            width: 100%;
            font-size: 10px;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
        }
        .signature-table td {
            border: none;
            text-align: center;
            font-size: 10px;
            padding: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>PT. ERICKMAN SARANA ABADI</h2>
        <p>8 Office Park Building, 12th Floor Unit A & H, Jalan TB. Simatupang Nomor 18, Jakarta Selatan</p>
        <p>Telepon: (021) 12345678 | Email: info@erickman.com</p>
    </div>

    <div class="title">Laporan Stok Opname Barang & Penyesuaian</div>
    <p style="margin-bottom: 10px; font-size: 10px;">Tanggal Cetak: <strong>{{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}</strong></p>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 25%;">Nama Barang</th>
                <th style="width: 10%;">Stok Sistem</th>
                <th style="width: 10%;">Stok Fisik</th>
                <th style="width: 10%;">Variance</th>
                <th style="width: 13%;">Petugas</th>
                <th style="width: 15%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($opnames as $index => $op)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($op->opname_date)->format('d-m-Y') }}</td>
                    <td class="left"><strong>{{ $op->item->name }}</strong></td>
                    <td>{{ $op->system_stock }}</td>
                    <td>{{ $op->physical_stock }}</td>
                    <td>
                        @if($op->variance < 0)
                            <span class="text-danger">{{ $op->variance }}</span>
                        @elseif($op->variance > 0)
                            <span class="text-success">+{{ $op->variance }}</span>
                        @else
                            <span style="color: #666666;">0</span>
                        @endif
                    </td>
                    <td>{{ $op->user->name }}</td>
                    <td class="left">{{ $op->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #777777; padding: 20px;">
                        Belum ada data riwayat penyesuaian stok opname.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td style="width: 50%;">
                Dibuat Oleh,<br><br><br><br>
                <strong>( ________________________ )</strong><br>
                Operator Gudang / Staf Lapangan
            </td>
            <td style="width: 50%;">
                Disetujui Oleh,<br><br><br><br>
                <strong>( Administrator )</strong><br>
                PT. Erickman Sarana Abadi
            </td>
        </tr>
    </table>

</body>
</html>
