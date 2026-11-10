<!-- DASHBOARD VIEW (IKHTISAR OPERASIONAL) -->
<div class="space-y-6">
    <!-- Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Dashboard</h1>
            <p class="text-xs text-slate-500 mt-1">Pusat kendali manajemen akun guru dan siswa SMKN 2 Jember.</p>
        </div>
    </div>

    <!-- 4 STAT CARDS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Guru -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-sm">
            <p class="text-[10px] font-semibold text-slate-500 tracking-wider uppercase mb-2">TOTAL AKUN GURU</p>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-semibold text-slate-800 tracking-tight">
                    {{ $gurus->count() }}
                </span>
                <span class="text-xs font-medium text-slate-800">Pendidik</span>
            </div>
            <p class="text-xs font-normal text-slate-400 mt-2">Terdaftar di sistem</p>
        </div>

        <!-- Card 2: Total Siswa -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-sm">
            <p class="text-[10px] font-semibold text-slate-500 tracking-wider uppercase mb-2">TOTAL AKUN SISWA</p>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-semibold text-slate-800 tracking-tight">
                    {{ $siswas->count() }}
                </span>
                <span class="text-xs font-medium text-slite-800">Peserta Didik</span>
            </div>
            <p class="text-xs font-normal text-slate-400 mt-2">Terdaftar di sistem</p>
        </div>

        <!-- Card 3: Modul Materi -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-sm">
            <p class="text-[10px] font-semibold text-slate-500 tracking-wider uppercase mb-2">TOTAL MATERI</p>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-semibold text-slate-800 tracking-tight">
                    {{ $materiCount }}
                </span>
                <span class="text-xs font-medium text-slate-800">Materi</span>
            </div>
            <p class="text-xs font-normal text-slate-400 mt-2">Terdaftar di sistem</p>
        </div>

        <!-- Card 4: Quiz -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-sm">
            <p class="text-[10px] font-semibold text-slate-500 tracking-wider uppercase">TOTAL QUIZ</p>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-semibold text-slate-800 tracking-tight">
                    {{ $quizCount }}
                </span>
                <span class="text-xs font-medium text-black">Quiz</span>
            </div>
            <p class="text-xs font-normal text-slate-400 mt-2">Terdaftar di sistem</p>
        </div>
    </div>

    <!-- LOWER 2-COLUMN SECTION: DAFTAR GURU & DAFTAR SISWA -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- LEFT COLUMN: DAFTAR AKUN GURU -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div>
                        <h2 class="text-base font-bold text-black">Daftar Akun Guru</h2>
                        <p class="text-[11px] text-black">Total {{ $gurus->count() }} Guru Terdaftar</p>
                    </div>
                </div>
                <a href="{{ route('superadmin.index', ['menu' => 'guru']) }}" class="text-xs font-bold text-[#4a101d] hover:underline flex items-center gap-1">
                    Kelola Guru &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/80 text-black uppercase tracking-wider font-bold">
                            <th class="px-3 py-2 rounded-l-lg">NIP</th>
                            <th class="px-3 py-2">Nama</th>
                            <th class="px-3 py-2 text-right rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($gurus as $guru)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-3 py-2 font-normal text-black">{{ $guru->nip }}</td>
                                <td class="px-3 py-2 font-normal text-black">{{ $guru->nama_lengkap }}</td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                                onclick="openEditGuruModal('{{ $guru->nip }}', '{{ addslashes($guru->nama_lengkap) }}')"
                                                class="text-amber-600 font-medium hover:underline cursor-pointer">Edit</button>
                                        <form action="{{ route('admin.destroy', $guru) }}" method="POST" onsubmit="return confirm('Hapus akun guru ini?')">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="from" value="superadmin">
                                            <button type="submit" class="text-red-600 font-medium hover:underline cursor-pointer">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-slate-400 font-medium">Belum ada akun guru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIGHT COLUMN: DAFTAR AKUN SISWA -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/70 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div>
                        <h2 class="text-base font-bold text-black">Daftar Akun Siswa</h2>
                        <p class="text-[11px] text-black">Total {{ $siswas->count() }} Siswa Terdaftar</p>
                    </div>
                </div>
                <a href="{{ route('superadmin.index', ['menu' => 'siswa']) }}" class="text-xs font-bold text-amber-600 hover:underline flex items-center gap-1">
                    Kelola Siswa &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/80 text-black uppercase tracking-wider font-bold">
                            <th class="px-3 py-2 rounded-l-lg">NISN</th>
                            <th class="px-3 py-2">Nama</th>
                            <th class="px-3 py-2 text-right rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($siswas as $siswa)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-3 py-2 font-normal text-black">{{ $siswa->nisn }}</td>
                                <td class="px-3 py-2 font-normal text-black">{{ $siswa->nama_lengkap }}</td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                                onclick="openEditSiswaModal('{{ $siswa->nisn }}', '{{ addslashes($siswa->nama_lengkap) }}', '{{ $siswa->kelas }}', '{{ $siswa->tahun }}', '{{ addslashes($siswa->jurusan) }}')"
                                                class="text-amber-600 font-medium hover:underline cursor-pointer">Edit</button>
                                        <form action="{{ route('siswa.destroy', $siswa) }}" method="POST" onsubmit="return confirm('Hapus akun siswa ini?')">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="from" value="superadmin">
                                            <button type="submit" class="text-red-600 font-medium hover:underline cursor-pointer">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-slate-400 font-medium">Belum ada akun siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
