<!-- Topbar -->
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 shadow-sm">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Left: Mobile menu button -->
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Middle: Breadcrumb -->
            <div class="hidden lg:flex items-center flex-1 px-4">
                <nav class="flex items-center" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-3">
                        <li>
                            <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                            </a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-3 text-base font-semibold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
                                @yield('page-title', 'Dashboard')
                            </span>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Right: User menu & notifications -->
            <div class="flex items-center space-x-4">
                <!-- Search -->
                <div class="hidden md:block">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <input type="search" 
                               name="q"
                               value="{{ request('q') }}"
                               placeholder="Cari siswa, kelas, pembayaran..." 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </form>
                </div>

                <!-- Notifications -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="p-2 rounded-full text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="sr-only">View notifications</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if(isset($notificationCount) && $notificationCount > 0)
                        <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                        @endif
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                         style="display: none;">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900">Notifikasi Terbaru</h3>
                            @if(isset($notificationCount))
                            <p class="text-xs text-gray-500 mt-0.5">{{ $notificationCount }} aktivitas terbaru</p>
                            @endif
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @if(isset($recentNotifications) && $recentNotifications->count() > 0)
                                @foreach($recentNotifications as $notif)
                                <a href="{{ $notif['url'] }}" class="block px-4 py-3 hover:bg-gray-50 transition border-l-4 border-transparent hover:border-blue-500">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full {{ $notif['icon_bg'] }} flex items-center justify-center">
                                                <svg class="w-5 h-5 {{ $notif['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $notif['icon'] }}"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3 flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">{{ $notif['title'] }}</p>
                                            <p class="text-xs text-gray-600 mt-0.5 truncate">{{ $notif['description'] }}</p>
                                            <div class="flex items-center justify-between mt-1.5">
                                                <span class="text-xs font-semibold text-blue-600">{{ $notif['amount'] }}</span>
                                                <span class="text-xs text-gray-400">{{ $notif['time']->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            @else
                            <div class="px-4 py-12 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <p class="text-sm text-gray-500 font-medium">Belum ada notifikasi</p>
                                <p class="text-xs text-gray-400 mt-1">Notifikasi akan muncul ketika ada aktivitas baru</p>
                            </div>
                            @endif
                        </div>
                        <div class="p-3 border-t border-gray-200 text-center">
                            <a href="{{ route('riwayat.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                Lihat Semua Aktivitas
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="flex items-center space-x-3 px-3 py-2 rounded-xl hover:bg-slate-50 transition-all duration-200 focus:outline-none group">
                        <div class="hidden md:block text-right">
                            <p class="text-sm font-semibold text-slate-700 group-hover:text-blue-600 transition-colors">{{ Auth::user()->name ?? 'User' }}</p>
                            <p class="text-xs text-slate-500">{{ Auth::user()->email ?? '' }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center text-white font-bold text-base shadow-lg shadow-blue-500/30 group-hover:shadow-xl group-hover:shadow-blue-500/40 transition-all duration-200">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <!-- User Dropdown -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200 overflow-hidden">
                        <!-- Profile Header -->
                        <div class="px-5 py-4 bg-gradient-to-r from-blue-500 to-cyan-400">
                            <div class="flex items-center space-x-3">
                                <div class="w-14 h-14 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">{{ Auth::user()->name ?? 'User' }}</p>
                                    <p class="text-xs text-blue-100">{{ Auth::user()->email ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Menu Items -->
                        <div class="py-2">
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-5 py-3 text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center mr-3 transition-colors">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <span>My Profile</span>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-5 py-3 text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 group-hover:bg-blue-100 flex items-center justify-center mr-3 transition-colors">
                                    <svg class="w-5 h-5 text-slate-600 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <span>Settings</span>
                            </a>
                        </div>
                        
                        <!-- Logout -->
                        <div class="border-t border-slate-100 py-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-5 py-3 text-sm font-medium text-red-600 hover:bg-red-50 transition-all duration-200 group">
                                    <div class="w-9 h-9 rounded-lg bg-red-50 group-hover:bg-red-100 flex items-center justify-center mr-3 transition-colors">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                    </div>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
