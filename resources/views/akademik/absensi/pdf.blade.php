<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Semester</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.35;
            color: #000;
            padding: 16px;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
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
            font-size: 9px;
            margin: 1px 0;
        }

        .meta {
            margin-bottom: 10px;
            font-size: 9px;
        }

        .meta-row {
            margin-bottom: 2px;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .summary th,
        .summary td {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            font-size: 9px;
        }

        .summary th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 4px 3px;
            font-size: 8px;
        }

        table.data th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .status-hadir { color: #0f5132; }
        .status-izin { color: #084298; }
        .status-sakit { color: #664d03; }
        .status-alpa { color: #842029; }
        .persen-good { color: #0f5132; font-weight: bold; }
        .persen-mid { color: #664d03; font-weight: bold; }
        .persen-low { color: #842029; font-weight: bold; }

        .muted {
            color: #555;
        }

        .footer {
            margin-top: 16px;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('finance.school.name', 'Sriwijaya Kids') }}</h1>
        <h2>Rekap Absensi Semester</h2>
        <p>Kelas {{ $kelas->nama ?? '-' }} | Semester {{ $semester->nomor_semester ?? '-' }}</p>
        <p>
            Periode {{ optional($semester->tanggal_mulai)->format('d M Y') ?? '-' }}
            s.d {{ optional($semester->tanggal_selesai)->format('d M Y') ?? '-' }}
        </p>
    </div>

    <div class="meta">
        <div class="meta-row">Tahun Ajaran: {{ $semester->tahunAjaran->nama ?? '-' }}</div>
        <div class="meta-row">Mata Pelajaran: {{ $mapelFilter->nama ?? 'Semua Mata Pelajaran' }}</div>
        <div class="meta-row">Jumlah Pertemuan Absensi: {{ $absensiList->count() }}</div>
    </div>

    <table class="summary">
        <thead>
            <tr>
                <th>Total Hadir</th>
                <th>Total Izin</th>
                <th>Total Sakit</th>
                <th>Total Alpa</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="status-hadir"><strong>{{ $totalHadir }}</strong></td>
                <td class="status-izin"><strong>{{ $totalIzin }}</strong></td>
                <td class="status-sakit"><strong>{{ $totalSakit }}</strong></td>
                <td class="status-alpa"><strong>{{ $totalAlpa }}</strong></td>
            </tr>
        </tbody>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 20%;">Mata Pelajaran</th>
                <th style="width: 16%;">Guru</th>
                <th style="width: 12%;">Hadir</th>
                <th style="width: 12%;">Izin</th>
                <th style="width: 12%;">Sakit</th>
                <th style="width: 12%;">Alpa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensiList as $index => $absensi)
            @php
                $hadirCount = $absensi->details->where('status', 'hadir')->count();
                $izinCount = $absensi->details->where('status', 'izin')->count();
                $sakitCount = $absensi->details->where('status', 'sakit')->count();
                $alpaCount = $absensi->details->where('status', 'alpa')->count();
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ optional($absensi->tanggal_absensi)->format('d/m/Y') ?? '-' }}</td>
                <td class="text-left">{{ $absensi->mataPelajaran->nama ?? 'Umum' }}</td>
                <td class="text-left">{{ $absensi->guru->nama ?? '-' }}</td>
                <td class="text-center status-hadir">{{ $hadirCount }}</td>
                <td class="text-center status-izin">{{ $izinCount }}</td>
                <td class="text-center status-sakit">{{ $sakitCount }}</td>
                <td class="text-center status-alpa">{{ $alpaCount }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center muted">Data absensi tidak tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 12px; margin-bottom: 6px; font-weight: bold; font-size: 10px;">
        Rekap Per Siswa (Semester)
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 28%;">Nama Siswa</th>
                <th style="width: 12%;">NIS</th>
                <th style="width: 9%;">Hadir</th>
                <th style="width: 9%;">Izin</th>
                <th style="width: 9%;">Sakit</th>
                <th style="width: 9%;">Alpa</th>
                <th style="width: 10%;">Total</th>
                <th style="width: 10%;">% Hadir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapPerSiswa as $index => $rekap)
            @php
                $persentaseHadir = (float) $rekap['persentase_hadir'];
                $kelasPersentase = $persentaseHadir >= 90
                    ? 'persen-good'
                    : ($persentaseHadir >= 75 ? 'persen-mid' : 'persen-low');
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-left">{{ $rekap['nama'] }}</td>
                <td class="text-center">{{ $rekap['nis'] }}</td>
                <td class="text-center status-hadir">{{ $rekap['hadir'] }}</td>
                <td class="text-center status-izin">{{ $rekap['izin'] }}</td>
                <td class="text-center status-sakit">{{ $rekap['sakit'] }}</td>
                <td class="text-center status-alpa">{{ $rekap['alpa'] }}</td>
                <td class="text-center">{{ $rekap['total'] }}</td>
                <td class="text-center {{ $kelasPersentase }}">{{ number_format($persentaseHadir, 2, ',', '.') }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center muted">Rekap siswa tidak tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ $dicetakPada->format('d M Y H:i') }} WIB
    </div>
</body>
</html>
