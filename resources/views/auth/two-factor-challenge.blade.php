@extends('layouts.guest')

@section('title', 'Two Factor Authentication - ' . config('app.name'))

@section('content')
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Two Factor Authentication</h2>
    <p class="text-sm text-gray-600 mb-6" x-show="!recovery">
        Please confirm access to your account by entering the authentication code provided by your authenticator application.
    </p>
    <p class="text-sm text-gray-600 mb-6" x-show="recovery" style="display: none;">
        Please confirm access to your account by entering one of your emergency recovery codes.
    </p>

    <form method="POST" action="{{ route('two-factor.login') }}">
        @csrf

        <!-- Code -->
        <div x-show="!recovery">
            <label for="code" class="block text-sm font-medium text-gray-700">Authentication Code</label>
            <input id="code" type="text" name="code" inputmode="numeric" autofocus autocomplete="one-time-code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('code')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Recovery Code -->
        <div x-show="recovery" style="display: none;">
            <label for="recovery_code" class="block text-sm font-medium text-gray-700">Recovery Code</label>
            <input id="recovery_code" type="text" name="recovery_code" autocomplete="one-time-code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('recovery_code')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Toggle Recovery -->
        <div class="mt-4">
            <button type="button" @click="recovery = !recovery" class="text-sm text-indigo-600 hover:text-indigo-500 underline">
                <span x-show="!recovery">Use a recovery code</span>
                <span x-show="recovery" style="display: none;">Use an authentication code</span>
            </button>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Login
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('twoFactor', () => ({
            recovery: false
        }))
    })
</script>
@endpush
@endsection