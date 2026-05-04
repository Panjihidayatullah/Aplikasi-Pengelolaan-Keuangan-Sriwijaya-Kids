<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Saya</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
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

        .meta {
            margin-bottom: 10px;
        }

        .meta-row {
            margin-bottom: 2px;
            font-size: 10px;
        }

        table.summary,
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.summary th,
        table.summary td,
        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 9px;
        }

        table.summary th,
        table.data th {
            background: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .status-hadir { color: #0f5132; }
        .status-izin { color: #084298; }
        .status-sakit { color: #664d03; }
        .status-alpa { color: #842029; }

        .footer {
            margin-top: 14px;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('finance.school.name', 'Sriwijaya Kids') }}</h1>
        <h2>Rekap Absensi Saya</h2>
        <p>{{ $semester->nama ?? '-' }} - {{ $semester->tahunAjaran->nama ?? '-' }}</p>
    </div>

    <div class="meta">
        <div class="meta-row">Nama Siswa: <strong>{{ $siswa->nama ?? '-' }}</strong></div>
        <div class="meta-row">NIS: <strong>{{ $siswa->nis ?? '-' }}</strong></div>
        <div class="meta-row">Kelas: <strong>{{ $kelas->nama ?? '-' }}</strong></div>
        <div class="meta-row">Bulan: <strong>{{ $bulanLabel }}</strong></div>
        <div class="meta-row">Total Riwayat: <strong>{{ $summary['total'] ?? 0 }}</strong> data</div>
    </div>

    <table class="summary">
        <thead>
            <tr>
                <th>Hadir</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Alpa</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center status-hadir"><strong>{{ $summary['hadir'] ?? 0 }}</strong></td>
                <td class="text-center status-izin"><strong>{{ $summary['izin'] ?? 0 }}</strong></td>
                <td class="text-center status-sakit"><strong>{{ $summary['sakit'] ?? 0 }}</strong></td>
                <td class="text-center status-alpa"><strong>{{ $summary['alpa'] ?? 0 }}</strong></td>
            </tr>
        </tbody>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 10%;">Pertemuan</th>
                <th style="width: 14%;">Tanggal</th>
                <th style="width: 24%;">Mapel</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 20%;">Catatan</th>
                <th style="width: 14%;">Guru</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $row)
            @php
                $status = (string) ($row->status ?? '-');
                $statusClass = $status === 'hadir'
                    ? 'status-hadir'
                    : ($status === 'izin'
                        ? 'status-izin'
                        : ($status === 'sakit' ? 'status-sakit' : 'status-alpa'));
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $row->pertemuan_ke ? 'P' . $row->pertemuan_ke : '' }}</td>
                <td class="text-center">{{ optional($row->absensi?->tanggal_absensi)->format('d/m/Y') ?? '-' }}</td>
                <td class="text-left">{{ $row->absensi?->mataPelajaran?->nama ?? 'Umum' }}</td>
                <td class="text-center {{ $statusClass }}"><strong>{{ ucfirst($status) }}</strong></td>
                <td class="text-left">{{ $row->catatan ?: ($row->absensi?->keterangan ?: '-') }}</td>
                <td class="text-left">{{ $row->absensi?->guru?->nama ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Data absensi belum tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ $dicetakPada->format('d M Y H:i') }} WIB
    </div>
</body>
</html>