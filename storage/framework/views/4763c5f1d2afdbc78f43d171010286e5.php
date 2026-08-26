<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('favicon.svg')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('apple-touch-icon.svg')); ?>">

    <title><?php echo $__env->yieldContent('title', config('app.name', 'Laravel')); ?></title>

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-slate-50 to-blue-50">
    <?php
        $isLoginPage = request()->routeIs('login');
        $isRegisterPage = request()->routeIs('register');
        $isForgotPasswordPage = request()->routeIs('password.request') || request()->routeIs('password.reset');
        $useResponsiveImagePanel = $isLoginPage || $isRegisterPage || $isForgotPasswordPage;
        $leftBackgroundImage = $isLoginPage
            ? asset('images/educlass-auth-login.svg')
            : asset('images/educlass-auth.svg');
        $leftBackgroundStyle = $isRegisterPage
            ? 'background-size: 100% auto; background-position: center top;'
            : 'background-size: cover; background-position: center;';
        $leftPanelInlineStyle = $useResponsiveImagePanel
            ? 'background-color: #0f172a;'
            : "background-image: url('{$leftBackgroundImage}'); {$leftBackgroundStyle}";
        $leftPanelClass = $useResponsiveImagePanel
            ? 'p-0 bg-slate-900 flex items-center justify-center'
            : 'p-12 flex flex-col justify-between bg-no-repeat';
        $leftPanelHeightClass = $useResponsiveImagePanel
            ? 'min-h-[220px] sm:min-h-[280px] md:min-h-[340px] lg:min-h-full'
            : '';
    ?>

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-6xl bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <!-- Left Side - Illustration -->
                 <div class="lg:w-1/2 relative overflow-hidden <?php echo e($leftPanelClass); ?> <?php echo e($leftPanelHeightClass); ?>"
                     style="<?php echo e($leftPanelInlineStyle); ?>">

                    <?php if($useResponsiveImagePanel): ?>
                    <img src="<?php echo e($leftBackgroundImage); ?>"
                        alt="Background Auth <?php echo e(config('finance.school.name', config('app.name'))); ?>"
                         class="w-full h-full object-contain object-center max-h-[72vh] lg:max-h-[88vh]">
                    <?php endif; ?>
                    
                    <?php if (! ($useResponsiveImagePanel)): ?>
                    <!-- Logo/Brand -->
                    <div class="relative z-10">
                        <div class="mb-8">
                               <img src="<?php echo e(asset(config('finance.school.logo', 'images/educlass-logo.svg'))); ?>"
                                 alt="<?php echo e(config('finance.school.name', config('app.name'))); ?>"
                                 class="h-14 w-auto object-contain">
                        </div>
                        
                        <h2 class="text-4xl font-bold text-white leading-tight mb-4"><?php echo $__env->yieldContent('welcome-title', 'Selamat Datang'); ?></h2>
                        <p class="text-blue-100 text-lg leading-relaxed"><?php echo $__env->yieldContent('welcome-description', 'Sistem Manajemen Keuangan Sekolah yang Modern dan Efisien'); ?></p>
                    </div>
                    
                    <!-- Illustration/Image Placeholder -->
                    <div class="relative z-10 hidden lg:block">
                        <div class="flex items-center justify-center space-x-4">
                            <!-- School Building Icon -->
                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 transform hover:scale-105 transition-transform duration-300">
                                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            
                            <!-- Students Icon -->
                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 transform hover:scale-105 transition-transform duration-300">
                                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            
                            <!-- Chart Icon -->
                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 transform hover:scale-105 transition-transform duration-300">
                                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Features List -->
                    <div class="relative z-10 mt-8 space-y-3">
                        <div class="flex items-center space-x-3 text-white">
                            <svg class="w-5 h-5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium">Manajemen Pembayaran SPP</span>
                        </div>
                        <div class="flex items-center space-x-3 text-white">
                            <svg class="w-5 h-5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium">Laporan Keuangan Real-time</span>
                        </div>
                        <div class="flex items-center space-x-3 text-white">
                            <svg class="w-5 h-5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium">Pencatatan Aset Sekolah</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Right Side - Form -->
                <div class="lg:w-1/2 p-12 flex items-center">
                    <div class="w-full max-w-md mx-auto">
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                </div>
            </div>
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
        
        // Show loading when page starts to unload (navigation/reload)
        window.addEventListener('beforeunload', function() {
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
            const isExportLikePath = path.endsWith('.pdf') || path.endsWith('.csv') || path.endsWith('.xlsx') || path.endsWith('.xls') || path.includes('/pdf');
            const isExportLikeQuery = url.searchParams.has('export') || url.searchParams.has('download');

            return isExportLikePath || isExportLikeQuery;
        }
        
        // Show loading when clicking links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link || shouldSkipLoaderForLink(link)) {
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

        document.addEventListener('DOMContentLoaded', function() {
            hideLoading();
        });
        
        // Hide loading when page is fully loaded
        window.addEventListener('load', function() {
            hideLoading();
        });

        window.addEventListener('pageshow', function() {
            hideLoading();
        });
        
        // Hide loading if it's still showing after 4 seconds (safety timeout)
        setTimeout(function() {
            hideLoading();
        }, 4000);
    </script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\sriwijaya_kidss\resources\views/layouts/guest.blade.php ENDPATH**/ ?>