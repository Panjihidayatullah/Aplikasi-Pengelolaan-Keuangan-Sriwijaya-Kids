<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arus Kas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .header h2 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .header p {
            font-size: 9px;
            margin: 1px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
        }
        
        table td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 9px;
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
            font-size: 10px;
        }
        
        .subtotal-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .total-row {
            background-color: #d0d0d0;
            font-weight: bold;
            font-size: 10px;
        }
        
        .grand-total-row {
            background-color: #b0b0b0;
            font-weight: bold;
            font-size: 11px;
        }
        
        .footer {
            margin-top: 30px;
            font-size: 9px;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-weight: bold;
        }
        
        .border-top-double {
            border-top: 3px double #000;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('finance.school.name', 'Sriwijaya Kids') }}</h1>
        <h2>Laporan Arus Kas</h2>
        <p>Periode {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s.d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
        <p>Klasifikasi: Semua</p>
    </div>

    <!-- Tabel Arus Kas dari Aktivitas Operasi (Pemasukan) -->
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 12%;">Nomor Referensi</th>
                <th style="width: 8%;">Unit</th>
                <th style="width: 20%;">Keterangan</th>
                <th style="width: 13%;">Debit</th>
                <th style="width: 13%;">Kredit</th>
                <th style="width: 13%;">Saldo</th>
                <th style="width: 13%;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr class="group-header">
                <td colspan="8">ARUS KAS DARI AKTIVITAS OPERASI</td>
            </tr>
            
            @if($pemasukanPerJenis->count() > 0)
                @php $saldoBerjalan = 0; @endphp
                @foreach($pemasukanPerJenis as $jenisId => $data)
                <tr class="group-header">
                    <td colspan="8">{{ $data['nama'] }}</td>
                </tr>
                @foreach($data['items'] as $item)
                    @php $saldoBerjalan += $item->jumlah; @endphp
                    <tr>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}</td>
                        <td class="text-left">{{ $item->kode_transaksi }}</td>
                        <td class="text-center">Pcs</td>
                        <td class="text-left">{{ $item->siswa->nama ?? '-' }}</td>
                        <td class="text-right">{{ number_format($item->jumlah, 2, ',', '.') }}</td>
                        <td class="text-right">0,00</td>
                        <td class="text-right">{{ number_format($saldoBerjalan, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item->jumlah, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td colspan="4" class="text-left">Total {{ $data['nama'] }}</td>
                    <td class="text-right">{{ number_format($data['total'], 2, ',', '.') }}</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">{{ number_format($saldoBerjalan, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($data['total'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data pemasukan</td>
                </tr>
            @endif
            
            <tr class="total-row">
                <td colspan="4" class="text-left">KAS BERSIH DARI AKTIVITAS OPERASI</td>
                <td class="text-right">{{ number_format($totalPemasukan, 2, ',', '.') }}</td>
                <td class="text-right">0,00</td>
                <td class="text-right">{{ number_format($totalPemasukan, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalPemasukan, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel Arus Kas untuk Aktivitas Pengeluaran -->
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 12%;">Nomor Referensi</th>
                <th style="width: 8%;">Unit</th>
                <th style="width: 20%;">Keterangan</th>
                <th style="width: 13%;">Debit</th>
                <th style="width: 13%;">Kredit</th>
                <th style="width: 13%;">Saldo</th>
                <th style="width: 13%;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr class="group-header">
                <td colspan="8">ARUS KAS UNTUK AKTIVITAS PENGELUARAN</td>
            </tr>
            
            @if($pengeluaranPerJenis->count() > 0)
                @php $saldoBerjalan = 0; @endphp
                @foreach($pengeluaranPerJenis as $jenisId => $data)
                <tr class="group-header">
                    <td colspan="8">{{ $data['nama'] }}</td>
                </tr>
                @foreach($data['items'] as $item)
                    @php $saldoBerjalan += $item->jumlah; @endphp
                    <tr>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td class="text-left">{{ $item->kode_transaksi }}</td>
                        <td class="text-center">Pcs</td>
                        <td class="text-left">{{ $item->keterangan }}</td>
                        <td class="text-right">0,00</td>
                        <td class="text-right">{{ number_format($item->jumlah, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($saldoBerjalan, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item->jumlah, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td colspan="4" class="text-left">Total {{ $data['nama'] }}</td>
                    <td class="text-right">0,00</td>
                    <td class="text-right">{{ number_format($data['total'], 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($saldoBerjalan, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($data['total'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data pengeluaran</td>
                </tr>
            @endif
            
            <tr class="total-row">
                <td colspan="4" class="text-left">KAS BERSIH UNTUK AKTIVITAS PENGELUARAN</td>
                <td class="text-right">0,00</td>
                <td class="text-right">{{ number_format($totalPengeluaran, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalPengeluaran, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalPengeluaran, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Summary -->
    <table>
        <tbody>
            <tr class="total-row border-top-double">
                <td style="width: 70%;" class="text-left">KENAIKAN/PENURUNAN KAS DAN SETARA KAS</td>
                <td style="width: 30%;" class="text-right">{{ number_format($saldo, 2, ',', '.') }}</td>
            </tr>
            <tr class="grand-total-row">
                <td class="text-left">KAS DAN SETARA KAS AKHIR PERIODE</td>
                <td class="text-right">{{ number_format($saldo, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p style="margin-bottom: 5px;">Dicetak pada: {{ now()->format('l, d F Y') }} WIB</p>
        
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
