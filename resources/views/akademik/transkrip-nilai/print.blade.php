<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Transkrip Nilai - {{ $siswa->nama }}</title>
    <style>
        @page {
            size: A4;
            margin: 16mm 14mm;
        }

        :root {
            --text: #111827;
            --line: #1f2937;
            --muted: #4b5563;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text);
            background: #eef2ff;
        }

        .page {
            width: 100%;
            max-width: 190mm;
            margin: 16px auto;
            background: #fff;
            padding: 12mm;
            position: relative;
            overflow: hidden;
            border: 1px solid #d1d5db;
        }

        .watermark {
            position: absolute;
            inset: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: none;
            opacity: 0.06;
        }

        .watermark img {
            width: 260px;
            height: auto;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        .doc-title {
            text-align: center;
            margin: 0;
            font-size: 34px;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .doc-number {
            text-align: center;
            margin: 2px 0 14px;
            font-size: 14px;
            color: var(--muted);
        }

        .meta-table,
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table {
            margin-bottom: 12px;
        }

        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 14px;
        }

        .meta-table td:first-child {
            width: 45%;
        }

        .meta-table td:nth-child(2) {
            width: 18px;
            text-align: center;
        }

        .nilai-table th,
        .nilai-table td {
            border: 1px solid var(--line);
            padding: 8px 10px;
            font-size: 14px;
        }

        .nilai-table thead th {
            text-align: center;
            font-weight: 700;
            background: #f3f4f6;
        }

        .nilai-table td:nth-child(1),
        .nilai-table td:nth-child(3) {
            text-align: center;
            white-space: nowrap;
        }

        .nilai-table td:nth-child(1) {
            width: 48px;
        }

        .nilai-table td:nth-child(3) {
            width: 100px;
            font-weight: 700;
        }

        .footer {
            margin-top: 18px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
        }

        .footer-summary {
            font-size: 14px;
            line-height: 1.7;
        }

        .signature {
            text-align: center;
            min-width: 240px;
        }

        .signature p {
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
        }

        .signature-space {
            height: 72px;
        }

        .signature-name {
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: underline;
            margin-top: 4px;
        }

        .screen-actions {
            max-width: 190mm;
            margin: 16px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .screen-actions button,
        .screen-actions a {
            border: 0;
            background: #0f172a;
            color: #fff;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        .screen-actions a {
            background: #334155;
        }

        @media print {
            body {
                background: #fff;
            }

            .screen-actions {
                display: none;
            }

            .page {
                margin: 0;
                border: none;
                max-width: none;
                width: auto;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="screen-actions">
        <button type="button" onclick="window.print()">Cetak</button>
        <a href="{{ route('akademik.transkrip-nilai.index') }}">Kembali</a>
    </div>

    <div class="page">
        <div class="watermark">
            <img src="{{ asset(config('finance.school.logo', 'images/Logo_SriwijayaKids.png')) }}" alt="Logo">
        </div>

        <div class="content">
            <h1 class="doc-title">TRANSKRIP NILAI</h1>
            <p class="doc-number">Nomor : {{ $nomorDokumen }}</p>

            <table class="meta-table">
                <tbody>
                    <tr>
                        <td>Satuan Pendidikan</td>
                        <td>:</td>
                        <td>{{ strtoupper(config('finance.school.name', 'SRIWIJAYA KIDS')) }}</td>
                    </tr>
                    <tr>
                        <td>Nama Lengkap</td>
                        <td>:</td>
                        <td>{{ $siswa->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>:</td>
                        <td>{{ $siswa->kelas->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>NIS</td>
                        <td>:</td>
                        <td>{{ $siswa->nis ?? optional($siswa->kartuPelajar->first())->nis_otomatis ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat, Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="nilai-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Mata Pelajaran</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nilais as $index => $nilai)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $nilai->mataPelajaran->nama ?? '-' }}</td>
                        <td>{{ number_format((float) $nilai->nilai_akhir, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">Belum ada data nilai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @php
                $avg = $nilais->avg('nilai_akhir');
                $min = $nilais->min('nilai_akhir');
                $max = $nilais->max('nilai_akhir');
            @endphp
            <div class="footer">
                <div class="footer-summary">
                    <div>Rata-rata Nilai : <strong>{{ $avg !== null ? number_format((float) $avg, 2, ',', '.') : '-' }}</strong></div>
                    <div>Nilai Tertinggi : <strong>{{ $max !== null ? number_format((float) $max, 2, ',', '.') : '-' }}</strong></div>
                    <div>Nilai Terendah : <strong>{{ $min !== null ? number_format((float) $min, 2, ',', '.') : '-' }}</strong></div>
                </div>
                <div class="signature">
                    <p>{{ now()->format('d M Y') }}</p>
                    <p>Kepala Sekolah</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ strtoupper(config('finance.school.name', 'Sriwijaya Kids')) }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>