<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Sistem Pembelajaran</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F3F4F6] min-h-screen text-[#1F2937] antialiased font-sans selection:bg-[#7B1038] selection:text-white">
    @if (request()->routeIs('superadmin.*'))
        @yield('content')
    @else
        @if (!request()->routeIs('home', 'admin.login', 'admin.register', 'siswa.login', 'siswa.register'))
        <header class="sticky top-0 z-50 bg-white shadow-[0_2px_8px_rgba(123,16,56,0.10)]">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 sm:h-[70px] flex items-center justify-between">
                <!-- Brand -->
                @if (auth('siswa')->check())
                    <a href="{{ route('portal.materi.index') }}" class="font-bold text-lg sm:text-xl text-[#7B1038] tracking-tight hover:opacity-90 transition">
                        Sistem Pembelajaran
                    </a>
                @elseif (auth('admin')->check())
                    <a href="{{ route('materi.index') }}" class="font-bold text-lg sm:text-xl text-[#7B1038] tracking-tight hover:opacity-90 transition">
                        Sistem Pembelajaran
                    </a>
                @else
                    <a href="{{ route('home') }}" class="font-bold text-lg sm:text-xl text-[#7B1038] tracking-tight hover:opacity-90 transition">
                        Sistem Pembelajaran
                    </a>
                @endif

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center gap-2 lg:gap-3 text-[13px] lg:text-[14px] font-medium text-[#5E0C2B]">
                    @if (auth('admin')->check())
                        @php $currentAdmin = auth('admin')->user(); @endphp
                        <a href="{{ route('materi.index') }}"
                           class="px-4 py-2 rounded-full transition-all duration-150 {{ request()->routeIs('materi.*') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                            Bahan Ajar
                        </a>
                        <a href="{{ route('admin.quiz.index') }}"
                           class="px-4 py-2 rounded-full transition-all duration-150 {{ request()->routeIs('admin.quiz.*') || request()->routeIs('admin.soal.*') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                            Kelola Quiz
                        </a>
                        <a href="{{ route('sertifikat.index') }}"
                           class="px-4 py-2 rounded-full transition-all duration-150 {{ request()->routeIs('sertifikat.*') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                            Sertifikat
                        </a>
                        <a href="{{ route('leaderboard.index') }}"
                           class="px-4 py-2 rounded-full transition-all duration-150 {{ request()->routeIs('leaderboard.*') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                            Leaderboard
                        </a>

                        @if ($currentAdmin->role === 'superadmin')
                            <a href="{{ route('superadmin.index') }}"
                               class="px-4 py-2 rounded-full transition-all duration-150 {{ request()->routeIs('superadmin.*') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                                Superadmin
                            </a>
                        @endif

                        <span class="w-px h-5 bg-[#E5CDD5] inline-block mx-1" aria-hidden="true"></span>
                        <a href="{{ route('admin.profil') }}"
                           class="px-3.5 py-1.5 rounded-full transition-all duration-150 {{ request()->routeIs('admin.profil*') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                            {{ $currentAdmin->nama_lengkap }}
                        </a>
                        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-[#7B1038] hover:bg-[#7B1038]/10 hover:text-[#5E0C2B] px-3.5 py-1.5 rounded-full text-sm font-semibold transition-colors duration-150">
                                Logout
                            </button>
                        </form>

                    @elseif (auth('siswa')->check())
                        <a href="{{ route('portal.materi.index') }}"
                           class="px-4 py-2 rounded-full transition-all duration-150 {{ request()->routeIs('portal.*') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                            Portal Siswa
                        </a>
                        <a href="{{ route('leaderboard.index') }}"
                           class="px-4 py-2 rounded-full transition-all duration-150 {{ request()->routeIs('leaderboard.*') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                            Leaderboard
                        </a>
                        <a href="{{ route('siswa.sertifikat.index') }}"
                           class="px-4 py-2 rounded-full transition-all duration-150 {{ request()->routeIs('siswa.sertifikat.*') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                            Sertifikat
                        </a>
                        <span class="w-px h-5 bg-[#E5CDD5] inline-block mx-1" aria-hidden="true"></span>
                        <a href="{{ route('siswa.profil') }}"
                           class="px-3.5 py-1.5 rounded-full transition-all duration-150 {{ request()->routeIs('siswa.profil') ? 'bg-[#7B1038] text-white shadow-xs font-semibold' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                            {{ auth('siswa')->user()->nama_lengkap }}
                        </a>
                        <form action="{{ route('siswa.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-[#7B1038] hover:bg-[#7B1038]/10 hover:text-[#5E0C2B] px-3.5 py-1.5 rounded-full text-sm font-semibold transition-colors duration-150">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('admin.login') }}" class="px-3 py-1.5 rounded-full transition-colors duration-150 {{ request()->routeIs('admin.login') ? 'bg-[#7B1038] text-white font-semibold shadow-xs' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">Login Guru</a>
                        <a href="{{ route('admin.register') }}" class="px-3 py-1.5 rounded-full transition-colors duration-150 {{ request()->routeIs('admin.register') ? 'bg-[#7B1038] text-white font-semibold shadow-xs' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">Registrasi Guru</a>
                        <span class="w-px h-5 bg-[#E5CDD5] inline-block mx-1" aria-hidden="true"></span>
                        <a href="{{ route('siswa.login') }}" class="px-3 py-1.5 rounded-full transition-colors duration-150 {{ request()->routeIs('siswa.login') ? 'bg-[#7B1038] text-white font-semibold shadow-xs' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">Login Siswa</a>
                        <a href="{{ route('siswa.register') }}" class="px-3 py-1.5 rounded-full transition-colors duration-150 {{ request()->routeIs('siswa.register') ? 'bg-[#7B1038] text-white font-semibold shadow-xs' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">Registrasi Siswa</a>
                    @endif
                </nav>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button id="mobile-menu-toggle"
                            type="button"
                            aria-label="Buka menu navigasi"
                            aria-expanded="false"
                            aria-controls="mobile-menu"
                            class="inline-flex items-center justify-center p-2 rounded-lg text-[#7B1038] hover:bg-[#7B1038]/10 focus:outline-none focus:ring-2 focus:ring-[#7B1038] transition">
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
                @if (auth('admin')->check())
                    @php $currentAdmin = auth('admin')->user(); @endphp
                    <a href="{{ route('materi.index') }}"
                       class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('materi.*') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">
                        Bahan Ajar
                    </a>
                    <a href="{{ route('admin.quiz.index') }}"
                       class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('admin.quiz.*') || request()->routeIs('admin.soal.*') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">
                        Kelola Quiz
                    </a>
                    <a href="{{ route('sertifikat.index') }}"
                       class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('sertifikat.*') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">
                        Sertifikat
                    </a>
                    <a href="{{ route('leaderboard.index') }}"
                       class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('leaderboard.*') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">
                        Leaderboard
                    </a>
                    @if ($currentAdmin->role === 'superadmin')
                        <a href="{{ route('superadmin.index') }}"
                           class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('superadmin.*') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">
                            Superadmin
                        </a>
                    @endif
                    <div class="h-px bg-[#E5CDD5] my-2"></div>
                    <a href="{{ route('admin.profil') }}"
                       class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('admin.profil*') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">
                        {{ $currentAdmin->nama_lengkap }}
                    </a>
                    <form action="{{ route('admin.logout') }}" method="POST" class="pt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-gray-100 text-[#7B1038] hover:bg-[#7B1038] hover:text-white rounded-xl text-sm font-semibold transition">
                            Logout
                        </button>
                    </form>
                @elseif (auth('siswa')->check())
                    <a href="{{ route('portal.materi.index') }}"
                       class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.*') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">
                        Portal Siswa
                    </a>
                    <a href="{{ route('leaderboard.index') }}"
                       class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('leaderboard.*') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">
                        Leaderboard
                    </a>
                    <a href="{{ route('siswa.sertifikat.index') }}"
                       class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('siswa.sertifikat.*') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">
                        Sertifikat
                    </a>
                    <div class="h-px bg-[#E5CDD5] my-2"></div>
                    <a href="{{ route('siswa.profil') }}"
                       class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('siswa.profil') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">
                        {{ auth('siswa')->user()->nama_lengkap }}
                    </a>
                    <form action="{{ route('siswa.logout') }}" method="POST" class="pt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-gray-100 text-[#7B1038] hover:bg-[#7B1038] hover:text-white rounded-xl text-sm font-semibold transition">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('admin.login') }}" class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('admin.login') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">Login Guru</a>
                    <a href="{{ route('admin.register') }}" class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('admin.register') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">Registrasi Guru</a>
                    <div class="h-px bg-[#E5CDD5] my-2"></div>
                    <a href="{{ route('siswa.login') }}" class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('siswa.login') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">Login Siswa</a>
                    <a href="{{ route('siswa.register') }}" class="mobile-menu-item flex items-center min-h-[44px] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('siswa.register') ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }}">Registrasi Siswa</a>
                @endif
            </div>
        </header>
        @endif

        @if (request()->routeIs('home', 'admin.login', 'admin.register', 'siswa.login', 'siswa.register'))
            @yield('content')
        @else
            <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
                @if (session('success'))
                    <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm flex items-center gap-2">
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
                        <p class="font-semibold mb-1">Terjadi kesalahan pada input:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.04)] border border-[#E5CDD5]/60 p-6 sm:p-8">
                    @yield('content')
                </div>
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
    @stack('scripts')
</body>
</html>