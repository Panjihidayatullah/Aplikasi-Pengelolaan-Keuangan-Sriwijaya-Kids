@extends('layouts.app')

@section('title', 'Edit Siswa - ' . config('app.name'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-semibold text-gray-900">Edit Data Siswa</h2>
            <p class="mt-1 text-sm text-gray-600">Update informasi siswa</p>
        </div>
    </div>

    @if($siswa)
    <!-- Form -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form action="{{ route('siswa.update', $siswa->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- NIS -->
                    <div>
                        <label for="nis" class="block text-base font-medium text-gray-700 mb-2">NIS <span class="text-red-500">*</span></label>
                        <input type="text" name="nis" id="nis" value="{{ old('nis', $siswa->nis) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4">
                        @error('nis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama -->
                    <div>
                        <label for="nama" class="block text-base font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $siswa->nama) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4">
                        @error('nama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kelas -->
                    <div>
                        <label for="kelas_id" class="block text-base font-medium text-gray-700 mb-2">Kelas <span class="text-red-500">*</span></label>
                        <select name="kelas_id" id="kelas_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4">
                            <option value="">Pilih Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ old('kelas_id', $siswa->kelas_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                        @error('kelas_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="jenis_kelamin" class="block text-base font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-base font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4">
                        @error('tanggal_lahir')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telepon -->
                    <div>
                        <label for="telepon" class="block text-base font-medium text-gray-700 mb-2">Telepon</label>
                        <input type="text" name="telepon" id="telepon" value="{{ old('telepon', $siswa->telepon) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4">
                        @error('telepon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email (readonly kecuali ubah akun dicentang) -->
                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">Email Akun</label>
                        @php $emailAkun = $siswa->user?->email ?? $siswa->email; @endphp
                        <div id="email-display" class="mt-1 flex items-center gap-2">
                            <span class="block w-full rounded-lg border border-gray-200 bg-gray-50 py-3 px-4 text-base text-gray-600 select-none">
                                {{ $emailAkun ?? '(belum ada email)' }}
                            </span>
                            <span class="text-xs text-gray-400 whitespace-nowrap">🔒 terkunci</span>
                        </div>
                        {{-- Input email tersembunyi, hanya aktif jika ubah-akun dicentang --}}
                        <input type="email" name="email" id="email-input"
                               value="{{ old('email', $emailAkun) }}"
                               class="mt-1 hidden w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Akun Login Siswa -->
                    <div class="md:col-span-2 rounded-lg border border-blue-100 bg-blue-50 p-4">
                        <h3 class="text-base font-semibold text-blue-900">Akun Login Siswa</h3>
                        <p class="mt-1 text-xs text-blue-700">Email dan password akun login terkunci. Centang untuk mengubahnya.</p>
                        <div class="mt-2 text-xs text-slate-600">
                            Status akun: <span class="font-semibold">{{ $siswa->user_id ? 'Sudah terhubung' : 'Belum ada akun login' }}</span>
                            @if($siswa->user)
                            <span class="ml-2">(Email akun: {{ $siswa->user->email }})</span>
                            @endif
                        </div>

                        {{-- Checkbox: ubah email & password --}}
                        <input type="hidden" name="buat_akun_login" value="0">
                        <label class="mt-3 inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="ubah-akun-cb" name="buat_akun_login" value="1"
                                   {{ old('buat_akun_login') == '1' ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-5 h-5">
                            <span class="ml-3 text-sm font-medium text-gray-700">Ubah email / password akun login siswa</span>
                        </label>

                        {{-- Field email baru & password — tersembunyi secara default --}}
                        <div id="akun-fields" class="{{ old('buat_akun_login') == '1' ? '' : 'hidden' }} mt-3 space-y-3 max-w-md">
                            <div>
                                <label for="email-input" class="block text-sm font-medium text-gray-700 mb-1">Email Baru</label>
                                {{-- email-input sudah dideklarasikan di atas --}}
                            </div>
                            <div>
                                <label for="password_akun" class="block text-sm font-medium text-gray-700 mb-1">Password Baru <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                <input type="text" name="password_akun" id="password_akun"
                                       value="{{ old('password_akun') }}"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4"
                                       placeholder="Kosongkan untuk tidak mengubah password">
                                @error('password_akun')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <script>
                    (function () {
                        const cb       = document.getElementById('ubah-akun-cb');
                        const fields   = document.getElementById('akun-fields');
                        const display  = document.getElementById('email-display');
                        const emailIn  = document.getElementById('email-input');

                        function toggle() {
                            if (cb.checked) {
                                fields.classList.remove('hidden');
                                display.classList.add('hidden');
                                emailIn.classList.remove('hidden');
                                emailIn.disabled = false;
                            } else {
                                fields.classList.add('hidden');
                                display.classList.remove('hidden');
                                emailIn.classList.add('hidden');
                                emailIn.disabled = true;
                            }
                        }

                        cb.addEventListener('change', toggle);
                        toggle(); // jalankan saat load
                    })();
                    </script>


                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-base font-medium text-gray-700 mb-2">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4">{{ old('alamat', $siswa->alamat) }}</textarea>
                        @error('alamat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Data Orang Tua -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Data Orang Tua</h3>
                    </div>

                    <!-- Nama Ayah -->
                    <div>
                        <label for="nama_ayah" class="block text-base font-medium text-gray-700 mb-2">Nama Ayah</label>
                        <input type="text" name="nama_ayah" id="nama_ayah" value="{{ old('nama_ayah', $siswa->nama_ayah) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4">
                        @error('nama_ayah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telepon Ayah -->
                    <div>
                        <label for="telepon_ayah" class="block text-base font-medium text-gray-700 mb-2">Telepon Ayah</label>
                        <input type="text" name="telepon_ayah" id="telepon_ayah" value="{{ old('telepon_ayah', $siswa->telepon_ayah) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4">
                        @error('telepon_ayah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Ibu -->
                    <div>
                        <label for="nama_ibu" class="block text-base font-medium text-gray-700 mb-2">Nama Ibu</label>
                        <input type="text" name="nama_ibu" id="nama_ibu" value="{{ old('nama_ibu', $siswa->nama_ibu) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4">
                        @error('nama_ibu')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telepon Ibu -->
                    <div>
                        <label for="telepon_ibu" class="block text-base font-medium text-gray-700 mb-2">Telepon Ibu</label>
                        <input type="text" name="telepon_ibu" id="telepon_ibu" value="{{ old('telepon_ibu', $siswa->telepon_ibu) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4">
                        @error('telepon_ibu')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Foto -->
                    <div class="md:col-span-2">
                        <label for="foto" class="block text-base font-medium text-gray-700 mb-2">Foto Siswa</label>
                        @if($siswa->foto)
                            <div class="mt-2 mb-2">
                                <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Current photo" class="h-20 w-20 rounded-full object-cover">
                                <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                            </div>
                        @endif
                        <input type="file" name="foto" id="foto" accept="image/*" class="mt-1 block w-full text-base text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-base file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah foto</p>
                        @error('foto')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Aktif -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $siswa->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-5 h-5">
                            <span class="ml-3 text-base text-gray-700">Siswa Aktif</span>
                        </label>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('siswa.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Update
                    </button>
                </div>
            </form>
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