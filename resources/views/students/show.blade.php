@extends('layouts.app')

@section('title', 'Detail Siswa - ' . config('app.name'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Detail Siswa</h2>
                <p class="mt-1 text-sm text-gray-600">Informasi lengkap siswa</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('siswa.edit', $siswa->id ?? 1) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                    Edit
                </a>
                <a href="{{ route('siswa.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    @if($siswa)
    <!-- Student Info -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Photo & Status -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-center">
                    @if($siswa->foto)
                        <img src="{{ asset('storage/' . $siswa->foto) }}" alt="{{ $siswa->nama }}" class="w-32 h-32 rounded-full mx-auto object-cover">
                    @else
                        <div class="w-32 h-32 rounded-full mx-auto bg-gray-200 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    @endif
                    <h3 class="mt-4 text-xl font-semibold text-gray-900">{{ $siswa->nama }}</h3>
                    <p class="text-sm text-gray-500">NIS: {{ $siswa->nis }}</p>
                    <span class="mt-2 inline-flex px-3 py-1 text-xs leading-5 font-semibold rounded-full {{ $siswa->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $siswa->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Pribadi</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">NIS</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $siswa->nis }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $siswa->nama }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kelas</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $siswa->kelas->nama_kelas ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Jenis Kelamin</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tanggal Lahir</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $siswa->tanggal_lahir ? format_date_indonesia($siswa->tanggal_lahir) : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Telepon</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $siswa->telepon ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $siswa->alamat ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $siswa->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Akun Login</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $siswa->user ? 'Tersedia (' . $siswa->user->email . ')' : 'Belum dibuat' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Parent Information -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Orang Tua</h3>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Father Info -->
                <div class="border rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Data Ayah</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $siswa->nama_ayah ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Telepon</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $siswa->telepon_ayah ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Mother Info -->
                <div class="border rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Data Ibu</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $siswa->nama_ibu ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Telepon</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $siswa->telepon_ibu ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-center">
            <p class="text-gray-500">Data siswa tidak ditemukan.</p>
            <a href="{{ route('siswa.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Kembali ke Daftar Siswa
            </a>
        </div>
    </div>
    @endif
</div>
@endsection