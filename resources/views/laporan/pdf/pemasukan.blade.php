<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemasukan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #000;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
        }
        
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .header h2 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .header p {
            font-size: 8px;
            margin: 1px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
        }
        
        table td {
            border: 1px solid #000;
            padding: 3px;
            font-size: 8px;
        }
        
        .text-left {
            text-align: left;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .group-header {
            background-color: #e8e8e8;
            font-weight: bold;
            font-size: 9px;
        }
        
        .subtotal-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .grand-total-row {
            background-color: #d0d0d0;
            font-weight: bold;
            font-size: 10px;
        }
        
        .footer {
            margin-top: 20px;
            font-size: 8px;
        }
        
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 180px;
        }
        
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            padding-top: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('finance.school.name', 'Sriwijaya Kids') }}</h1>
        <h2>Laporan Pemasukan</h2>
        <p>Periode {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s.d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 9%;">Tanggal</th>
                <th style="width: 11%;">Kode Transaksi</th>
                <th style="width: 15%;">Nama Siswa</th>
                <th style="width: 8%;">NIS</th>
                <th style="width: 12%;">Jenis Pembayaran</th>
                <th style="width: 12%;">Keterangan</th>
                <th style="width: 10%;">Metode</th>
                <th style="width: 12%;">Jumlah</th>
                <th style="width: 8%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @if($pembayaranPerJenis->count() > 0)
                @php $no = 1; @endphp
                @foreach($pembayaranPerJenis as $jenisId => $data)
                <tr class="group-header">
                    <td colspan="10">{{ $data['nama'] }}</td>
                </tr>
                @foreach($data['items'] as $item)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}</td>
                        <td class="text-left">{{ $item->kode_transaksi }}</td>
                        <td class="text-left">{{ $item->siswa->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->siswa->nis ?? '-' }}</td>
                        <td class="text-left">{{ $item->jenis ? \App\Models\JenisPembayaran::normalizeNama($item->jenis->nama) : '-' }}</td>
                        <td class="text-left">{{ \Illuminate\Support\Str::limit($item->keterangan, 30) }}</td>
                        <td class="text-center">{{ $item->metode_bayar }}</td>
                        <td class="text-right">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item->status }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td colspan="8" class="text-left">Subtotal {{ $data['nama'] }}</td>
                    <td class="text-right">{{ number_format($data['total'], 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data pemasukan</td>
                </tr>
            @endif
            
            @if($pembayaranPerJenis->count() > 0)
            <tr class="grand-total-row">
                <td colspan="8" class="text-left">TOTAL PEMASUKAN</td>
                <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                <td></td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p style="margin-bottom: 5px;">Dicetak pada: {{ now()->format('l, d F Y H:i') }} WIB</p>
        <p style="margin-bottom: 5px;">Total Data: {{ $pembayaran->count() }} transaksi</p>
        
        <div class="signature-section">
            <div class="signature-box">
                <p>Dibuat Oleh,</p>
                <div class="signature-line">
                    <p>Bendahara</p>
                </div>
            </div>
            
            <div class="signature-box">
                <p>Mengetahui,</p>
                <div class="signature-line">
                    <p>Kepala Sekolah</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
