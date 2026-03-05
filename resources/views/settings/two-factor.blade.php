@extends('layouts.app')

@section('title', 'Two-Factor Authentication - ' . config('app.name'))
@section('page-title', 'Two-Factor Authentication')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
        <!-- Header -->
        <div class="px-8 py-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
            <h3 class="text-2xl font-bold text-slate-800">Two-Factor Authentication</h3>
            <p class="mt-1 text-sm text-slate-500">Tambahkan keamanan tambahan pada akun Anda dengan autentikasi dua faktor.</p>
        </div>

        <div class="p-8">
            @if($twoFactorEnabled)
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center mb-6">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Two-Factor Authentication sudah aktif
                </div>

                <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-lg shadow-red-500/40 hover:shadow-red-600/50 transform hover:scale-[1.02] transition-all duration-200">
                        🔓 Nonaktifkan Two-Factor Authentication
                    </button>
                </form>
            @else
                <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-6">
                    <p class="text-sm">
                        Two-Factor Authentication menambahkan lapisan keamanan tambahan pada akun Anda dengan memerlukan lebih dari sekadar password untuk login.
                    </p>
                </div>

                <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                    @csrf
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/40 hover:shadow-blue-600/50 transform hover:scale-[1.02] transition-all duration-200">
                        🔐 Aktifkan Two-Factor Authentication
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
