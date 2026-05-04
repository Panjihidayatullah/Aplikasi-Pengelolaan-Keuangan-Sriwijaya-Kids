<div class="grid grid-cols-1 gap-5 mb-6 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-cyan-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Kelas Aktif</p>
        <p class="mt-2 text-2xl font-bold text-blue-900">{{ $siswaMetrics['kelas'] ?? '-' }}</p>
        <p class="mt-1 text-xs text-blue-700">Kelas utama Anda saat ini</p>
    </div>

    <div class="rounded-2xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-sky-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Jadwal Aktif</p>
        <p class="mt-2 text-3xl font-bold text-indigo-900">{{ number_format($siswaMetrics['jadwal_aktif'] ?? 0) }}</p>
        <p class="mt-1 text-xs text-indigo-700">Total sesi pelajaran berjalan</p>
    </div>

    <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-teal-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Ujian Mendatang</p>
        <p class="mt-2 text-3xl font-bold text-emerald-900">{{ number_format($siswaMetrics['ujian_mendatang'] ?? 0) }}</p>
        <p class="mt-1 text-xs text-emerald-700">Ujian yang akan diikuti</p>
    </div>

    <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Progress Tugas</p>
        <p class="mt-2 text-3xl font-bold text-amber-900">{{ number_format($siswaMetrics['tugas_selesai'] ?? 0) }}/{{ number_format($siswaMetrics['total_tugas'] ?? 0) }}</p>
        <p class="mt-1 text-xs text-amber-700">Tugas selesai dari total tugas</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-5 mb-6 lg:grid-cols-3">
    <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col h-full">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-slate-900">Distribusi Jadwal Mingguan</h3>
            <p class="text-xs text-slate-500">Jumlah mata pelajaran aktif per hari</p>
        </div>
        <div class="mt-auto h-72 flex items-end">
            <canvas id="siswaJadwalChart" class="w-full h-60"></canvas>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col h-full">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-slate-900">Progress Pengumpulan</h3>
            <p class="text-xs text-slate-500">Status penyelesaian tugas</p>
        </div>
        <div class="h-56">
            <canvas id="siswaTugasProgressChart"></canvas>
        </div>
        <div style="margin-top:auto; padding-top:1rem; display:flex; gap:0.75rem; align-items:stretch;">
            <div style="flex:1; border-radius:0.75rem; border:1px solid #a7f3d0; background:#ecfdf5; padding:0.65rem 0.75rem; text-align:center;">
                <p style="font-size:0.76rem; font-weight:600; color:#065f46; display:flex; align-items:center; justify-content:center; gap:0.4rem;">
                    <span style="display:inline-block; width:0.6rem; height:0.6rem; border-radius:999px; background:#10b981;"></span>
                    Selesai
                </p>
                <p style="margin-top:0.2rem; font-size:1.05rem; font-weight:700; color:#065f46;" id="siswaProgressPctSelesai">0%</p>
            </div>
            <div style="flex:1; border-radius:0.75rem; border:1px solid #fecaca; background:#fef2f2; padding:0.65rem 0.75rem; text-align:center;">
                <p style="font-size:0.76rem; font-weight:600; color:#991b1b; display:flex; align-items:center; justify-content:center; gap:0.4rem;">
                    <span style="display:inline-block; width:0.6rem; height:0.6rem; border-radius:999px; background:#ef4444;"></span>
                    Belum Dikumpulkan
                </p>
                <p style="margin-top:0.2rem; font-size:1.05rem; font-weight:700; color:#991b1b;" id="siswaProgressPctBelum">0%</p>
            </div>
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
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Mapel</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Deadline</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($siswaUpcomingTugas as $tugas)
                    @php
                        $deadline = $tugas->tanggal_deadline ?? $tugas->deadline;
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $tugas->judul ?? $tugas->judul_tugas ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $tugas->mataPelajaran->nama_mapel ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $deadline ? \Carbon\Carbon::parse($deadline)->format('d M Y H:i') : '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('akademik.lms.tugas.show', $tugas) }}" class="inline-flex rounded-lg bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-200">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada tugas dengan deadline terdekat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
