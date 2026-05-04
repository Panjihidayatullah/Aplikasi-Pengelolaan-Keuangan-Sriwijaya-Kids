<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembayaran</title>
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
        <h2>Detail Transaksi Pembayaran</h2>
        <p>Kode: {{ $pembayaran->kode_transaksi }}</p>
    </div>

    <div class="meta">
        <div class="meta-row">Tanggal cetak: <strong>{{ now()->format('d F Y, H:i') }}</strong></div>
        <div class="meta-row">Petugas: <strong>{{ $pembayaran->user->name ?? '-' }}</strong></div>
    </div>

    <div class="section-title">Informasi Pembayaran</div>
    <table>
        <tbody>
            <tr>
                <th>Kode Transaksi</th>
                <td>{{ $pembayaran->kode_transaksi }}</td>
            </tr>
            <tr>
                <th>Jenis Pembayaran</th>
                <td>{{ $pembayaran->jenis ? \App\Models\JenisPembayaran::normalizeNama($pembayaran->jenis->nama) : '-' }}</td>
            </tr>
            <tr>
                <th>Jumlah Bayar</th>
                <td class="amount">Rp {{ number_format((float) $pembayaran->jumlah, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Tanggal Bayar</th>
                <td>{{ optional($pembayaran->tanggal_bayar)->format('d F Y') ?? '-' }}</td>
            </tr>
            <tr>
                <th>Metode Pembayaran</th>
                <td>{{ $pembayaran->metode_bayar ?? '-' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $pembayaran->status ?? '-' }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $pembayaran->keterangan ?: '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Data Siswa</div>
    <table>
        <tbody>
            <tr>
                <th>NIS</th>
                <td>{{ $pembayaran->siswa->nis ?? '-' }}</td>
            </tr>
            <tr>
                <th>Nama Lengkap</th>
                <td>{{ $pembayaran->siswa->nama ?? '-' }}</td>
            </tr>
            <tr>
                <th>Kelas</th>
                <td>{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</td>
            </tr>
            <tr>
                <th>Telepon</th>
                <td>{{ $pembayaran->siswa->telepon ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan otomatis oleh sistem.
    </div>

    <div class="signature-wrap">
        <div class="signature-box">
            <div>Bendahara</div>
            <div class="signature-line">{{ $pembayaran->user->name ?? '-' }}</div>
        </div>
    </div>
</body>
</html>
