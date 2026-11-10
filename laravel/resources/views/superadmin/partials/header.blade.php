<!-- TOP HEADER BAR -->
<header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-6 py-3.5 flex items-center justify-between sticky top-0 z-20">
    <div class="flex items-center gap-3 flex-wrap">
        <!-- Search Input Form -->
        <form action="{{ route('superadmin.index') }}" method="GET" class="relative hidden md:block">
            @if(request('menu'))
                <input type="hidden" name="menu" value="{{ request('menu') }}">
            @endif
            <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" name="search" id="superadmin-search-input" value="{{ request('search') }}" placeholder="Cari NIP, NISN, Guru, Siswa, Kelas..." 
                   class="w-72 lg:w-96 pl-9 pr-8 py-1.5 text-xs rounded-xl bg-indigo-50/40 border border-indigo-100/80 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 focus:bg-white transition">
            @if(request('search'))
                <a href="{{ route('superadmin.index', ['menu' => request('menu', 'dashboard')]) }}" class="absolute right-3 top-1.5 text-slate-400 hover:text-slate-700 text-sm font-bold" title="Hapus Filter">&times;</a>
            @endif
        </form>
    </div>

    <!-- Profile -->
    <div class="flex items-center gap-4">

        <!-- Profile Badge -->
        <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
            <div class="text-right hidden sm:block">
                <span class="text-xs font-bold text-slate-800 block leading-tight">
                    {{ auth('superadmin')->user()->nama_lengkap ?? 'Administrator Utama' }}
                </span>
            </div>
            <div class="w-9 h-9 rounded-full bg-[#4a101d] text-white flex items-center justify-center font-bold text-xs shadow-md shadow-amber-950/20 ring-2 ring-[#4a101d]/20 shrink-0">
                {{ strtoupper(substr(auth('superadmin')->user()->nama_lengkap ?? 'SA', 0, 2)) }}
            </div>
        </div>
    </div>
</header>
