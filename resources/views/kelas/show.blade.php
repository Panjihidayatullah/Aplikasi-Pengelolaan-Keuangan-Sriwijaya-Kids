@extends('layouts.app')

@section('title', 'Rombel Kelas - ' . ($kelas->nama_kelas ?? '-'))
@section('page-title', 'Rombel Kelas')

@section('content')
<div class="space-y-6" data-table-slider-ignore>
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Rombel {{ $kelas->nama_kelas }}</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola anggota siswa di kelas ini: tambah, edit, hapus, dan status aktif.</p>
        </div>
        <a href="{{ route('kelas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-200 transition">
            Kembali ke Data Kelas
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600">Total Siswa</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $totalSiswa }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600">Siswa Aktif</p>
            <p class="mt-2 text-2xl font-bold text-green-700">{{ $totalAktif }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600">Siswa Tidak Aktif</p>
            <p class="mt-2 text-2xl font-bold text-red-700">{{ $totalTidakAktif }}</p>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Pilih Siswa Dengan Ceklis</h3>
            <p class="text-sm text-gray-600 mt-1">Pilih siswa yang belum punya kelas. Jika siswa sudah di kelas lain, ceklis tidak bisa dipilih sampai dikeluarkan dari kelas asal.</p>
        </div>

        <div class="p-6 border-b border-gray-100 bg-gray-50">
            <form method="GET" action="{{ route('kelas.show', $kelas->id) }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Kandidat Siswa</label>
                    <input type="text" name="candidate_search" value="{{ request('candidate_search') }}" placeholder="Cari NIS atau nama siswa..." class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold text-sm">Cari</button>
                    <a href="{{ route('kelas.show', [$kelas->id, 'search' => request('search'), 'status' => request('status')]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold text-sm">Reset</a>
                </div>
            </form>
        </div>

        <form action="{{ route('kelas.siswa.store', $kelas->id) }}" method="POST" onsubmit="return confirm('Simpan siswa yang diceklis ke rombel ini?')">
            @csrf
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="candidate_search" value="{{ request('candidate_search') }}">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pilih</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Kelamin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas Saat Ini</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($kandidatSiswas as $kandidat)
                        @php
                            $hasValidKelas = !empty($kandidat->kelas_id) && $kandidat->kelas !== null;
                            $isCurrentClass = $hasValidKelas && (int) $kandidat->kelas_id === (int) $kelas->id;
                            $isOtherClass = $hasValidKelas && !$isCurrentClass;
                            $isSelectable = !$hasValidKelas;
                        @endphp
                        <tr class="hover:bg-gray-50 {{ $isSelectable ? 'cursor-pointer transition-colors' : '' }}"
                            @if($isSelectable) onclick="const cb = this.querySelector('input[type=checkbox]'); if(event.target.tagName !== 'INPUT' && event.target.tagName !== 'BUTTON' && event.target.tagName !== 'A' && cb) { cb.checked = !cb.checked; }" @endif>
                            <td class="px-6 py-4 text-sm">
                                <input
                                    type="checkbox"
                                    name="siswa_ids[]"
                                    value="{{ $kandidat->id }}"
                                    @checked(in_array((string) $kandidat->id, array_map('strval', old('siswa_ids', [])), true))
                                    @disabled(!$isSelectable)
                                >
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $kandidat->nis }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $kandidat->nama }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $kandidat->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($isCurrentClass)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $kandidat->kelas->nama_kelas ?? $kelas->nama_kelas }}
                                </span>
                                @elseif($isOtherClass)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                    {{ $kandidat->kelas->nama_kelas }}
                                </span>
                                @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Belum punya kelas
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($isOtherClass)
                                @php
                                    $kelasAsalNama = $kandidat->kelas->nama_kelas ?? 'kelas asal';
                                    $confirmTransferMessage = "Keluarkan siswa dari kelas {$kelasAsalNama} lalu pindahkan ke kelas ini?";
                                @endphp
                                <form action="{{ route('kelas.siswa.transfer', [$kelas->id, $kandidat->id]) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                    <input type="hidden" name="candidate_search" value="{{ request('candidate_search') }}">
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900 font-semibold" onclick='return confirm(@js($confirmTransferMessage))'>
                                        Keluarkan dari kelas lain
                                    </button>
                                </form>
                                @else
                                <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada kandidat siswa.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 transition">
                    Simpan Siswa Terceklis Ke Rombel
                </button>
                <p class="text-xs text-gray-500">Ceklis aktif untuk siswa yang belum punya kelas (termasuk data lama dengan relasi kelas tidak ditemukan). Jika siswa sudah terdaftar di kelas lain/kelas ini, ceklis nonaktif.</p>
            </div>
        </form>

        @if($kandidatSiswas->total() > $kandidatSiswas->perPage())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $kandidatSiswas->links('vendor.pagination.tailwind-no-summary') }}
        </div>
        @endif
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Rombel Kelas {{ $kelas->nama_kelas }}</h3>
            <p class="text-sm text-gray-600 mt-1">Daftar siswa yang saat ini terdaftar pada rombel ini.</p>
            <form method="GET" action="{{ route('kelas.show', $kelas->id) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Siswa</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIS atau nama siswa..." class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Semua</option>
                        <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                        <option value="tidak_aktif" @selected(request('status') === 'tidak_aktif')>Tidak Aktif</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold text-sm">Filter</button>
                    <a href="{{ route('kelas.show', $kelas->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold text-sm">Reset</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Kelamin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($siswas as $siswa)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $siswa->nis }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $siswa->nama }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $siswa->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $siswa->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $siswa->telepon ?: '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $siswa->email ?: '-' }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <button type="button" onclick="toggleEditForm({{ $siswa->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <form action="{{ route('kelas.siswa.destroy', [$kelas->id, $siswa->id]) }}" method="POST" class="inline" onsubmit="return confirm('Yakin keluarkan siswa ini dari rombel?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Keluarkan</button>
                            </form>
                        </td>
                    </tr>
                    <tr id="edit-row-{{ $siswa->id }}" class="hidden bg-gray-50">
                        <td colspan="7" class="px-6 py-4">
                            <form action="{{ route('kelas.siswa.update', [$kelas->id, $siswa->id]) }}" method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-3" onsubmit="return confirm('Simpan perubahan data siswa ini?')">
                                @csrf
                                @method('PUT')
                                <input type="text" name="nis" value="{{ $siswa->nis }}" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="NIS">
                                <input type="text" name="nama" value="{{ $siswa->nama }}" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Nama">
                                <select name="jenis_kelamin" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <option value="L" @selected($siswa->jenis_kelamin === 'L')>Laki-laki</option>
                                    <option value="P" @selected($siswa->jenis_kelamin === 'P')>Perempuan</option>
                                </select>
                                <input type="text" name="telepon" value="{{ $siswa->telepon }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Telepon">
                                <input type="email" name="email" value="{{ $siswa->email }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Email">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="inline-flex items-center gap-2 text-xs font-semibold text-gray-700">
                                        <input type="checkbox" name="is_active" value="1" @checked($siswa->is_active)>
                                        Aktif
                                    </label>
                                    <button type="submit" class="px-1 py-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800">Simpan</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada siswa di rombel kelas ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($siswas->total() > $siswas->perPage())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $siswas->links('vendor.pagination.tailwind-no-summary') }}
        </div>
        @endif
    </div>
</div>

<script>
function toggleEditForm(id) {
    const row = document.getElementById('edit-row-' + id);
    if (!row) return;

    row.classList.toggle('hidden');
}
</script>
@endsection