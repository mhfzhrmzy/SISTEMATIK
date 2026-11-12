{{-- resources/views/layouts/partials/sidebar-admin.blade.php --}}
@php
    $currentAdmin = auth('admin')->user();

    $menus = [
        ['Bahan Ajar',  'materi.index',      request()->routeIs('materi.*')],
        ['Kelola Quiz', 'admin.quiz.index',  request()->routeIs('admin.quiz.*') || request()->routeIs('admin.soal.*')],
        ['Sertifikat',  'sertifikat.index',  request()->routeIs('sertifikat.*')],
        ['Leaderboard', 'leaderboard.index', request()->routeIs('leaderboard.*')],
    ];

    if ($currentAdmin->role === 'superadmin') {
        $menus[] = ['Superadmin', 'superadmin.index', request()->routeIs('superadmin.*')];
    }

    $profilAktif = request()->routeIs('admin.profil*');
@endphp

<style>
    #sidebar-admin {
        position: fixed; top: 0; bottom: 0; left: 0; z-index: 40;
        width: 16rem; display: flex; flex-direction: column;
        background: #fff; box-shadow: 2px 0 12px rgba(123, 16, 56, 0.08);
        transform: translateX(-100%); transition: transform 0.2s ease;
    }
    #sidebar-admin.is-open { transform: translateX(0); }

    #sidebar-overlay {
        display: none; position: fixed; inset: 0; z-index: 30;
        background: rgba(0, 0, 0, 0.4);
    }
    #sidebar-overlay.is-open { display: block; }

    .sb-mobile-bar {
        display: flex; position: sticky; top: 0; z-index: 30; height: 3.5rem;
        align-items: center; justify-content: space-between; padding: 0 1rem;
        background: #fff; box-shadow: 0 2px 8px rgba(123, 16, 56, 0.10);
    }

    @media (min-width: 1024px) {
        #sidebar-admin { transform: none; }
        #sidebar-overlay { display: none !important; }
        .sb-mobile-bar, .sb-close { display: none !important; }
    }

    .guru-search-box {
        width: 100%;
        height: 2.25rem;
    }
    .guru-search-box:focus-within {
        border-color: #7B1038;
        box-shadow: 0 0 0 3px rgba(123, 16, 56, 0.14);
    }
    @media (min-width: 640px) {
        .guru-search-box {
            width: 17.5rem;
            flex-shrink: 0;
        }
    }
</style>

<div class="sb-mobile-bar">
    <a href="{{ route('materi.index') }}" class="font-bold text-lg text-[#7B1038] tracking-tight">Sistem Pembelajaran</a>
    <button id="sidebar-open" type="button" aria-label="Buka menu navigasi" aria-controls="sidebar-admin" aria-expanded="false"
            class="p-2 rounded-lg text-[#7B1038] hover:bg-[#7B1038]/10 focus:outline-none focus:ring-2 focus:ring-[#7B1038]">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
</div>

<div id="sidebar-overlay"></div>

<aside id="sidebar-admin">
    <div class="h-16 sm:h-[70px] shrink-0 flex items-center justify-between px-5 border-b border-[#E5CDD5]/70">
        <a href="{{ route('materi.index') }}" class="font-bold text-lg text-[#7B1038] tracking-tight hover:opacity-90 transition">
            Sistem Pembelajaran
        </a>
        <button id="sidebar-close" type="button" aria-label="Tutup menu navigasi"
                class="sb-close p-1.5 rounded-lg text-[#7B1038] hover:bg-[#7B1038]/10">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1.5 text-sm font-medium">
        @foreach ($menus as [$label, $routeName, $aktif])
            <a href="{{ route($routeName) }}"
               class="flex items-center min-h-[44px] px-4 py-2.5 rounded-xl transition-all duration-150 {{ $aktif ? 'bg-[#7B1038] text-white font-semibold shadow-xs' : 'text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <div class="shrink-0 border-t border-[#E5CDD5]/70 p-3 space-y-1.5">
        <a href="{{ route('admin.profil') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 {{ $profilAktif ? 'bg-[#7B1038] text-white' : 'text-[#5E0C2B] hover:bg-[#7B1038]/10' }}">
            @if ($currentAdmin->foto_profile)
                <img src="{{ asset('storage/'.$currentAdmin->foto_profile) }}" alt="" class="w-9 h-9 rounded-full object-cover shrink-0">
            @else
                <span class="w-9 h-9 rounded-full shrink-0 flex items-center justify-center text-xs font-bold {{ $profilAktif ? 'bg-white/20 text-white' : 'bg-[#7B1038]/10 text-[#7B1038]' }}">
                    {{ strtoupper(substr($currentAdmin->nama_lengkap, 0, 3)) }}
                </span>
            @endif
            <span class="min-w-0">
                <span class="block text-sm font-semibold truncate">{{ $currentAdmin->nama_lengkap }}</span>
                <span class="block text-[11px] {{ $profilAktif ? 'text-white/80' : 'text-gray-500' }}">Lihat profil</span>
            </span>
        </a>

        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-gray-100 text-[#7B1038] hover:bg-[#7B1038] hover:text-white rounded-xl text-sm font-semibold transition">
                Logout
            </button>
        </form>
    </div>
</aside>

<script>
(function () {
    const sidebar = document.getElementById('sidebar-admin');
    const overlay = document.getElementById('sidebar-overlay');
    const openBtn = document.getElementById('sidebar-open');
    const closeBtn = document.getElementById('sidebar-close');
    if (!sidebar || !openBtn) return;

    function openSidebar() {
        sidebar.classList.add('is-open');
        overlay.classList.add('is-open');
        openBtn.setAttribute('aria-expanded', 'true');
    }
    function closeSidebar() {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-open');
        openBtn.setAttribute('aria-expanded', 'false');
    }

    openBtn.addEventListener('click', openSidebar);
    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && openBtn.getAttribute('aria-expanded') === 'true') {
            closeSidebar();
            openBtn.focus();
        }
    });
})();
</script>