@extends('layouts.app')

@section('title', 'Edit Role - ' . config('app.name'))
@section('page-title', 'Edit Role')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Edit Role: {{ $role->name }}</h3>
        </div>

        <form action="{{ route('roles.update', $role->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Role <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                    class="mt-1 block w-full px-4 py-3 text-base rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500">Contoh: Admin, Bendahara, Kepala Sekolah</p>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
                <div class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                    @if($permissions->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada permissions yang tersedia.</p>
                    @else
                        @foreach($permissions as $permission)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700">{{ ucwords(str_replace('.', ' ', $permission->name)) }}</span>
                        </label>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('roles.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700">
                    Update Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
