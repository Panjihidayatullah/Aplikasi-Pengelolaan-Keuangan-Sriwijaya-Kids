<div class="grid grid-cols-1 gap-5 mb-6 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-cyan-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Kelas Diampu</p>
        <p class="mt-2 text-3xl font-bold text-blue-900">{{ number_format($guruMetrics['kelas_diampu'] ?? 0) }}</p>
        <p class="mt-1 text-xs text-blue-700">Kelas aktif yang Anda ajar</p>
    </div>

    <div class="rounded-2xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-sky-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Jadwal Mengajar</p>
        <p class="mt-2 text-3xl font-bold text-indigo-900">{{ number_format($guruMetrics['jadwal_mengajar'] ?? 0) }}</p>
        <p class="mt-1 text-xs text-indigo-700">Total sesi pembelajaran aktif</p>
    </div>

    <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-teal-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Materi & Tugas</p>
        <p class="mt-2 text-3xl font-bold text-emerald-900">{{ number_format(($guruMetrics['materi'] ?? 0) + ($guruMetrics['tugas'] ?? 0)) }}</p>
        <p class="mt-1 text-xs text-emerald-700">Konten LMS yang sudah dibuat</p>
    </div>

    <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Perlu Dinilai</p>
        <p class="mt-2 text-3xl font-bold text-amber-900">{{ number_format($guruMetrics['belum_dinilai'] ?? 0) }}</p>
        <p class="mt-1 text-xs text-amber-700">Pengumpulan menunggu penilaian</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-5 mb-6 lg:grid-cols-3">
    <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-slate-900">Tren LMS Guru (6 Bulan)</h3>
            <p class="text-xs text-slate-500">Perbandingan tugas dibuat dan pengumpulan masuk</p>
        </div>
        <div class="h-64">
            <canvas id="guruLmsTrendChart"></canvas>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-slate-900">Status Penilaian</h3>
            <p class="text-xs text-slate-500">Dinilai vs belum dinilai</p>
        </div>
        <div class="h-64">
            <canvas id="guruPenilaianChart"></canvas>
        </div>
    </div>
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
        <h3 class="text-base font-semibold text-slate-900">Deadline Tugas Terdekat</h3>
        <a href="{{ route('akademik.lms.tugas.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Lihat Semua</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Judul</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Kelas</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Mapel</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Deadline</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($guruUpcomingTugas as $tugas)
                    @php
                        $deadline = $tugas->tanggal_deadline ?? $tugas->deadline;
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $tugas->judul ?? $tugas->judul_tugas ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $tugas->kelas->nama_kelas ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $tugas->mataPelajaran->nama_mapel ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $deadline ? \Carbon\Carbon::parse($deadline)->format('d M Y H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada deadline tugas terdekat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
