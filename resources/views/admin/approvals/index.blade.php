@extends('layouts.app')

@section('title', 'Persetujuan Pendaftaran Akun - ' . config('app.name'))

@section('header')
    <h2 class="text-2xl font-bold leading-tight text-slate-800">
        {{ __('Persetujuan Akun Baru') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        
        <div class="overflow-hidden bg-white shadow-xl sm:rounded-2xl border border-slate-100">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Daftar Akun Menunggu Persetujuan
                </h3>
            </div>

            <div class="p-6">
                @if($pendingUsers->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-slate-900">Tidak ada pendaftar baru</h3>
                        <p class="mt-1 text-sm text-slate-500">Saat ini tidak ada antrean akun yang perlu disetujui.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                                <tr>
                                    <th scope="col" class="px-4 py-3 font-semibold border-b border-slate-200">User / Waktu Daftar</th>
                                    <th scope="col" class="px-4 py-3 font-semibold border-b border-slate-200">Role Diminta</th>
                                    <th scope="col" class="px-4 py-3 font-semibold border-b border-slate-200">Keterangan / Alasan</th>
                                    <th scope="col" class="px-4 py-3 font-semibold border-b border-slate-200 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($pendingUsers as $user)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-4 py-4">
                                            <div class="font-medium text-slate-800">{{ $user->name }}</div>
                                            <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                            <div class="text-xs text-slate-400 mt-1">{{ $user->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-sm text-slate-700 italic max-w-xs break-words">
                                                "{{ $user->keterangan_registrasi ?? 'Tidak ada keterangan' }}"
                                            </p>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="flex justify-center space-x-2">
                                                <!-- Form Terima -->
                                                <form action="{{ route('admin.approvals.approve', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI akun ini?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Terima
                                                    </button>
                                                </form>

                                                <!-- Form Tolak -->
                                                <form action="{{ route('admin.approvals.reject', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK dan MENGHAPUS pendaftar ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg shadow-sm text-red-600 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $pendingUsers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
