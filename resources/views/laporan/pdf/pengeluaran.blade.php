<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengeluaran</title>
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
        <h2>Laporan Pengeluaran</h2>
        <p>Periode {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s.d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 9%;">Tanggal</th>
                <th style="width: 12%;">Kode Transaksi</th>
                <th style="width: 15%;">Jenis Pengeluaran</th>
                <th style="width: 30%;">Keterangan</th>
                <th style="width: 13%;">Jumlah</th>
                <th style="width: 10%;">Petugas</th>
                <th style="width: 8%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @if($pengeluaranPerJenis->count() > 0)
                @php $no = 1; @endphp
                @foreach($pengeluaranPerJenis as $jenisId => $data)
                <tr class="group-header">
                    <td colspan="8">{{ $data['nama'] }}</td>
                </tr>
                @foreach($data['items'] as $item)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td class="text-left">{{ $item->kode_transaksi }}</td>
                        <td class="text-left">{{ $item->jenis->nama ?? '-' }}</td>
                        <td class="text-left">{{ \Illuminate\Support\Str::limit($item->keterangan, 50) }}</td>
                        <td class="text-right">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td class="text-left">{{ $item->user->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->status }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td colspan="5" class="text-left">Subtotal {{ $data['nama'] }}</td>
                    <td class="text-right">{{ number_format($data['total'], 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data pengeluaran</td>
                </tr>
            @endif
            
            @if($pengeluaranPerJenis->count() > 0)
            <tr class="grand-total-row">
                <td colspan="5" class="text-left">TOTAL PENGELUARAN</td>
                <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p style="margin-bottom: 5px;">Dicetak pada: {{ now()->format('l, d F Y H:i') }} WIB</p>
        <p style="margin-bottom: 5px;">Total Data: {{ $pengeluaran->count() }} transaksi</p>
        
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
