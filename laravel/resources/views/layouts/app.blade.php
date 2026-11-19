<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Sistem Pembelajaran</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="preload" as="image" href="{{ asset('smkn2-jember.png') }}">
    <style>
        :root {
            --text-gray-900: #1F2937;
            --text-gray-700: #374151;
            --text-gray-600: #4B5563;
            --text-gray-500: #6B7280;
        }
        .text-gradient-gray {
            background: linear-gradient(90deg, #1F2937, #6B7280);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: #1F2937;
        }
        input[type="file"]::file-selector-button,
        input[type="file"]::-webkit-file-upload-button {
            background-color: #7B1038 !important;
            color: #FFFFFF !important;
            border: none !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.625rem !important;
            font-weight: 700 !important;
            font-size: 0.75rem !important;
            cursor: pointer !important;
            margin-right: 0.875rem !important;
            transition: background-color 0.15s ease !important;
        }
        input[type="file"]::file-selector-button:hover,
        input[type="file"]::-webkit-file-upload-button:hover {
            background-color: #5E0C2B !important;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#F3F4F6] min-h-screen text-[#1F2937] antialiased font-sans tracking-normal selection:bg-[#7B1038] selection:text-white {{ request()->routeIs('portal.quiz.kerjakan') ? 'h-screen overflow-hidden' : (request()->routeIs('portal.quiz.submit') ? 'h-screen overflow-hidden flex flex-col' : '') }}">
    @if (request()->routeIs('superadmin.*'))
        @yield('content')
    @else
        {{-- Navbar atas: tidak dipakai untuk guru (guru pakai sidebar) atau saat siswa sedang mengerjakan kuis (fokus ujian) --}}
        @if (!request()->routeIs('home', 'admin.login', 'admin.register', 'siswa.login', 'siswa.register', 'portal.quiz.kerjakan') && !auth('admin')->check())
        <header class="sticky top-0 z-50 bg-white shadow-[0_2px_8px_rgba(123,16,56,0.10)]">
            <div class="max-w-screen-2xl mx-auto px-10 h-16 sm:h-[70px] flex items-center justify-between">
                <!-- Brand / Data Diri Siswa -->
                @if (auth('siswa')->check())
                    @php
                        $navSiswa = auth('siswa')->user();
                        $navKelas = $navSiswa->nama_kelas ?? $navSiswa->kelas;
                    @endphp
                    <a href="{{ route('portal.materi.index') }}" class="flex items-baseline gap-1.5 sm:gap-2.5 hover:opacity-90 transition min-w-0 py-1" title="Portal Siswa">
                        <span class="font-bold text-sm sm:text-base md:text-lg text-[var(--text-gray-900)] text-gradient-gray tracking-normal truncate">
                            {{ $navSiswa->nama_lengkap }}
                        </span>
                        <span class="text-sm text-gray-400 font-normal select-none">|</span>
                        <span class="text-sm font-medium text-gray-600 whitespace-nowrap">
                            Kelas {{ str_replace(' ', '-', (string)$navKelas) }}
                        </span>
                        <span class="text-sm text-gray-400 font-normal select-none hidden sm:inline">|</span>
                        <span class="text-sm font-medium text-gray-600 truncate max-w-[140px] sm:max-w-[200px] md:max-w-xs hidden sm:inline">
                            {{ $navSiswa->jurusan }}
                        </span>
                        <span class="text-sm text-gray-400 font-normal select-none hidden md:inline">|</span>
                        <span class="text-sm font-medium text-gray-600 whitespace-nowrap hidden md:inline">
                            {{ $navSiswa->nisn }}
                        </span>
                    </a>
                @else
                    <a href="{{ route('home') }}" class="font-bold text-lg sm:text-xl text-[#7B1038] tracking-normal hover:opacity-90 transition">
                        Sistem Pembelajaran
                    </a>
                @endif

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center gap-2 lg:gap-3 text-sm font-medium {{ auth('siswa')->check() ? 'text-[var(--text-gray-700)]' : 'text-[#5E0C2B]' }}">
                    @if (auth('siswa')->check())
                        <a href="{{ route('portal.materi.index') }}"
                           class="px-4 py-2 rounded-full transition-all duration-150 {{ request()->routeIs('portal.*') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[var(--text-gray-700)] hover:text-[var(--text-gray-900)] hover:bg-slate-100' }}">
                            Portal Siswa
                        </a>
                        <a href="{{ route('leaderboard.index') }}"
                           class="px-4 py-2 rounded-full transition-all duration-150 {{ request()->routeIs('leaderboard.*') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[var(--text-gray-700)] hover:text-[var(--text-gray-900)] hover:bg-slate-100' }}">
                            Leaderboard
                        </a>
                        <a href="{{ route('siswa.sertifikat.index') }}"
                           class="px-4 py-2 rounded-full transition-all duration-150 {{ request()->routeIs('siswa.sertifikat.*') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[var(--text-gray-700)] hover:text-[var(--text-gray-900)] hover:bg-slate-100' }}">
                            Sertifikat
                        </a>
                        <span class="w-px h-5 bg-[#E5CDD5] inline-block mx-1" aria-hidden="true"></span>
                        <a href="{{ route('siswa.profil') }}"
                           class="px-3.5 py-1.5 rounded-full transition-all duration-150 {{ request()->routeIs('siswa.profil') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[var(--text-gray-700)] hover:text-[var(--text-gray-900)] hover:bg-slate-100' }}">
                            {{ auth('siswa')->user()->nama_lengkap }}
                        </a>
                        <form action="{{ route('siswa.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-[var(--text-gray-700)] hover:bg-slate-100 hover:text-[var(--text-gray-900)] px-3.5 py-1.5 rounded-full text-sm font-semibold transition-colors duration-150">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('admin.login') }}" class="px-3 py-1.5 rounded-full transition-colors duration-150 {{ request()->routeIs('admin.login') ? 'bg-[#7B1038] text-white font-semibold shadow-xs' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-slate-100' }}">Login Guru</a>
                        <a href="{{ route('admin.register') }}" class="px-3 py-1.5 rounded-full transition-colors duration-150 {{ request()->routeIs('admin.register') ? 'bg-[#7B1038] text-white font-semibold shadow-xs' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-slate-100' }}">Registrasi Guru</a>
                        <span class="w-px h-5 bg-[#E5CDD5] inline-block mx-1" aria-hidden="true"></span>
                        <a href="{{ route('siswa.login') }}" class="px-3 py-1.5 rounded-full transition-colors duration-150 {{ request()->routeIs('siswa.login') ? 'bg-[#7B1038] text-white font-semibold shadow-xs' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-slate-100' }}">Login Siswa</a>
                        <a href="{{ route('siswa.register') }}" class="px-3 py-1.5 rounded-full transition-colors duration-150 {{ request()->routeIs('siswa.register') ? 'bg-[#7B1038] text-white font-semibold shadow-xs' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-slate-100' }}">Registrasi Siswa</a>
                    @endif
                </nav>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button id="mobile-menu-toggle"
                            type="button"
                            aria-label="Buka menu navigasi"
                            aria-expanded="false"
                            aria-controls="mobile-menu"
                            class="inline-flex items-center justify-center p-2 rounded-lg {{ auth('siswa')->check() ? 'text-[var(--text-gray-700)] hover:text-[var(--text-gray-900)]' : 'text-[#7B1038]' }} hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-[#7B1038] transition">
                        <svg id="hamburger-icon" class="w-6 h-6 block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg id="close-icon" class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Dropdown -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-[#E5CDD5] shadow-lg px-4 py-3 space-y-1.5">
                @if (auth('siswa')->check())
                    <a href="{{ route('portal.materi.index') }}"
                       class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.*') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[var(--text-gray-700)] hover:bg-slate-100 hover:text-[var(--text-gray-900)]' }}">
                        Portal Siswa
                    </a>
                    <a href="{{ route('leaderboard.index') }}"
                       class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('leaderboard.*') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[var(--text-gray-700)] hover:bg-slate-100 hover:text-[var(--text-gray-900)]' }}">
                        Leaderboard
                    </a>
                    <a href="{{ route('siswa.sertifikat.index') }}"
                       class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('siswa.sertifikat.*') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[var(--text-gray-700)] hover:bg-slate-100 hover:text-[var(--text-gray-900)]' }}">
                        Sertifikat
                    </a>
                    <div class="h-px bg-[#E5CDD5] my-2"></div>
                    <a href="{{ route('siswa.profil') }}"
                       class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('siswa.profil') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[var(--text-gray-700)] hover:bg-slate-100 hover:text-[var(--text-gray-900)]' }}">
                        {{ auth('siswa')->user()->nama_lengkap }}
                    </a>
                    <form action="{{ route('siswa.logout') }}" method="POST" class="pt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-gray-100 text-[var(--text-gray-700)] hover:bg-[#7B1038] hover:text-white rounded-xl text-sm font-semibold transition">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('admin.login') }}" class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('admin.login') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-slate-100 hover:text-[#7B1038]' }}">Login Guru</a>
                    <a href="{{ route('admin.register') }}" class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('admin.register') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-slate-100 hover:text-[#7B1038]' }}">Registrasi Guru</a>
                    <div class="h-px bg-[#E5CDD5] my-2"></div>
                    <a href="{{ route('siswa.login') }}" class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('siswa.login') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-slate-100 hover:text-[#7B1038]' }}">Login Siswa</a>
                    <a href="{{ route('siswa.register') }}" class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('siswa.register') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-slate-100 hover:text-[#7B1038]' }}">Registrasi Siswa</a>
                @endif
            </div>
        </header>
        @endif

        {{-- Konten --}}
        @if (request()->routeIs('home', 'admin.login', 'admin.register', 'siswa.login', 'siswa.register'))
            @yield('content')
        @else
            @if (auth('admin')->check())
                @include('layouts.partials.sidebar-admin')
            @endif

            <main class="w-full {{ request()->routeIs('portal.quiz.kerjakan') ? 'h-screen max-h-screen overflow-hidden flex flex-col py-3 sm:py-4 px-4 sm:px-6' : (request()->routeIs('portal.quiz.submit') ? 'flex-1 min-h-0 overflow-hidden flex flex-col justify-center items-center py-2 sm:py-3 px-4 sm:px-6' : 'py-6') }} {{ auth('admin')->check() ? 'lg:ml-64 lg:w-[calc(100%-16rem)] px-4 sm:px-6 lg:px-8' : '' }}">
                @if (request()->routeIs('portal.quiz.kerjakan', 'portal.quiz.submit'))
                    <div class="max-w-4xl mx-auto w-full {{ request()->routeIs('portal.quiz.kerjakan') ? 'flex-1 flex flex-col min-h-0' : 'flex-1 min-h-0 flex flex-col justify-center items-center' }}">
                        @yield('content')
                    </div>
                @elseif (auth('siswa')->check() || request()->routeIs('portal.*', 'siswa.*', 'leaderboard.*', 'admin.leaderboard.*', '*leaderboard*', 'admin.profil*') || request()->is('leaderboard*', 'admin/leaderboard*'))
                    <div class="{{ auth('admin')->check() ? 'w-full' : 'max-w-screen-2xl mx-auto px-10 w-full' }}">
                        @yield('content')
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.04)] border border-[#E5CDD5]/60 p-6 sm:p-8">
                        @yield('content')
                    </div>
                @endif
            </main>
        @endif
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburgerIcon = document.getElementById('hamburger-icon');
            const closeIcon = document.getElementById('close-icon');

            if (!toggleBtn || !mobileMenu) return;

            function openMenu() {
                mobileMenu.classList.remove('hidden');
                toggleBtn.setAttribute('aria-expanded', 'true');
                toggleBtn.setAttribute('aria-label', 'Tutup menu navigasi');
                hamburgerIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
            }

            function closeMenu() {
                mobileMenu.classList.add('hidden');
                toggleBtn.setAttribute('aria-expanded', 'false');
                toggleBtn.setAttribute('aria-label', 'Buka menu navigasi');
                hamburgerIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }

            toggleBtn.addEventListener('click', function () {
                const isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';
                if (isExpanded) {
                    closeMenu();
                } else {
                    openMenu();
                }
            });

            // Close when link or button inside mobile menu is clicked
            mobileMenu.querySelectorAll('.mobile-menu-item, form button').forEach(function (el) {
                el.addEventListener('click', function () {
                    closeMenu();
                });
            });

            // Close when Escape key is pressed
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && toggleBtn.getAttribute('aria-expanded') === 'true') {
                    closeMenu();
                    toggleBtn.focus();
                }
            });

            // Close on resize to desktop (>= 768px)
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768 && toggleBtn.getAttribute('aria-expanded') === 'true') {
                    closeMenu();
                }
            });
        });
    </script>

    {{-- Elegant Floating Toast Popup (Tidak Menggeser Konten, Auto Hilang 3.5 Detik) --}}
    @php
        $toastSuccess = session('success');
        $toastError   = session('error');
        $toastErrors  = $errors->any() ? $errors->all() : null;
    @endphp

    @if ($toastSuccess || $toastError || $toastErrors)
    <div id="toast-container" class="fixed top-5 right-5 sm:top-6 sm:right-6 z-[9999] flex flex-col gap-3 max-w-sm sm:max-w-md w-[calc(100%-2.5rem)] pointer-events-none select-none">
        @if ($toastSuccess)
        <div class="toast-popup pointer-events-auto relative overflow-hidden bg-white/95 backdrop-blur-md border border-emerald-200/80 shadow-[0_10px_25px_-5px_rgba(16,185,129,0.18),0_8px_10px_-6px_rgba(0,0,0,0.06)] rounded-2xl p-4 flex items-start gap-3.5 transition-all duration-300 transform translate-y-0 opacity-100" role="alert">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#1F9254] flex items-center justify-center shrink-0 shadow-2xs">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
            <div class="flex-1 min-w-0 pt-0.5">
                <p class="text-sm font-bold text-[#1F2937]">Berhasil</p>
                <p class="text-sm text-gray-600 mt-0.5 leading-relaxed font-medium">{{ $toastSuccess }}</p>
            </div>
            <button type="button" onclick="dismissToast(this.closest('.toast-popup'))" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition shrink-0 cursor-pointer" aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="toast-progress absolute bottom-0 left-0 h-0.5 bg-emerald-500 rounded-b-2xl"></div>
        </div>
        @endif

        @if ($toastError)
        <div class="toast-popup pointer-events-auto relative overflow-hidden bg-white/95 backdrop-blur-md border border-rose-200/80 shadow-[0_10px_25px_-5px_rgba(244,63,94,0.18),0_8px_10px_-6px_rgba(0,0,0,0.06)] rounded-2xl p-4 flex items-start gap-3.5 transition-all duration-300 transform translate-y-0 opacity-100" role="alert">
            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 shadow-2xs">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0 pt-0.5">
                <p class="text-sm font-bold text-[#1F2937]">Pemberitahuan</p>
                <p class="text-sm text-gray-600 mt-0.5 leading-relaxed font-medium">{{ $toastError }}</p>
            </div>
            <button type="button" onclick="dismissToast(this.closest('.toast-popup'))" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition shrink-0 cursor-pointer" aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="toast-progress absolute bottom-0 left-0 h-0.5 bg-rose-500 rounded-b-2xl"></div>
        </div>
        @endif

        @if ($toastErrors)
        <div class="toast-popup pointer-events-auto relative overflow-hidden bg-white/95 backdrop-blur-md border border-rose-200/80 shadow-[0_10px_25px_-5px_rgba(244,63,94,0.18),0_8px_10px_-6px_rgba(0,0,0,0.06)] rounded-2xl p-4 flex items-start gap-3.5 transition-all duration-300 transform translate-y-0 opacity-100" role="alert">
            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 shadow-2xs">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0 pt-0.5">
                <p class="text-sm font-bold text-[#1F2937]">Terjadi Kesalahan</p>
                <div class="text-sm text-gray-600 mt-0.5 space-y-1">
                    @foreach ($toastErrors as $err)
                        <p class="leading-relaxed font-medium">• {{ $err }}</p>
                    @endforeach
                </div>
            </div>
            <button type="button" onclick="dismissToast(this.closest('.toast-popup'))" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition shrink-0 cursor-pointer" aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="toast-progress absolute bottom-0 left-0 h-0.5 bg-rose-500 rounded-b-2xl"></div>
        </div>
        @endif
    </div>

    <style>
        @keyframes toastProgressAnim {
            from { width: 100%; }
            to { width: 0%; }
        }
        .toast-progress {
            animation: toastProgressAnim 3.5s linear forwards;
        }
    </style>

    <script>
        function dismissToast(el) {
            if (!el || el.dataset.dismissed) return;
            el.dataset.dismissed = "true";
            el.style.transition = "opacity 0.35s ease, transform 0.35s ease, max-height 0.35s ease, margin 0.35s ease";
            el.style.opacity = "0";
            el.style.transform = "translateX(24px) scale(0.96)";
            setTimeout(() => {
                el.remove();
                const container = document.getElementById('toast-container');
                if (container && container.children.length === 0) {
                    container.remove();
                }
            }, 350);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const toasts = document.querySelectorAll('.toast-popup');
            toasts.forEach(toast => {
                setTimeout(() => {
                    dismissToast(toast);
                }, 3500);
            });
        });
    </script>
    @endif
    @stack('scripts')
</body>
</html>