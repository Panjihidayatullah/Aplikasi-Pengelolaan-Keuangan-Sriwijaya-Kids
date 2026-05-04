@extends('layouts.app')

@section('title', 'Semester')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Semester</h1>
            <p class="text-gray-600 mt-1">Kelola periode semester akademik</p>
        </div>
        @if(can_access('create semester'))
        <a href="{{ route('akademik.semester.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition font-semibold">
            Tambah Semester
        </a>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Nama</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Tahun Ajaran</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Periode</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">UTS</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">UAS</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($semesters as $semester)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-800 font-semibold">Semester {{ $semester->nomor_semester }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $semester->tahunAjaran->nama ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ optional($semester->tanggal_mulai)->format('d M Y') }} - {{ optional($semester->tanggal_selesai)->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ optional($semester->tanggal_uts)->format('d M Y') ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ optional($semester->tanggal_uas)->format('d M Y') ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if($semester->is_active)
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Aktif</span>
                        @else
                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <div class="inline-flex items-center gap-3 whitespace-nowrap">
                            <a href="{{ route('akademik.semester.show', $semester) }}" class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Lihat</a>
                            @if(can_access('edit semester'))
                            <a href="{{ route('akademik.semester.edit', $semester) }}" class="text-yellow-500 hover:text-yellow-700 font-semibold text-sm">Edit</a>
                            @endif
                            @if(can_access('delete semester'))
                            <form method="POST" action="{{ route('akademik.semester.destroy', $semester) }}" class="inline-flex" onsubmit="return confirm('Yakin ingin menghapus semester ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-sm">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada data semester</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($semesters->hasPages())
    <div class="mt-6">{{ $semesters->links() }}</div>
    @endif
</div>
@endsection
