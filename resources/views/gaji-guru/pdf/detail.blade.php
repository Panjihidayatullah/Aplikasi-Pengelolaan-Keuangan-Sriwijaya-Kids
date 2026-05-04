<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji Guru</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #111;
            font-size: 11px;
            line-height: 1.5;
            padding: 18px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header h1 {
            font-size: 15px;
            margin-bottom: 2px;
        }

        .header h2 {
            font-size: 13px;
            margin-bottom: 2px;
        }

        .header p {
            font-size: 10px;
        }

        .meta {
            margin-bottom: 12px;
        }

        .meta-row {
            margin-bottom: 2px;
        }

        .section-title {
            margin: 10px 0 6px;
            font-size: 12px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 5px;
            font-size: 10px;
            vertical-align: top;
        }

        th {
            width: 34%;
            text-align: left;
            background: #f0f0f0;
            font-weight: bold;
        }

        .amount {
            font-size: 14px;
            font-weight: bold;
        }

        .footer {
            margin-top: 14px;
            font-size: 9px;
        }

        .signature-wrap {
            margin-top: 30px;
            width: 100%;
        }

        .signature-box {
            width: 210px;
            margin-left: auto;
            text-align: center;
        }

        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #000;
            padding-top: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('finance.school.name', 'Sriwijaya Kids') }}</h1>
        <h2>Detail Transaksi Gaji Guru</h2>
        <p>Kode: {{ $gajiGuru->pengeluaran->kode_transaksi ?? '-' }}</p>
    </div>

    <div class="meta">
        <div class="meta-row">Tanggal cetak: <strong>{{ now()->format('d F Y, H:i') }}</strong></div>
        <div class="meta-row">Petugas: <strong>{{ $gajiGuru->dibayarOleh->name ?? $gajiGuru->pengeluaran->user->name ?? '-' }}</strong></div>
    </div>

    <div class="section-title">Informasi Gaji Guru</div>
    <table>
        <tbody>
            <tr>
                <th>Kode Transaksi</th>
                <td>{{ $gajiGuru->pengeluaran->kode_transaksi ?? '-' }}</td>
            </tr>
            <tr>
                <th>Nama Guru</th>
                <td>{{ $gajiGuru->guru->nama ?? '-' }}</td>
            </tr>
            <tr>
                <th>NIP</th>
                <td>{{ $gajiGuru->guru->nip ?? '-' }}</td>
            </tr>
            <tr>
                <th>Periode Gaji</th>
                <td>{{ $gajiGuru->periode_bulan }}/{{ $gajiGuru->periode_tahun }}</td>
            </tr>
            <tr>
                <th>Tanggal Bayar</th>
                <td>{{ optional($gajiGuru->pengeluaran->tanggal)->format('d F Y') ?? '-' }}</td>
            </tr>
            <tr>
                <th>Jumlah Gaji</th>
                <td class="amount">Rp {{ number_format((float) ($gajiGuru->pengeluaran->jumlah ?? 0), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $gajiGuru->pengeluaran->status ?? '-' }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $gajiGuru->pengeluaran->keterangan ?: '-' }}</td>
            </tr>
            <tr>
                <th>Detail Gaji Guru</th>
                <td>{{ $gajiGuru->detail ?: '-' }}</td>
            </tr>
            <tr>
                <th>Bukti File</th>
                <td>{{ $gajiGuru->pengeluaran->bukti_file ? basename($gajiGuru->pengeluaran->bukti_file) : '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Penanggung Jawab</div>
    <table>
        <tbody>
            <tr>
                <th>Nama</th>
                <td>{{ $gajiGuru->dibayarOleh->name ?? $gajiGuru->pengeluaran->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $gajiGuru->dibayarOleh->email ?? $gajiGuru->pengeluaran->user->email ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan otomatis oleh sistem.
    </div>

    <div class="signature-wrap">
        <div class="signature-box">
            <div>Bendahara</div>
            <div class="signature-line">{{ $gajiGuru->dibayarOleh->name ?? $gajiGuru->pengeluaran->user->name ?? '-' }}</div>
        </div>
    </div>
</body>
</html>
