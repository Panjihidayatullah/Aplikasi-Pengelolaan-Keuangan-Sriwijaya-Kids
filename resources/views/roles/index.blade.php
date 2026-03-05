@extends('layouts.app')

@section('title', 'Role & Permission - ' . config('app.name'))
@section('page-title', 'Role & Permission')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Role & Permission</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola hak akses pengguna</p>
        </div>
        <a href="{{ route('roles.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Tambah Role</a>
    </div>

    @if($roles->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada role</h3>
        <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat role baru.</p>
        <div class="mt-6">
            <a href="{{ route('roles.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                Tambah Role
            </a>
        </div>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($roles as $role)
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">{{ $role->name }}</h3>
            <p class="text-sm text-gray-600 mt-1">{{ $role->permissions->count() }} permissions</p>
            <div class="mt-4 pt-4 border-t flex items-center justify-between">
                <a href="{{ route('roles.edit', $role->id) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Edit Permissions →</a>
                @if(!in_array($role->name, ['Admin', 'Bendahara', 'Kepala Sekolah']))
                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus role ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:text-red-900">Hapus</button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
