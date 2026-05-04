@extends('layouts.app')

@section('title', 'Kenaikan Kelas')

@section('content')
<div class="container mx-auto px-4 py-6">
    @php
        $normalizedRoleNames = auth()->user()->getRoleNames()->map(function ($role) {
            $normalized = strtolower((string) $role);

            return preg_replace('/[^a-z0-9]/', '', $normalized);
        });
        $isKepalaSekolahRole = $normalizedRoleNames->contains('kepalasekolah');

        $isGuru = auth()->user()->hasRole('Guru');
        $canCreate = (can_access('process kenaikan-kelas') || is_admin() || $isGuru) && !$isKepalaSekolahRole;
        $canApprove = (can_access('approve kenaikan-kelas') || is_admin() || $isGuru) && !$isKepalaSekolahRole;
        $canView = can_access('view kenaikan-kelas') || is_admin() || $isKepalaSekolahRole || $isGuru;
        $canEdit = (can_access('manage kenaikan-kelas') || is_admin() || $isGuru) && !$isKepalaSekolahRole;
        $formatKelasInfo = function ($kelas) {
            if (!$kelas) {
                return '-';
            }

            $nama = $kelas->nama ?? $kelas->nama_kelas ?? '-';
            $tingkat = (int) ($kelas->tingkat ?: \App\Models\Kelas::inferTingkatFromNama((string) ($kelas->nama_kelas ?? $nama)));
            $rombel = '';
            if (preg_match('/([A-Za-z]+)$/', trim((string) ($kelas->nama_kelas ?? $nama)), $matches)) {
                $rombel = strtoupper((string) ($matches[1] ?? ''));
            }

            return trim($nama . ' (Tingkat ' . ($tingkat > 0 ? $tingkat : '-') . ($rombel !== '' ? ' - Rombel ' . $rombel : '') . ')');
        };
    @endphp

    <div class="mb-6 flex items-start justify-between gap-3 flex-wrap">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Kenaikan Kelas</h1>
            <p class="text-gray-600 mt-1">Proses kenaikan dan kelulusan siswa berdasarkan tingkatan dan rombel.</p>
        </div>
        <div class="inline-flex items-center gap-2">
            @if($canCreate)
            <a href="{{ route('akademik.kenaikan-kelas.proses-rombel') }}" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition">
                Proses Per Rombel
            </a>
            <a href="{{ route('akademik.kenaikan-kelas.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                Proses Manual
            </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama siswa, NIS, atau kelas" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Status</option>
                    <option value="naik" @selected(request('status') === 'naik')>Naik</option>
                    <option value="tidak_naik" @selected(request('status') === 'tidak_naik')>Tidak Naik</option>
                    <option value="lulus" @selected(request('status') === 'lulus')>Lulus</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tingkatan</label>
                <select name="tingkat" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Tingkatan</option>
                    @foreach($tingkatOptions as $tingkat)
                    <option value="{{ $tingkat }}" @selected((string) request('tingkat') === (string) $tingkat)>Tingkat {{ $tingkat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Rombel</label>
                <select name="rombel" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Rombel</option>
                    @foreach($rombelOptions as $rombel)
                    <option value="{{ $rombel->id }}" @selected((string) request('rombel') === (string) $rombel->id)>{{ $rombel->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" @selected((string) request('tahun_ajaran_id') === (string) $ta->id)>{{ $ta->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-5 flex gap-2">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">Filter</button>
                <a href="{{ route('akademik.kenaikan-kelas.index') }}" class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition">Reset</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-4">
            <p class="text-sm text-green-600 font-semibold">Naik Kelas</p>
            <p class="text-2xl font-bold text-green-700">{{ $totalNaik ?? 0 }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-lg p-4">
            <p class="text-sm text-red-600 font-semibold">Tidak Naik</p>
            <p class="text-2xl font-bold text-red-700">{{ $totalTidakNaik ?? 0 }}</p>
        </div>
        <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 border border-cyan-200 rounded-lg p-4">
            <p class="text-sm text-cyan-600 font-semibold">Lulus</p>
            <p class="text-2xl font-bold text-cyan-700">{{ $totalLulus ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b flex flex-wrap items-center justify-between gap-4 bg-gray-50">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Daftar Kenaikan Kelas</h2>
                <p class="text-sm text-gray-600">Daftar siswa yang telah diproses kenaikan kelasnya. Klik Terapkan untuk memindahkan siswa secara definitif.</p>
            </div>
            @if($canApprove)
            <div class="flex items-center gap-3">
                <form id="form-bulk-approve" method="POST" action="{{ route('akademik.kenaikan-kelas.bulk-approve') }}">
                    @csrf
                    <!-- inputs will be appended here via js -->
                    <button type="submit" id="btn-bulk-approve" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-sm transition" disabled>
                        Terapkan Massal (<span id="count-selected">0</span>)
                    </button>
                </form>
            </div>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        @if($canApprove)
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800 w-12">
                            <input type="checkbox" id="check-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        @endif
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">NIS</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Nama Siswa</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Kelas Saat Ini</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Kelas Tujuan</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Rata-rata Nilai</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kenaikans as $item)
                    <tr class="border-b hover:bg-gray-50 transition cursor-pointer row-clickable">
                        @if($canApprove)
                        <td class="px-6 py-4" onclick="event.stopPropagation();">
                            @if(!$item->is_applied)
                            <input type="checkbox" name="selected_ids[]" value="{{ $item->id }}" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            @endif
                        </td>
                        @endif
                        <td class="px-6 py-4 text-gray-600 font-semibold text-sm">{{ $item->siswa->nis ?? optional($item->siswa->kartuPelajar->first())->nis_otomatis ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-800 font-semibold">{{ $item->siswa->nama ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $formatKelasInfo($item->kelasSekarang) }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $formatKelasInfo($item->kelasTujuan) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1
                                @if((float) $item->rata_rata_nilai >= 85) bg-green-100 text-green-800
                                @elseif((float) $item->rata_rata_nilai >= 70) bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800 @endif
                                rounded text-xs font-bold">
                                {{ number_format((float) $item->rata_rata_nilai, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1
                                @if($item->status === 'naik') bg-green-100 text-green-800
                                @elseif($item->status === 'lulus') bg-cyan-100 text-cyan-800
                                @elseif($item->status === 'tidak_naik') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif
                                rounded text-xs font-semibold">
                                @if($item->status === 'naik') Naik Kelas
                                @elseif($item->status === 'lulus') Lulus
                                @elseif($item->status === 'tidak_naik') Tidak Naik
                                @else {{ ucfirst(str_replace('_', ' ', (string) $item->status)) }} @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="inline-flex items-center gap-3 whitespace-nowrap">
                                @if($canView)
                                <a href="{{ route('akademik.kenaikan-kelas.show', $item) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">Detail</a>
                                @endif

                                @if($canEdit)
                                <a href="{{ route('akademik.kenaikan-kelas.edit', $item) }}" class="text-yellow-600 hover:text-yellow-800 font-semibold text-sm">Edit</a>
                                @endif

                                @if($canApprove)
                                @if($item->is_applied)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-2.5 py-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Diterapkan
                                </span>
                                @else
                                <form method="POST" action="{{ route('akademik.kenaikan-kelas.approve', $item) }}" class="inline" onsubmit="return confirm('Terapkan proses ini ke data kelas siswa?')">
                                    @csrf
                                    <button type="submit" class="bg-transparent border-0 p-0 appearance-none shadow-none text-green-600 hover:text-green-800 font-semibold text-sm">Terapkan</button>
                                </form>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $canApprove ? 8 : 7 }}" class="px-6 py-8 text-center text-gray-500">Belum ada data kenaikan kelas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($kenaikans->hasPages())
    <div class="mt-6">
        {{ $kenaikans->links() }}
    </div>
    @endif
</div>

@if($canApprove)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check-all');
        const rowCheckboxes = Array.from(document.querySelectorAll('.row-checkbox'));
        const btnBulkApprove = document.getElementById('btn-bulk-approve');
        const countSelectedSpan = document.getElementById('count-selected');
        const formBulkApprove = document.getElementById('form-bulk-approve');

        function updateState() {
            const checkedCount = rowCheckboxes.filter(cb => cb.checked).length;
            countSelectedSpan.textContent = checkedCount;
            btnBulkApprove.disabled = checkedCount === 0;
            
            if (checkAll && rowCheckboxes.length > 0) {
                checkAll.checked = checkedCount === rowCheckboxes.length;
                checkAll.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                rowCheckboxes.forEach(cb => {
                    cb.checked = checkAll.checked;
                });
                updateState();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateState);
        });

        // Make whole row clickable
        const rows = document.querySelectorAll('.row-clickable');
        rows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't toggle if clicking on links or buttons inside the row
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
                    return;
                }
                const checkbox = this.querySelector('.row-checkbox');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    updateState();
                }
            });
        });

        formBulkApprove.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const selectedIds = rowCheckboxes.filter(cb => cb.checked).map(cb => cb.value);
            if (selectedIds.length === 0) return;

            if (!confirm(`Terapkan massal proses ini ke ${selectedIds.length} data kelas siswa?`)) {
                return;
            }

            // Remove previous hidden inputs
            formBulkApprove.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());

            // Add new hidden inputs
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                formBulkApprove.appendChild(input);
            });

            formBulkApprove.submit();
        });
    });
</script>
@endif
@endsection
