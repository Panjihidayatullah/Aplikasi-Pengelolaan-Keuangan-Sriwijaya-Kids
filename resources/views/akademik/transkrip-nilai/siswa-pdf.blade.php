<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Transkrip Siswa - {{ $siswa->nama }}</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #111;
            font-size: 10px;
            line-height: 1.4;
            padding: 16px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 15px;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 10px;
        }

        .meta {
            margin-bottom: 10px;
        }

        .meta-row {
            margin-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 9px;
        }

        th {
            background: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }

        td {
            vertical-align: middle;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .summary {
            margin-top: 12px;
            font-size: 9px;
            line-height: 1.6;
        }

        .footer {
            margin-top: 12px;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('finance.school.name', 'Sriwijaya Kids') }}</h1>
        <p>Laporan Transkrip Nilai Siswa</p>
        <p>Nomor Dokumen: {{ $nomorDokumen }}</p>
    </div>

    @php
        $status = (string) ($kenaikanTerbaru->status ?? '');
        $statusLabel = $status === 'naik' ? 'Naik Kelas' : ($status === 'tidak_naik' ? 'Tidak Naik Kelas' : ($status === 'lulus' ? 'Lulus' : '-'));
    @endphp

    <div class="meta">
        <div class="meta-row">Nama Siswa: <strong>{{ $siswa->nama ?? '-' }}</strong></div>
        <div class="meta-row">NIS: <strong>{{ $siswa->nis ?? optional($siswa->kartuPelajar->first())->nis_otomatis ?? '-' }}</strong></div>
        <div class="meta-row">Kelas Saat Ini: <strong>{{ $siswa->kelas->nama ?? '-' }}</strong></div>
        <div class="meta-row">Status Kenaikan Terbaru: <strong>{{ $statusLabel }}</strong></div>
        <div class="meta-row">Kelas Tujuan: <strong>{{ $kenaikanTerbaru?->kelasTujuan?->nama ?? '-' }}</strong></div>
        <div class="meta-row">Tahun Ajaran Kenaikan: <strong>{{ $kenaikanTerbaru?->tahunAjaran?->nama ?? '-' }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 14%;">Semester</th>
                <th style="width: 16%;">Tahun Ajaran</th>
                <th style="width: 24%;">Mata Pelajaran</th>
                <th style="width: 10%;">Tugas</th>
                <th style="width: 10%;">UTS</th>
                <th style="width: 10%;">UAS</th>
                <th style="width: 8%;">Nilai Akhir</th>
                <th style="width: 4%;">Grade</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilais as $index => $nilai)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $nilai->semester->nama ?? '-' }}</td>
                <td class="text-center">{{ $nilai->tahunAjaran->nama ?? '-' }}</td>
                <td class="text-left">{{ $nilai->mataPelajaran->nama ?? '-' }}</td>
                <td class="text-center">{{ number_format((float) ($nilai->nilai_harian ?? 0), 2, ',', '.') }}</td>
                <td class="text-center">{{ number_format((float) ($nilai->nilai_uts ?? 0), 2, ',', '.') }}</td>
                <td class="text-center">{{ number_format((float) ($nilai->nilai_uas ?? 0), 2, ',', '.') }}</td>
                <td class="text-center text-bold">{{ number_format((float) ($nilai->nilai_akhir ?? 0), 2, ',', '.') }}</td>
                <td class="text-center text-bold">{{ $nilai->grade ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Belum ada data nilai.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @php
        $avg = $nilais->avg('nilai_akhir');
        $max = $nilais->max('nilai_akhir');
        $min = $nilais->min('nilai_akhir');
    @endphp
    <div class="summary">
        <div>Rata-rata Nilai Akhir: <strong>{{ $avg !== null ? number_format((float) $avg, 2, ',', '.') : '-' }}</strong></div>
        <div>Nilai Tertinggi: <strong>{{ $max !== null ? number_format((float) $max, 2, ',', '.') : '-' }}</strong></div>
        <div>Nilai Terendah: <strong>{{ $min !== null ? number_format((float) $min, 2, ',', '.') : '-' }}</strong></div>
    </div>

    <div class="footer">
        Dicetak pada {{ now()->format('d M Y H:i') }} WIB
    </div>
</body>
</html>
