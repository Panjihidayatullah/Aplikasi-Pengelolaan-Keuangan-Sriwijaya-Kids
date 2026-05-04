@extends('layouts.app')

@section('title', 'Manajemen Ruang')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Manajemen Ruang</h1>
            <p class="text-gray-600 mt-1">Kelola data ruang kelas/lab agar penjadwalan tidak bentrok.</p>
        </div>
        <a href="{{ route('akademik.ruang.create') }}" class="inline-flex items-center px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
            Tambah Ruang
        </a>
    </div>

    @if($errors->has('ruang'))
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
        {{ $errors->first('ruang') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <div class="md:col-span-3">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Cari Ruang</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Kode, nama ruang, lokasi..." class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">Filter</button>
                <a href="{{ route('akademik.ruang.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Kode</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Nama Ruang</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Lokasi</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Kapasitas</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Dipakai Jadwal</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ruangs as $ruang)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-800 font-semibold">{{ $ruang->kode_ruang }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $ruang->nama_ruang }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $ruang->lokasi ?: '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $ruang->kapasitas ?: '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $ruang->jadwal_pelajaran_count }}</td>
                    <td class="px-6 py-4">
                        @if($ruang->is_active)
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Aktif</span>
                        @else
                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="inline-flex items-center gap-3 whitespace-nowrap">
                            <a href="{{ route('akademik.ruang.edit', $ruang) }}" class="text-yellow-500 hover:text-yellow-700 font-semibold text-sm">Edit</a>
                            <form method="POST" action="{{ route('akademik.ruang.destroy', $ruang) }}" class="inline-flex" onsubmit="return confirm('Yakin ingin menghapus ruang ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada data ruang.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ruangs->hasPages())
    <div class="mt-6">{{ $ruangs->links() }}</div>
    @endif
</div>
@endsection
