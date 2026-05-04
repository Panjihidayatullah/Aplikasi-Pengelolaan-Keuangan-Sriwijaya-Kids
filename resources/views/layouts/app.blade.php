<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <script>
        (function () {
            const root = document.documentElement;
            const allowedThemes = ['default', 'light', 'dark'];
            const minFontScale = 85;
            const maxFontScale = 120;

            const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

            window.__applyUiPreference = function (theme, fontScale) {
                const safeTheme = allowedThemes.includes(theme) ? theme : 'default';
                const safeFontScale = clamp(Number(fontScale) || 100, minFontScale, maxFontScale);

                root.setAttribute('data-ui-theme', safeTheme);
                root.style.fontSize = safeFontScale + '%';
            };

            try {
                const storedTheme = localStorage.getItem('ui.theme') || 'default';
                const storedFontScale = localStorage.getItem('ui.fontScale') || '100';
                window.__applyUiPreference(storedTheme, storedFontScale);
            } catch (error) {
                window.__applyUiPreference('default', 100);
            }
        })();
    </script>

    <style>
        html {
            min-height: 100%;
        }

        body {
            background: transparent !important;
        }

        html[data-ui-theme="default"] {
            background-color: #e3e9f7 !important;
        }

        html[data-ui-theme="light"] {
            background-color: #edf1f8 !important;
        }

        html[data-ui-theme="dark"] {
            background-color: #0f172a !important;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-full">
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen lg:pl-64">
            @include('layouts.topbar')

            <!-- Page Content -->
            <main class="flex-1 py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded" role="alert">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded" role="alert">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-gray-900/30 backdrop-blur-md z-[9999] flex items-center justify-center" style="display: none;">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 flex flex-col items-center space-y-4 max-w-sm mx-4">
            <!-- Spinner -->
            <div class="w-16 h-16 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>
            
            <!-- Text -->
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Sedang Memuat</h3>
                <p class="text-sm text-gray-600">Mohon tunggu sebentar...</p>
            </div>
        </div>
    </div>

    <!-- Loading Script -->
    <script>
        // Loading overlay element
        const loadingOverlay = document.getElementById('loading-overlay');
        let loadingTimer = null;

        function showLoading(immediate = false) {
            if (!loadingOverlay) return;

            if (loadingTimer) {
                clearTimeout(loadingTimer);
                loadingTimer = null;
            }

            if (immediate) {
                loadingOverlay.style.display = 'flex';
                return;
            }

            // Delay a bit to avoid flicker for very fast navigation.
            loadingTimer = setTimeout(function () {
                loadingOverlay.style.display = 'flex';
            }, 120);
        }

        function hideLoading() {
            if (!loadingOverlay) return;

            if (loadingTimer) {
                clearTimeout(loadingTimer);
                loadingTimer = null;
            }

            loadingOverlay.style.display = 'none';
        }
        
        // Flag: jika user mengklik link download/no-loader, jangan tampilkan loader di beforeunload
        let _skipNextBeforeUnload = false;

        // Show loading when page starts to unload (navigation/reload)
        window.addEventListener('beforeunload', function() {
            if (_skipNextBeforeUnload) {
                _skipNextBeforeUnload = false;
                return;
            }
            showLoading(true);
        });

        function shouldSkipLoaderForLink(link) {
            if (!link || !link.href) {
                return true;
            }

            if (link.hasAttribute('target') || link.hasAttribute('data-no-loader') || link.hasAttribute('download')) {
                return true;
            }

            let url;
            try {
                url = new URL(link.href, window.location.origin);
            } catch (error) {
                return true;
            }

            if (url.origin !== window.location.origin || url.hash) {
                return true;
            }

            const path = url.pathname.toLowerCase();
            const isExportLikePath = path.endsWith('.pdf') || 
                                     path.endsWith('.csv') || 
                                     path.endsWith('.xlsx') || 
                                     path.endsWith('.xls') || 
                                     path.includes('/pdf') || 
                                     path.includes('/export/');
            const isExportLikeQuery = url.searchParams.has('export') || url.searchParams.has('download');

            return isExportLikePath || isExportLikeQuery;
        }
        
        // Show loading when clicking links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link || shouldSkipLoaderForLink(link)) {
                // Set flag agar beforeunload tidak trigger loader
                _skipNextBeforeUnload = true;
                // Reset flag setelah 3 detik jika beforeunload tidak terpanggil
                setTimeout(function() { _skipNextBeforeUnload = false; }, 3000);
                return;
            }

            showLoading(false);
        });

        
        // Show loading when submitting forms
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form.hasAttribute('data-no-loader')) {
                showLoading(true);
            }
        });

        // Hide loading as soon as DOM is ready (faster than waiting all assets).
        document.addEventListener('DOMContentLoaded', function() {
            hideLoading();
        });
        
        // Hide loading when page is fully loaded
        window.addEventListener('load', function() {
            hideLoading();
        });

        // Handle browser bfcache restore (back/forward navigation)
        window.addEventListener('pageshow', function() {
            hideLoading();
        });
        
        // Hide loading if it's still showing after 4 seconds (safety timeout)
        setTimeout(function() {
            hideLoading();
        }, 4000);

        // Keep table height within viewport and navigate rows by slide-style paging.
        (function () {
            const tableStates = [];

            function shouldSkipTable(table) {
                if (!table || !table.querySelector('tbody')) return true;
                if (table.closest('[data-table-slider-ignore]')) return true;
                if (table.closest('.jadwal-slider')) return true;
                if (table.classList.contains('table-slider-ignore')) return true;
                return false;
            }

            function hasNativePagination(wrapper) {
                const scope = wrapper.parentElement || wrapper;
                return Boolean(
                    scope.querySelector('[aria-label="Pagination Navigation"]') ||
                    scope.querySelector('nav[role="navigation"]') ||
                    scope.querySelector('.pagination')
                );
            }

            function createControls(wrapper) {
                const host = wrapper.parentElement || wrapper;
                if (host.querySelector('[data-slide-generated="1"]')) {
                    return host.querySelector('[data-slide-generated="1"]');
                }

                const controls = document.createElement('div');
                controls.className = 'table-slide-controls hidden border-t border-gray-200 px-4 sm:px-6 py-3';
                controls.dataset.slideGenerated = '1';
                controls.innerHTML = [
                    '<div class="table-slide-summary" data-slide-summary>Showing 1 to 1 of 1 results</div>',
                    '<div class="table-slide-pager" style="background:#ffffff !important; background-color:#ffffff !important; border-color:#cbd5e1 !important;">',
                    '   <button type="button" class="table-slide-icon-btn" data-slide-prev aria-label="Slide sebelumnya" style="background:#ffffff !important; background-color:#ffffff !important; border-color:#bfdbfe !important; color:#1d4ed8 !important;"><span aria-hidden="true" style="background:transparent !important; background-color:transparent !important;">&#8249;</span></button>',
                    '   <div class="table-slide-pages" data-slide-pages style="background:#ffffff !important; background-color:#ffffff !important;"></div>',
                    '   <button type="button" class="table-slide-icon-btn" data-slide-next aria-label="Slide berikutnya" style="background:#ffffff !important; background-color:#ffffff !important; border-color:#bfdbfe !important; color:#1d4ed8 !important;"><span aria-hidden="true" style="background:transparent !important; background-color:transparent !important;">&#8250;</span></button>',
                    '</div>',
                ].join('');
                host.appendChild(controls);
                return controls;
            }

            function collectState(table) {
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll(':scope > tr'));
                if (!rows.length) return null;

                const wrapper = table.closest('.overflow-x-auto') || table.parentElement;
                if (!wrapper) return null;
                if (hasNativePagination(wrapper)) return null;

                const controls = createControls(wrapper);
                const prev = controls.querySelector('[data-slide-prev]');
                const next = controls.querySelector('[data-slide-next]');
                const pages = controls.querySelector('[data-slide-pages]');
                const summary = controls.querySelector('[data-slide-summary]');
                const pager = controls.querySelector('.table-slide-pager');

                const state = {
                    table,
                    tbody,
                    rows,
                    wrapper,
                    controls,
                    prev,
                    next,
                    pages,
                    summary,
                    pager,
                    page: 0,
                    perPage: rows.length,
                    totalPages: 1,
                };

                prev.addEventListener('click', function () {
                    state.page = Math.max(0, state.page - 1);
                    renderState(state);
                });

                next.addEventListener('click', function () {
                    state.page = Math.min(state.totalPages - 1, state.page + 1);
                    renderState(state);
                });

                return state;
            }

            function computePerPage(state) {
                const fixedRowsAttr = Number(state.table.getAttribute('data-slider-per-page'));
                if (Number.isFinite(fixedRowsAttr) && fixedRowsAttr > 0) {
                    return Math.max(1, Math.floor(fixedRowsAttr));
                }

                const maxRowsAttr = Number(state.table.getAttribute('data-slider-max-rows'));
                if (Number.isFinite(maxRowsAttr) && maxRowsAttr > 0) {
                    return Math.max(1, Math.floor(maxRowsAttr));
                }

                return 10;
            }

            function forcePagerButtonStyle(button, options = {}) {
                if (!button) return;

                const isActive = Boolean(options.active);
                const isDisabled = Boolean(options.disabled);

                const bgColor = '#ffffff';
                const textColor = isDisabled ? '#94a3b8' : '#1d4ed8';

                button.style.setProperty('background', bgColor, 'important');
                button.style.setProperty('background-color', bgColor, 'important');
                button.style.setProperty('color', textColor, 'important');
                button.style.setProperty('border-color', '#bfdbfe', 'important');
                button.style.setProperty('box-shadow', isActive ? 'inset 0 0 0 1px #93c5fd' : 'none', 'important');
                button.style.setProperty('opacity', '1', 'important');
                button.style.setProperty('appearance', 'none', 'important');
                button.style.setProperty('-webkit-appearance', 'none', 'important');
                button.style.setProperty('background-image', 'none', 'important');
            }

            function forcePagerContainerStyle(state) {
                if (!state) return;

                if (state.pager) {
                    state.pager.style.setProperty('background', '#ffffff', 'important');
                    state.pager.style.setProperty('background-color', '#ffffff', 'important');
                    state.pager.style.setProperty('border-color', '#cbd5e1', 'important');
                }

                if (state.pages) {
                    state.pages.style.setProperty('background', '#ffffff', 'important');
                    state.pages.style.setProperty('background-color', '#ffffff', 'important');
                    state.pages.style.setProperty('border-left-color', '#e2e8f0', 'important');
                    state.pages.style.setProperty('border-right-color', '#e2e8f0', 'important');
                }
            }

            function renderPageButtons(state) {
                if (!state.pages) return;

                const maxButtons = 5;
                let start = Math.max(0, state.page - Math.floor(maxButtons / 2));
                let end = Math.min(state.totalPages, start + maxButtons);
                if (end - start < maxButtons) {
                    start = Math.max(0, end - maxButtons);
                }

                const html = [];
                for (let i = start; i < end; i += 1) {
                    const isActive = i === state.page;
                    html.push(
                        `<button type="button" class="table-slide-page-btn${isActive ? ' is-active' : ''}" data-slide-page="${i}" ${isActive ? 'aria-current="page"' : ''} style="background:#ffffff !important; background-color:#ffffff !important; border-color:#bfdbfe !important; color:#1d4ed8 !important;">${i + 1}</button>`
                    );
                }

                state.pages.innerHTML = html.join('');
                state.pages.querySelectorAll('[data-slide-page]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        const target = Number(btn.getAttribute('data-slide-page'));
                        if (Number.isNaN(target)) return;
                        state.page = target;
                        renderState(state);
                    });

                    const target = Number(btn.getAttribute('data-slide-page'));
                    forcePagerButtonStyle(btn, {
                        active: target === state.page,
                        disabled: false,
                    });
                });
            }

            function renderState(state) {
                const totalRows = state.rows.length;
                if (!totalRows) return;

                forcePagerContainerStyle(state);
                state.controls.style.setProperty('background', '#ffffff', 'important');
                state.controls.style.setProperty('background-color', '#ffffff', 'important');

                state.perPage = computePerPage(state);
                state.totalPages = Math.max(1, Math.ceil(totalRows / state.perPage));
                state.page = Math.min(state.page, state.totalPages - 1);

                const start = state.page * state.perPage;
                const end = start + state.perPage;

                state.rows.forEach(function (row, index) {
                    row.style.display = index >= start && index < end ? '' : 'none';
                });

                const visibleStart = totalRows ? start + 1 : 0;
                const visibleEnd = Math.min(end, totalRows);
                if (state.summary) {
                    state.summary.textContent = `Showing ${visibleStart} to ${visibleEnd} of ${totalRows} results`;
                }

                const isSlidingNeeded = state.totalPages > 1;
                state.controls.classList.toggle('hidden', !isSlidingNeeded);
                if (state.pager) {
                    state.pager.classList.toggle('hidden', !isSlidingNeeded);
                }

                renderPageButtons(state);
                if (state.prev) {
                    state.prev.disabled = state.page === 0;
                    forcePagerButtonStyle(state.prev, {
                        active: false,
                        disabled: state.prev.disabled,
                    });
                }
                if (state.next) {
                    state.next.disabled = state.page >= state.totalPages - 1;
                    forcePagerButtonStyle(state.next, {
                        active: false,
                        disabled: state.next.disabled,
                    });
                }

                state.controls.querySelectorAll('.table-slide-icon-btn > span').forEach(function (icon) {
                    icon.style.setProperty('background', 'transparent', 'important');
                    icon.style.setProperty('background-color', 'transparent', 'important');
                });

                state.controls.querySelectorAll('.table-slide-pager, .table-slide-pages, .table-slide-icon-btn, .table-slide-page-btn').forEach(function (el) {
                    el.style.setProperty('background', '#ffffff', 'important');
                    el.style.setProperty('background-color', '#ffffff', 'important');
                });
            }

            function initTableSlider() {
                const tables = Array.from(document.querySelectorAll('main table'));
                tables.forEach(function (table) {
                    if (shouldSkipTable(table)) return;
                    if (table.dataset.sliderReady === '1') return;

                    const state = collectState(table);
                    if (!state) return;

                    table.dataset.sliderReady = '1';
                    tableStates.push(state);
                    renderState(state);
                });
            }

            let resizeRaf = null;
            window.addEventListener('resize', function () {
                if (resizeRaf) return;
                resizeRaf = window.requestAnimationFrame(function () {
                    tableStates.forEach(renderState);
                    resizeRaf = null;
                });
            });

            document.addEventListener('DOMContentLoaded', initTableSlider);
            document.addEventListener('livewire:navigated', initTableSlider);
        })();
    </script>

    <style>
        .table-slide-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .table-slide-summary {
            color: #1e3a8a;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .table-slide-pager {
            display: inline-flex;
            align-items: center;
            border: 1px solid #cbd5e1;
            border-radius: 0.625rem;
            overflow: hidden;
            background: #ffffff !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .table-slide-pager button,
        .table-slide-pager button:hover,
        .table-slide-pager button:focus,
        .table-slide-pager button:active {
            background: #ffffff !important;
            background-color: #ffffff !important;
            color: #1d4ed8 !important;
            border-color: #bfdbfe !important;
            box-shadow: none !important;
        }

        .table-slide-pages {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff !important;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }

        .table-slide-icon-btn {
            border: 0;
            background: #ffffff !important;
            color: #334155;
            min-width: 2.6rem;
            height: 2.25rem;
            font-size: 1.5rem;
            font-weight: 500;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .18s ease;
        }

        .table-slide-icon-btn:hover:not(:disabled) {
            background: #ffffff !important;
            color: #1e293b;
        }

        .table-slide-icon-btn:disabled {
            opacity: 1;
            color: #94a3b8 !important;
            background: #ffffff !important;
            cursor: not-allowed;
        }

        .table-slide-page-btn {
            min-width: 2.6rem;
            height: 2.25rem;
            border: 0;
            background: #ffffff !important;
            color: #334155;
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .18s ease;
        }

        .table-slide-page-btn:hover {
            background: #ffffff !important;
        }

        .table-slide-page-btn.is-active {
            background: #ffffff !important;
            color: #1e3a8a;
            box-shadow: inset 0 0 0 1px #93c5fd;
        }

        .table-slide-page-btn[aria-current="page"] {
            background: #ffffff !important;
            color: #1d4ed8 !important;
            box-shadow: inset 0 0 0 1px #93c5fd !important;
        }

        nav[role="navigation"] a[rel="next"],
        nav[role="navigation"] a[rel="prev"],
        nav[role="navigation"] a[aria-label*="next" i],
        nav[role="navigation"] a[aria-label*="berikut" i],
        nav[role="navigation"] span[aria-current="page"] > span,
        nav[role="navigation"] span[aria-disabled="true"] > span {
            background: #ffffff !important;
            background-color: #ffffff !important;
            border-color: #bfdbfe !important;
            color: #1d4ed8 !important;
            box-shadow: none !important;
        }

        nav[role="navigation"] a[rel="next"]:hover,
        nav[role="navigation"] a[rel="next"]:focus,
        nav[role="navigation"] a[rel="next"]:active,
        nav[role="navigation"] a[rel="prev"]:hover,
        nav[role="navigation"] a[rel="prev"]:focus,
        nav[role="navigation"] a[rel="prev"]:active,
        nav[role="navigation"] a[aria-label*="next" i]:hover,
        nav[role="navigation"] a[aria-label*="next" i]:focus,
        nav[role="navigation"] a[aria-label*="next" i]:active {
            background: #ffffff !important;
            background-color: #ffffff !important;
            border-color: #bfdbfe !important;
            color: #1d4ed8 !important;
            box-shadow: none !important;
        }

        nav[role="navigation"] span[aria-disabled="true"] > span {
            color: #94a3b8 !important;
            border-color: #e2e8f0 !important;
            opacity: 1 !important;
        }

        .table-slide-controls .table-slide-pager,
        .table-slide-controls .table-slide-pager *,
        .table-slide-controls .table-slide-pages,
        .table-slide-controls .table-slide-pages * {
            background: #ffffff !important;
            background-color: #ffffff !important;
            background-image: none !important;
        }

        nav[role="navigation"] .inline-flex,
        nav[role="navigation"] .inline-flex:hover,
        nav[role="navigation"] .inline-flex:focus,
        nav[role="navigation"] .inline-flex:active {
            background: #ffffff !important;
            background-color: #ffffff !important;
            background-image: none !important;
        }

        main table td form button[type="submit"][class*="text-red-"] {
            background: transparent !important;
            background-color: transparent !important;
            border-color: transparent !important;
            box-shadow: none !important;
        }

        main table td form button[type="submit"][class*="text-red-"]:hover,
        main table td form button[type="submit"][class*="text-red-"]:focus,
        main table td form button[type="submit"][class*="text-red-"]:active {
            background: transparent !important;
            background-color: transparent !important;
            border-color: transparent !important;
            box-shadow: none !important;
        }

        main table td a[class*="text-red-"] {
            background: transparent !important;
            background-color: transparent !important;
            border-color: transparent !important;
            box-shadow: none !important;
        }

        main table td a[class*="text-red-"]:hover,
        main table td a[class*="text-red-"]:focus,
        main table td a[class*="text-red-"]:active {
            background: transparent !important;
            background-color: transparent !important;
            border-color: transparent !important;
            box-shadow: none !important;
        }

        form button[class*="text-red-"]:not([class*="bg-"]),
        form button[class*="text-red-"]:not([class*="bg-"]):hover,
        form button[class*="text-red-"]:not([class*="bg-"]):focus,
        form button[class*="text-red-"]:not([class*="bg-"]):active {
            background: transparent !important;
            background-color: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            padding: 0 !important;
            appearance: none !important;
            -webkit-appearance: none !important;
        }
    </style>

    @stack('scripts')
</body>
</html>
