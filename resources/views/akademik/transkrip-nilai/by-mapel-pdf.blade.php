<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Export Transkrip Nilai Mapel</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
        }

        h1 {
            margin: 0;
            font-size: 20px;
            text-align: center;
        }

        .meta {
            margin-top: 10px;
            margin-bottom: 10px;
            font-size: 12px;
            line-height: 1.6;
        }

        .meta-row {
            display: flex;
            gap: 8px;
        }

        .meta-label {
            width: 140px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th,
        td {
            border: 1px solid #1f2937;
            padding: 6px 7px;
        }

        th {
            background: #f3f4f6;
            font-weight: 700;
            text-align: center;
        }

        td:nth-child(1),
        td:nth-child(2),
        td:nth-child(4),
        td:nth-child(5),
        td:nth-child(6),
        td:nth-child(7),
        td:nth-child(8) {
            text-align: center;
        }

        .footer {
            margin-top: 10px;
            font-size: 11px;
            color: #4b5563;
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>EXPORT TRANSKRIP NILAI PER MAPEL</h1>

    <div class="meta">
        <div class="meta-row"><span class="meta-label">Kelas</span><span>: {{ $kelas->nama ?? '-' }}</span></div>
        <div class="meta-row"><span class="meta-label">Mata Pelajaran</span><span>: {{ $mataPelajaran->nama ?? '-' }} ({{ $mataPelajaran->kode_mapel ?? '-' }})</span></div>
        <div class="meta-row"><span class="meta-label">Semester</span><span>: {{ $selectedSemester->nama ?? '-' }}</span></div>
        <div class="meta-row"><span class="meta-label">Tahun Ajaran</span><span>: {{ $selectedTahunAjaran->nama ?? '-' }}</span></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 11%;">NIS</th>
                <th style="width: 26%; text-align: left;">Nama Siswa</th>
                <th style="width: 10%;">Nilai Tugas</th>
                <th style="width: 10%;">UTS</th>
                <th style="width: 10%;">UAS</th>
                <th style="width: 12%;">Nilai Akhir</th>
                <th style="width: 7%;">Grade</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row['no'] }}</td>
                <td>{{ $row['nis'] }}</td>
                <td style="text-align: left;">{{ $row['nama_siswa'] }}</td>
                <td>{{ $row['nilai_tugas'] }}</td>
                <td>{{ $row['nilai_uts'] }}</td>
                <td>{{ $row['nilai_uas'] }}</td>
                <td>{{ $row['nilai_akhir'] }}</td>
                <td>{{ $row['grade'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada data siswa untuk diekspor.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak pada: {{ $dicetakPada->format('d/m/Y H:i') }}</div>
</body>
</html>
