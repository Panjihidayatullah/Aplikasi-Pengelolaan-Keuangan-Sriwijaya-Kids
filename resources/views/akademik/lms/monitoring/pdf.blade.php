<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Monitoring Siswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
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

        tfoot td {
            background: #f7f7f7;
            font-weight: bold;
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
        <p>Laporan Monitoring Detail Siswa</p>
    </div>

    <div class="meta">
        <div class="meta-row">Nama Siswa: <strong>{{ $siswa->nama ?? '-' }}</strong></div>
        <div class="meta-row">NISN: <strong>{{ $studentNisn }}</strong></div>
        <div class="meta-row">Kelas: <strong>{{ $selectedKelas->nama ?? '-' }}</strong></div>
        <div class="meta-row">Semester: <strong>{{ $selectedSemester->nama ?? '-' }}</strong></div>
        <div class="meta-row">Tahun Ajaran: <strong>{{ $selectedTahunAjaran->nama ?? '-' }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 22%;">Mapel</th>
                <th style="width: 12%;">Absensi</th>
                <th style="width: 12%;">Tugas</th>
                <th style="width: 12%;">Nilai</th>
                <th style="width: 10%;">UTS</th>
                <th style="width: 10%;">UAS</th>
                <th style="width: 12%;">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detailRows as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-left">{{ $row['mapel_nama'] }}</td>
                <td class="text-center">{{ $row['absensi_hadir'] }}/{{ $row['absensi_total'] }}</td>
                <td class="text-center">{{ $row['tugas_submit'] }}/{{ $row['tugas_total'] }}</td>
                <td class="text-center">{{ $row['nilai_isi'] }}/{{ $row['nilai_total'] }}</td>
                <td class="text-center">{{ $row['uts'] !== null ? number_format((float) $row['uts'], 2, ',', '.') : '-' }}</td>
                <td class="text-center">{{ $row['uas'] !== null ? number_format((float) $row['uas'], 2, ',', '.') : '-' }}</td>
                <td class="text-center text-bold">{{ $row['total_nilai'] !== null ? number_format((float) $row['total_nilai'], 2, ',', '.') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Data detail belum tersedia pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        @if($detailRows->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="2" class="text-center">TOTAL / RATA-RATA</td>
                <td class="text-center">{{ $detailTotals['absensi_hadir'] }}/{{ $detailTotals['absensi_total'] }}</td>
                <td class="text-center">{{ $detailTotals['tugas_submit'] }}/{{ $detailTotals['tugas_total'] }}</td>
                <td class="text-center">{{ $detailTotals['nilai_isi'] }}/{{ $detailTotals['nilai_total'] }}</td>
                <td class="text-center">{{ $detailTotals['avg_uts'] !== null ? number_format((float) $detailTotals['avg_uts'], 2, ',', '.') : '-' }}</td>
                <td class="text-center">{{ $detailTotals['avg_uas'] !== null ? number_format((float) $detailTotals['avg_uas'], 2, ',', '.') : '-' }}</td>
                <td class="text-center">{{ $detailTotals['avg_total_nilai'] !== null ? number_format((float) $detailTotals['avg_total_nilai'], 2, ',', '.') : '-' }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        Dicetak pada {{ $dicetakPada->format('d M Y H:i') }} WIB
    </div>
</body>
</html>
