<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelajaran</title>
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
            color: #111;
            padding: 16px;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #111;
            padding-bottom: 8px;
        }

        .header h1 {
            font-size: 14px;
            margin-bottom: 2px;
        }

        .header h2 {
            font-size: 12px;
            margin-bottom: 2px;
        }

        .header p {
            font-size: 9px;
            margin-bottom: 1px;
        }

        .meta {
            margin-bottom: 10px;
            font-size: 9px;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .summary th,
        .summary td {
            border: 1px solid #111;
            padding: 5px 4px;
            text-align: center;
        }

        .summary th {
            background: #f2f2f2;
        }

        .data {
            width: 100%;
            border-collapse: collapse;
        }

        .data th,
        .data td {
            border: 1px solid #111;
            padding: 4px 3px;
            font-size: 8px;
        }

        .data th {
            background: #f2f2f2;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .muted {
            color: #666;
        }

        .badge {
            font-weight: bold;
        }

        .badge-active {
            color: #0f5132;
        }

        .badge-inactive {
            color: #842029;
        }

        .footer {
            margin-top: 12px;
            font-size: 8px;
        }
    </style>
</head>
<body>
    @php
        $totalJadwal = $jadwal->count();
        $totalAktif = $jadwal->where('is_active', true)->count();
        $hariUnik = $jadwal->pluck('hari')->filter()->unique()->count();
    @endphp

    <div class="header">
        <h1>{{ config('finance.school.name', 'Sriwijaya Kids') }}</h1>
        <h2>Data Jadwal Pelajaran</h2>
        <p>{{ $scopeLabel }}</p>
        @if(!empty($detailLabel))
        <p>{{ $detailLabel }}</p>
        @endif
    </div>

    <div class="meta">
        <p>Total Jadwal: {{ $totalJadwal }}</p>
        <p>Hari Terisi: {{ $hariUnik }}</p>
    </div>

    <table class="summary">
        <thead>
            <tr>
                <th>Jadwal Aktif</th>
                <th>Jadwal Nonaktif</th>
                <th>Total Jadwal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="badge badge-active">{{ $totalAktif }}</td>
                <td class="badge badge-inactive">{{ max(0, $totalJadwal - $totalAktif) }}</td>
                <td><strong>{{ $totalJadwal }}</strong></td>
            </tr>
        </tbody>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 8%;">Hari</th>
                <th style="width: 10%;">Jam</th>
                @if(!$isSiswaScope)
                <th style="width: 12%;">Kelas</th>
                @endif
                <th style="width: 22%;">Mata Pelajaran</th>
                <th style="width: 18%;">Guru</th>
                <th style="width: 14%;">Ruang</th>
                @if(!$isSiswaScope)
                <th style="width: 10%;">Status</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($jadwal as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $item->hari ?? '-' }}</td>
                <td class="text-center">{{ substr((string) $item->jam_mulai, 0, 5) }} - {{ substr((string) $item->jam_selesai, 0, 5) }}</td>
                @if(!$isSiswaScope)
                <td class="text-left">{{ $item->kelas->nama ?? '-' }}</td>
                @endif
                <td class="text-left">{{ $item->is_istirahat ? 'Ishoma / Istirahat' : ($item->mataPelajaran->nama ?? '-') }}</td>
                <td class="text-left">{{ $item->is_istirahat ? '-' : ($item->guru->nama ?? '-') }}</td>
                <td class="text-left">{{ $item->is_istirahat ? '-' : ($item->ruangan->nama ?? $item->ruang ?? '-') }}</td>
                @if(!$isSiswaScope)
                <td class="text-center">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ $isSiswaScope ? 6 : 8 }}" class="text-center muted">Data jadwal belum tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ $dicetakPada->format('d M Y H:i') }} WIB
    </div>
</body>
</html>
