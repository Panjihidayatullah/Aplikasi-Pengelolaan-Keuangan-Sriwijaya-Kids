@extends('layouts.app')

@section('title', 'Pengaturan - ' . config('app.name'))
@section('page-title', 'Pengaturan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
        <div class="px-8 py-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
            <h3 class="text-2xl font-bold text-slate-800">Pengaturan</h3>
            <p class="mt-1 text-sm text-slate-500">Atur tema latar dan ukuran font aplikasi sesuai kenyamanan Anda.</p>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="rounded-xl border-2 border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-700 mb-3">Tema Background</p>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 text-sm text-slate-700">
                            <input type="radio" name="ui_theme" value="default" class="h-4 w-4 text-blue-600 border-slate-300" checked>
                            <span>Default</span>
                        </label>
                        <label class="flex items-center gap-3 text-sm text-slate-700">
                            <input type="radio" name="ui_theme" value="light" class="h-4 w-4 text-blue-600 border-slate-300">
                            <span>Terang</span>
                        </label>
                        <label class="flex items-center gap-3 text-sm text-slate-700">
                            <input type="radio" name="ui_theme" value="dark" class="h-4 w-4 text-blue-600 border-slate-300">
                            <span>Gelap</span>
                        </label>
                    </div>
                </div>

                <div class="rounded-xl border-2 border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-700 mb-3">Ukuran Font</p>
                    <div class="flex items-center gap-2 mb-3">
                        <button id="fontDecreaseBtn" type="button" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 transition">A-</button>
                        <div id="fontScaleLabel" class="min-w-[72px] text-center text-sm font-semibold text-slate-700">100%</div>
                        <button id="fontIncreaseBtn" type="button" class="px-3 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 transition">A+</button>
                        <button id="fontResetBtn" type="button" class="ml-auto px-3 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-sm font-semibold transition">Reset</button>
                    </div>
                    <div class="px-1">
                        <input
                            id="fontScaleSlider"
                            type="range"
                            min="85"
                            max="120"
                            step="1"
                            value="100"
                            class="w-full h-2 rounded-lg appearance-none cursor-pointer bg-slate-200 accent-blue-600"
                        >
                        <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                            <span>85%</span>
                            <span>120%</span>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">Rentang ukuran: 85% - 120%</p>
                </div>
            </div>

            <p id="uiPrefSavedHint" class="mt-4 text-sm text-green-600 font-medium hidden">Pengaturan tampilan disimpan.</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const themeRadios = Array.from(document.querySelectorAll('input[name="ui_theme"]'));
    const fontDecreaseBtn = document.getElementById('fontDecreaseBtn');
    const fontIncreaseBtn = document.getElementById('fontIncreaseBtn');
    const fontResetBtn = document.getElementById('fontResetBtn');
    const fontScaleSlider = document.getElementById('fontScaleSlider');
    const fontScaleLabel = document.getElementById('fontScaleLabel');
    const savedHint = document.getElementById('uiPrefSavedHint');

    if (!themeRadios.length || !fontDecreaseBtn || !fontIncreaseBtn || !fontResetBtn || !fontScaleLabel || !fontScaleSlider) {
        return;
    }

    const minScale = 85;
    const maxScale = 120;
    const step = 5;

    const clamp = (value) => Math.min(maxScale, Math.max(minScale, value));

    const getTheme = () => {
        try {
            const stored = localStorage.getItem('ui.theme');
            if (stored === 'light' || stored === 'dark' || stored === 'default') {
                return stored;
            }
        } catch (error) {
            // Ignore storage errors.
        }
        return 'default';
    };

    const getScale = () => {
        try {
            return clamp(Number(localStorage.getItem('ui.fontScale') || 100));
        } catch (error) {
            return 100;
        }
    };

    let currentTheme = getTheme();
    let currentScale = getScale();

    const showSavedHint = () => {
        if (!savedHint) return;
        savedHint.classList.remove('hidden');
        setTimeout(() => savedHint.classList.add('hidden'), 1200);
    };

    const syncUI = () => {
        themeRadios.forEach((radio) => {
            radio.checked = radio.value === currentTheme;
        });
        fontScaleLabel.textContent = currentScale + '%';
        fontScaleSlider.value = String(currentScale);
    };

    const persistAndApply = () => {
        try {
            localStorage.setItem('ui.theme', currentTheme);
            localStorage.setItem('ui.fontScale', String(currentScale));
        } catch (error) {
            // Ignore storage errors.
        }

        if (typeof window.__applyUiPreference === 'function') {
            window.__applyUiPreference(currentTheme, currentScale);
        }

        syncUI();
        showSavedHint();
    };

    themeRadios.forEach((radio) => {
        radio.addEventListener('change', function () {
            currentTheme = radio.value;
            persistAndApply();
        });
    });

    fontDecreaseBtn.addEventListener('click', function () {
        currentScale = clamp(currentScale - step);
        persistAndApply();
    });

    fontIncreaseBtn.addEventListener('click', function () {
        currentScale = clamp(currentScale + step);
        persistAndApply();
    });

    fontResetBtn.addEventListener('click', function () {
        currentScale = 100;
        persistAndApply();
    });

    fontScaleSlider.addEventListener('input', function () {
        currentScale = clamp(Number(fontScaleSlider.value));
        persistAndApply();
    });

    syncUI();
});
</script>
@endsection
