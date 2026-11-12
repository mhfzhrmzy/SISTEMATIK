<!-- LEFT SIDEBAR -->
<aside class="w-64 lg:w-72 bg-white border-r border-slate-200/80 flex flex-col justify-between shrink-0 sticky top-0 h-screen overflow-y-auto z-30 shadow-sm">
    <div>
        <!-- BRAND HEADER -->
        <div class="p-5 border-b border-slate-100 flex items-center gap-3">
            <div>
                <h1 class="font-extrabold text-slate-900 tracking-tight text-sm leading-snug">SMKN 2 JEMBER</h1>
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">PORTAL SUPERADMIN</p>
            </div>
        </div>

        <!-- NAVIGATION MENU -->
        <nav class="p-4 space-y-1.5" aria-label="Navigasi Utama Superadmin">
            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">NAVIGASI UTAMA</p>

            <!-- Dashboard -->
            <a href="{{ route('superadmin.index', ['menu' => 'dashboard']) }}" 
               class="px-4 py-3 rounded-2xl flex items-center gap-3 text-sm font-semibold transition {{ ($menu ?? '') === 'dashboard' && !request()->routeIs('superadmin.materi.*') && !request()->routeIs('superadmin.quiz.*') && !request()->routeIs('superadmin.soal.*') ? 'bg-[#6D1837] text-white shadow-md shadow-amber-950/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Daftar Guru -->
            <a href="{{ route('superadmin.index', ['menu' => 'guru']) }}" 
               class="px-4 py-3 rounded-2xl flex items-center gap-3 text-sm font-semibold transition {{ ($menu ?? '') === 'guru' ? 'bg-[#6D1837] text-white shadow-md shadow-amber-950/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                </svg>
                <span>Daftar Guru</span>
            </a>

            <!-- Daftar Siswa -->
            <a href="{{ route('superadmin.index', ['menu' => 'siswa']) }}" 
               class="px-4 py-3 rounded-2xl flex items-center gap-3 text-sm font-semibold transition {{ ($menu ?? '') === 'siswa' ? 'bg-[#6D1837] text-white shadow-md shadow-amber-950/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Daftar Siswa</span>
            </a>

            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-4 mb-2">PEMBELAJARAN</p>

            <!-- Bahan Ajar -->
            <a href="{{ route('superadmin.materi.index') }}" 
               class="px-4 py-3 rounded-2xl flex items-center gap-3 text-sm font-semibold transition {{ request()->routeIs('superadmin.materi.*') || ($menu ?? '') === 'materi' ? 'bg-[#6D1837] text-white shadow-md shadow-amber-950/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span>Bahan Ajar</span>
            </a>

            <!-- Kelola Quiz -->
            <a href="{{ route('superadmin.quiz.index') }}" 
               class="px-4 py-3 rounded-2xl flex items-center gap-3 text-sm font-semibold transition {{ request()->routeIs('superadmin.quiz.*') || request()->routeIs('superadmin.soal.*') || ($menu ?? '') === 'quiz' ? 'bg-[#6D1837] text-white shadow-md shadow-amber-950/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span>Kelola Quiz</span>
            </a>
        </nav>
    </div>

    <!-- SIDEBAR FOOTER -->
    <div class="p-4 border-t border-slate-100 space-y-3">
        <!-- Logout Button -->
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 text-red-600 hover:bg-red-50 hover:text-red-700 text-sm font-semibold rounded-xl transition cursor-pointer">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
