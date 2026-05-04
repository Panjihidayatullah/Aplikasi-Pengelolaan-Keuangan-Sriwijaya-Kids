<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Raport Siswa - {{ $siswa->nama }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1a1a2e; background: #fff; }

        /* ── Page Header ── */
        .page-header { text-align: center; padding: 14px 0 10px; border-bottom: 3px solid #4f46e5; margin-bottom: 14px; }
        .school-name  { font-size: 15px; font-weight: bold; color: #4f46e5; }
        .doc-title    { font-size: 12px; font-weight: bold; color: #1f2937; margin-top: 4px; text-transform: uppercase; letter-spacing: 1px; }

        /* ── Student card ── */
        .student-card { background: #f8f7ff; border: 1px solid #e0e7ff; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; }
        .student-card table { width: 100%; border-collapse: collapse; }
        .student-card td   { padding: 2px 8px; font-size: 9.5px; color: #374151; }
        .student-card td.lbl { font-weight: bold; color: #6d28d9; width: 100px; }

        /* ── Kelas block ── */
        .kelas-block { margin-bottom: 20px; page-break-inside: avoid; }

        /* Kelas header — pakai native HTML table agar DomPDF render background warna */
        .kelas-header-table { width: 100%; border-collapse: collapse; }
        .kelas-header-td  { background-color: #4f46e5; color: #ffffff; padding: 8px 12px; }
        .kelas-nama       { font-size: 11px; font-weight: bold; }
        .kelas-ta         { font-size: 8.5px; font-weight: normal; margin-left: 10px; }
        .kelas-status-td  { background-color: #4f46e5; color: #ffffff; padding: 8px 12px;
                            text-align: right; white-space: nowrap; width: 80px; }
        .status-badge     { border: 1px solid rgba(255,255,255,0.5); padding: 2px 8px;
                            border-radius: 10px; font-size: 8.5px; font-weight: bold; color: #fff; }

        /* ── Semester wrapper ── */
        .sem-table       { width: 100%; border-collapse: collapse;
                           border: 1px solid #e5e7eb; border-top: none; }
        .sem-cell        { width: 50%; vertical-align: top; border-right: 1px solid #e5e7eb; padding: 0; }
        .sem-cell-last   { width: 50%; vertical-align: top; padding: 0; }

        /* Semester sub-header */
        .sem-sub-table   { width: 100%; border-collapse: collapse; background-color: #f3f4f6;
                           border-bottom: 1px solid #e5e7eb; }
        .sem-name-td     { padding: 5px 10px; font-size: 9.5px; font-weight: bold; color: #374151; }
        .sem-avg-td      { padding: 5px 10px; text-align: right; font-size: 9px;
                           color: #6d28d9; font-weight: bold; white-space: nowrap; width: 130px; }
        .sem-empty-td    { padding: 5px 10px; text-align: right; font-size: 9px;
                           color: #9ca3af; font-weight: normal; width: 100px; }

        /* ── Nilai table ── */
        .nilai-table               { width: 100%; border-collapse: collapse; font-size: 8.5px; }
        .nilai-table thead tr      { background-color: #f9fafb; }
        .nilai-table th            { padding: 4px 6px; text-align: center; color: #6b7280;
                                     font-weight: 600; border-bottom: 1px solid #e5e7eb; }
        .nilai-table th:first-child { text-align: left; }
        .nilai-table td            { padding: 3px 6px; text-align: center; color: #374151;
                                     border-bottom: 1px solid #f3f4f6; }
        .nilai-table td:first-child { text-align: left; color: #111827; font-weight: 500; }
        .nilai-table tfoot tr      { background-color: #f3f4f6; }
        .nilai-table tfoot td      { font-weight: bold; color: #374151; border-top: 1px solid #e5e7eb; }

        /* Grade badges */
        .grade-badge { display: inline-block; padding: 1px 5px; border-radius: 10px;
                       font-weight: bold; font-size: 8px; }
        .grade-A { background-color: #d1fae5; color: #065f46; }
        .grade-B { background-color: #dbeafe; color: #1e40af; }
        .grade-C { background-color: #fef3c7; color: #92400e; }
        .grade-D { background-color: #fee2e2; color: #991b1b; }
        .grade-E { background-color: #fce7f3; color: #9d174d; }

        .empty-semester { padding: 14px 10px; text-align: center; color: #9ca3af;
                          font-size: 8.5px; font-style: italic; }
        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #9ca3af;
                  border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>

{{-- ── Page Header ── --}}
<div class="page-header">
    <div class="school-name">{{ config('app.name', 'Homeschooling Sriwijaya Kids') }}</div>
    <div class="doc-title">Raport Akademik Siswa</div>
</div>

{{-- ── Data Siswa ── --}}
<div class="student-card">
    <table>
        <tr>
            <td class="lbl">Nama Siswa</td>
            <td>: <strong>{{ $siswa->nama }}</strong></td>
            <td class="lbl">NIS</td>
            <td>: {{ $siswa->nis ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Jenis Kelamin</td>
            <td>: {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            <td class="lbl">Kelas Saat Ini</td>
            <td>: {{ $siswa->kelas?->nama_kelas ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Tanggal Cetak</td>
            <td>: {{ now()->format('d F Y') }}</td>
            <td></td><td></td>
        </tr>
    </table>
</div>

{{-- ── Raport Per Kelas (descending: terbaru → terlama) ── --}}
@foreach($raportData->reverse() as $kelasGroup)
@php
    $st = match($kelasGroup['status'] ?? '') {
        'naik'       => 'Naik Kelas',
        'tidak_naik' => 'Tidak Naik',
        'lulus'      => 'Lulus',
        'aktif'      => 'Aktif',
        default      => '-'
    };
@endphp

<div class="kelas-block">

    {{-- Kelas Header --}}
    <table class="kelas-header-table">
        <tr>
            <td class="kelas-header-td">
                <span class="kelas-nama">{{ $kelasGroup['nama_kelas'] }}</span>
                <span class="kelas-ta">| Tahun Ajaran: {{ $kelasGroup['tahun_ajaran'] }}</span>
            </td>
            <td class="kelas-status-td">
                <span class="status-badge">{{ $st }}</span>
            </td>
        </tr>
    </table>

    {{-- Semester 1 & 2 --}}
    <table class="sem-table">
        <tr>
            @foreach([1, 2] as $semNomor)
            @php $semData = $kelasGroup['per_semester'][$semNomor]; @endphp
            <td class="{{ $semNomor === 1 ? 'sem-cell' : 'sem-cell-last' }}">

                {{-- Semester sub-header --}}
                <table class="sem-sub-table">
                    <tr>
                        <td class="sem-name-td">{{ $semData['semester_nama'] }}</td>
                        @if($semData['avg_nilai'] !== null)
                        <td class="sem-avg-td">
                            Rata-rata: {{ number_format($semData['avg_nilai'], 2, ',', '.') }}
                            <span class="grade-badge grade-{{ $semData['avg_grade'] }}">{{ $semData['avg_grade'] }}</span>
                        </td>
                        @else
                        <td class="sem-empty-td">Belum ada nilai</td>
                        @endif
                    </tr>
                </table>

                {{-- Nilai --}}
                @if($semData['mapel_rows']->isNotEmpty())
                <table class="nilai-table">
                    <thead>
                        <tr>
                            <th>Mata Pelajaran</th>
                            <th>Tugas</th>
                            <th>UTS</th>
                            <th>UAS</th>
                            <th>Akhir</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($semData['mapel_rows'] as $mRow)
                        <tr>
                            <td>{{ $mRow['mapel_nama'] }}</td>
                            <td>{{ $mRow['nilai_tugas'] !== null ? number_format((float)$mRow['nilai_tugas'], 1, ',', '.') : '-' }}</td>
                            <td>{{ $mRow['nilai_uts']   !== null ? number_format((float)$mRow['nilai_uts'],   1, ',', '.') : '-' }}</td>
                            <td>{{ $mRow['nilai_uas']   !== null ? number_format((float)$mRow['nilai_uas'],   1, ',', '.') : '-' }}</td>
                            <td><strong>{{ $mRow['nilai_akhir'] !== null ? number_format((float)$mRow['nilai_akhir'], 2, ',', '.') : '-' }}</strong></td>
                            <td>
                                @if($mRow['grade'])
                                <span class="grade-badge grade-{{ $mRow['grade'] }}">{{ $mRow['grade'] }}</span>
                                @else -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    @if($semData['avg_nilai'] !== null)
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align:right;">Rata-rata</td>
                            <td>{{ number_format($semData['avg_nilai'], 2, ',', '.') }}</td>
                            <td><span class="grade-badge grade-{{ $semData['avg_grade'] }}">{{ $semData['avg_grade'] }}</span></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
                @else
                <div class="empty-semester">Belum ada nilai pada semester ini</div>
                @endif

            </td>
            @endforeach
        </tr>
    </table>

</div>
@endforeach

<div class="footer">
    Dicetak: {{ now()->format('d F Y H:i') }} &nbsp;|&nbsp; {{ config('app.name') }}
</div>

</body>
</html>
