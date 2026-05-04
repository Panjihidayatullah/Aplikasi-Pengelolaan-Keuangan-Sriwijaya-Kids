@extends('layouts.app')

@section('title', 'Profil Saya - ' . config('app.name'))
@section('page-title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto">
    
    @if(session('status') === 'profile-updated')
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center mb-6">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Profil berhasil diperbarui!
        </div>
    @endif

    <!-- Single Unified Card -->
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
        
        <!-- Header -->
        <div class="px-8 py-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
            <h3 class="text-2xl font-bold text-slate-800">Profil Saya</h3>
            <p class="mt-1 text-sm text-slate-500">Kelola informasi profil dan keamanan akun Anda.</p>
        </div>

        <div class="p-8">
            <!-- Section 1: Informasi Profil -->
            <div class="mb-8">
                <h4 class="text-lg font-bold text-slate-800 mb-1">Informasi Profil</h4>
                <p class="text-sm text-slate-500 mb-4">Perbarui informasi profil dan alamat email akun Anda.</p>
                
                <form id="profile-form" action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-6">
                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Role
                            </label>
                            @php
                                $currentUser = auth()->user();
                                $roleNames = method_exists($currentUser, 'getRoleNames')
                                    ? $currentUser->getRoleNames()->values()
                                    : collect();

                                if ($roleNames->isEmpty() && filled($currentUser?->role)) {
                                    $roleNames = collect([(string) $currentUser->role]);
                                }

                                $roleClasses = [
                                    'Admin' => 'bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-800 border-2 border-purple-200',
                                    'Bendahara' => 'bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800 border-2 border-blue-200',
                                    'Kepala Sekolah' => 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border-2 border-green-200',
                                    'Guru' => 'bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-800 border-2 border-amber-200',
                                    'Siswa' => 'bg-gradient-to-r from-teal-100 to-emerald-100 text-teal-800 border-2 border-teal-200',
                                ];
                            @endphp
                            <div class="flex flex-wrap items-center gap-2">
                                @forelse($roleNames as $roleName)
                                    @php
                                        $roleStyle = $roleClasses[(string) $roleName] ?? 'bg-gradient-to-r from-slate-100 to-gray-100 text-slate-800 border-2 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold {{ $roleStyle }}">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $roleName }}
                                    </span>
                                @empty
                                    <span class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-slate-100 to-gray-100 text-slate-700 border-2 border-slate-200">
                                        Belum ada role
                                    </span>
                                @endforelse
                            </div>
                            <p class="mt-2 text-xs text-slate-500 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Role akun Anda menentukan akses ke berbagai fitur dan menu dalam sistem.
                            </p>
                        </div>

                        <!-- Nama -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', auth()->user()->name) }}" 
                                   required
                                   autofocus
                                   autocomplete="name"
                                   placeholder="Nama lengkap"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email', auth()->user()->email) }}" 
                                   required
                                   autocomplete="username"
                                   placeholder="email@example.com"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror

                            @if($mustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                                <div class="mt-2">
                                    <p class="text-sm text-gray-800">
                                        Email Anda belum diverifikasi.
                                        <button form="send-verification" class="underline text-sm text-blue-600 hover:text-blue-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Klik di sini untuk mengirim ulang email verifikasi.
                                        </button>
                                    </p>

                                    @if(session('status') === 'verification-link-sent')
                                        <p class="mt-2 font-medium text-sm text-green-600">
                                            Link verifikasi baru telah dikirim ke alamat email Anda.
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Divider -->
            <div class="border-t border-slate-200 my-8"></div>

            <!-- Section 2: Update Password -->
            <div class="mb-8">
                <h4 class="text-lg font-bold text-slate-800 mb-1">Update Password</h4>
                <p class="text-sm text-slate-500 mb-4">Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.</p>

                <form id="password-form" action="{{ route('user-password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-2">
                                Password Saat Ini <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   name="current_password" 
                                   id="current_password" 
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('current_password', 'updatePassword') border-red-500 @enderror">
                            @error('current_password', 'updatePassword')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                                Password Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   autocomplete="new-password"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('password', 'updatePassword') border-red-500 @enderror">
                            @error('password', 'updatePassword')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">
                                Konfirmasi Password Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   autocomplete="new-password"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('password_confirmation', 'updatePassword') border-red-500 @enderror">
                            @error('password_confirmation', 'updatePassword')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="border-t border-slate-200 pt-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <!-- Success Messages -->
                    <div class="flex-1 min-w-[200px]">
                        @if(session('status') === 'password-updated')
                            <p class="text-sm text-green-600 font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Password berhasil diperbarui!
                            </p>
                        @endif
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit" 
                                form="profile-form"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/40 hover:shadow-blue-600/50 transform hover:scale-[1.02] transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Simpan Perubahan
                        </button>

                        <button type="submit" 
                                form="password-form"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-500 hover:from-indigo-700 hover:to-purple-600 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/40 hover:shadow-indigo-600/50 transform hover:scale-[1.02] transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Update Password
                        </button>

                        <button type="button" 
                                onclick="showDeleteConfirmation()"
                                class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-lg shadow-red-500/40 hover:shadow-red-600/50 transform hover:scale-[1.02] transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Akun
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-2xl font-bold text-slate-800 text-center mb-2">Hapus Akun</h3>
            <p class="text-sm text-slate-600 text-center mb-6">Apakah Anda yakin ingin menghapus akun Anda?</p>
            
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <p class="text-sm text-red-800 mb-2">
                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                </p>
                <ul class="text-xs text-red-700 space-y-1 list-disc list-inside">
                    <li>Semua data akun Anda akan dihapus secara permanen</li>
                    <li>Anda akan kehilangan akses ke sistem</li>
                    <li>Riwayat aktivitas akan dihapus</li>
                </ul>
            </div>
            
            <div class="flex gap-3">
                <button type="button" 
                        onclick="hideDeleteConfirmation()"
                        class="flex-1 px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl transition-all duration-200">
                    Batal
                </button>
                <button type="button" 
                        onclick="confirmDelete()"
                        class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-lg shadow-red-500/40 transition-all duration-200">
                    Ya, Hapus Akun
                </button>
            </div>
        </div>
    </div>
</div>

<form id="delete-account-form" action="{{ route('profile.destroy') }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
function showDeleteConfirmation() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideDeleteConfirmation() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function confirmDelete() {
    document.getElementById('delete-account-form').submit();
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteConfirmation();
    }
});
</script>

@if($mustVerifyEmail && !auth()->user()->hasVerifiedEmail())
    <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="hidden">
        @csrf
    </form>
@endif
@endsection
