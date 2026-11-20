<!-- TOP HEADER BAR -->
<header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-6 py-3.5 flex items-center justify-between sticky top-0 z-20">
    <div class="flex items-center gap-3 flex-wrap">
        <!-- Search Input Form -->
        <form action="{{ route('superadmin.index') }}" method="GET" class="relative hidden md:block">
            @if(request('menu'))
                <input type="hidden" name="menu" value="{{ request('menu') }}">
            @endif
            <input type="text" name="search" id="superadmin-search-input" value="{{ request('search') }}" placeholder="Cari NIP, NISN, Guru, Siswa, atau Kelas..."
                   class="w-72 lg:w-96 px-5 py-2.5 text-sm rounded-xl bg-indigo-50/40 border border-indigo-100/80 text-black placeholder-black focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 focus:bg-white transition">
            @if(request('search'))
                <a href="{{ route('superadmin.index', ['menu' => request('menu', 'dashboard')]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 text-base font-bold" title="Hapus Filter">&times;</a>
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
