@extends('layouts.app')
@section('title', 'Leaderboard Pembelajaran')
@section('content')
<div class="w-full space-y-6">

    <!-- Header Leaderboard -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mb-1">
                Leaderboard dan Peringkat Nilai Siswa
            </h1>
            <p class="text-xs sm:text-sm text-slate-500">
                @if ($isSiswa)
                    Peringkat perolehan nilai Post-Test siswa di Kelas {{ $kelas }} &bull; {{ $jurusan }}.
                @else
                    Peringkat perolehan poin dari pengerjaan kuis Pre-Test dan Post-Test siswa.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200/90 shadow-2xs">
                {{ $leaderboards->count() }} Siswa Terdaftar
            </span>

            @if (! $isSiswa && ($kelas || $jurusan || $selectedMateriId || $filterTest))
                <a href="{{ url()->current() }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 border border-slate-200 px-3.5 py-1.5 rounded-xl transition duration-150 shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset Filter
                </a>
            @elseif ($isSiswa && ($selectedMateriId || $tipeLeaderboard === 'permateri'))
                <a href="{{ route('leaderboard.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 border border-slate-200 px-3.5 py-1.5 rounded-xl transition duration-150 shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Kembali ke Keseluruhan
                </a>
            @endif
        </div>
    </div>

    <!-- Panel Filter Guru / Siswa -->
    <div class="bg-slate-50/70 rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs space-y-4">
        <form method="GET" action="{{ url()->current() }}" id="leaderboardFilterForm">

            @if ($isSiswa)
                {{-- Banner Informasi Kelas Siswa --}}
                <div class="bg-white rounded-xl border border-slate-200 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#7B1038] text-white flex items-center justify-center text-sm font-bold shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ruang Kelas &amp; Jurusan Anda</div>
                            <div class="text-sm font-bold text-slate-900">
                                Kelas {{ $kelas }} • {{ $jurusan }}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- 1. PEMILIHAN KELAS DAN JURUSAN (UNTUK GURU) --}}
                <div class="mb-4">
                    <div class="border-b border-slate-200/70 pb-2.5 mb-3">
                        <h2 class="text-sm font-bold text-slate-900">Pilih Kelas &amp; Jurusan Yang Dituju</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Filter data peringkat siswa berdasarkan tingkat kelas dan program keahlian</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {{-- Custom Dropdown Maroon: Tingkat Kelas --}}
                        <div class="relative" id="customDropdownKelas">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tingkat Kelas</label>
                            <select name="kelas" id="filterKelas" class="hidden">
                                <option value="" @selected(empty($kelas))>Semua Kelas</option>
                                @foreach ($daftarKelas as $k)
                                    <option value="{{ $k }}" @selected((string)$kelas === (string)$k)>
                                        Kelas {{ $k }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button"
                                    id="btnDropdownKelas"
                                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-medium text-slate-800 hover:border-[#7B1038] focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition cursor-pointer text-left shadow-2xs">
                                <span id="labelDropdownKelas" class="truncate">
                                    {{ $kelas ? 'Kelas ' . $kelas : 'Semua Kelas' }}
                                </span>
                                <svg id="arrowDropdownKelas" class="w-4 h-4 text-slate-400 transition-transform duration-150 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="menuDropdownKelas"
                                 class="hidden absolute z-30 mt-1 w-full rounded-xl bg-white border border-slate-200 shadow-xl py-1 max-h-60 overflow-y-auto">
                                <div class="dropdown-item-kelas px-3 py-2 text-xs font-medium {{ empty($kelas) ? 'text-white bg-[#7B1038] font-bold' : 'text-slate-700 hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }} cursor-pointer transition flex items-center justify-between"
                                     data-value="">
                                    <span>Semua Kelas</span>
                                    <span class="chk-icon {{ empty($kelas) ? '' : 'hidden' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </div>
                                @foreach ($daftarKelas as $k)
                                    @php $isSel = (string)$kelas === (string)$k; @endphp
                                    <div class="dropdown-item-kelas px-3 py-2 text-xs font-medium {{ $isSel ? 'text-white bg-[#7B1038] font-bold' : 'text-slate-700 hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }} cursor-pointer transition flex items-center justify-between"
                                         data-value="{{ $k }}">
                                        <span>Kelas {{ $k }}</span>
                                        <span class="chk-icon {{ $isSel ? '' : 'hidden' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Custom Dropdown Maroon: Jurusan --}}
                        <div class="relative" id="customDropdownJurusan">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jurusan / Program Keahlian</label>
                            <select name="jurusan" id="filterJurusan" class="hidden">
                                @foreach ($daftarJurusan as $j)
                                    <option value="{{ $j }}" @selected($jurusan === $j)>{{ $j }}</option>
                                @endforeach
                            </select>
                            <button type="button"
                                    id="btnDropdownJurusan"
                                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-medium text-slate-800 hover:border-[#7B1038] focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition cursor-pointer text-left shadow-2xs">
                                <span id="labelDropdownJurusan" class="truncate">{{ $jurusan ?? $daftarJurusan[0] }}</span>
                                <svg id="arrowDropdownJurusan" class="w-4 h-4 text-slate-400 transition-transform duration-150 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="menuDropdownJurusan"
                                 class="hidden absolute z-30 mt-1 w-full rounded-xl bg-white border border-slate-200 shadow-xl py-1 max-h-60 overflow-y-auto">
                                @foreach ($daftarJurusan as $j)
                                    @php $isSel = $jurusan === $j; @endphp
                                    <div class="dropdown-item-jurusan px-3 py-2 text-xs font-medium {{ $isSel ? 'text-white bg-[#7B1038] font-bold' : 'text-slate-700 hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }} cursor-pointer transition flex items-center justify-between"
                                         data-value="{{ $j }}">
                                        <span class="truncate">{{ $j }}</span>
                                        <span class="chk-icon {{ $isSel ? '' : 'hidden' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 2. TIPE LEADERBOARD: KESELURUHAN vs PER MATERI --}}
            <div class="space-y-3 pt-1">
                <div class="inline-flex p-1 rounded-xl bg-slate-200/80 border border-slate-200 gap-1 shadow-2xs">
                    {{-- Tombol Keseluruhan --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="tipe_leaderboard" value="keseluruhan" class="hidden"
                               @checked($tipeLeaderboard === 'keseluruhan') onchange="this.form.submit()">
                        <span class="inline-flex items-center justify-center px-5 py-2 rounded-lg text-xs font-bold transition-all duration-150 {{ $tipeLeaderboard === 'keseluruhan' ? 'bg-[#7B1038] text-white shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-white/70' }}">
                            Keseluruhan
                        </span>
                    </label>

                    {{-- Tombol Permateri --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="tipe_leaderboard" value="permateri" class="hidden"
                               @checked($tipeLeaderboard === 'permateri') onchange="this.form.submit()">
                        <span class="inline-flex items-center justify-center px-5 py-2 rounded-lg text-xs font-bold transition-all duration-150 {{ $tipeLeaderboard === 'permateri' ? 'bg-[#7B1038] text-white shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-white/70' }}">
                            Permateri
                        </span>
                    </label>
                </div>

                {{-- 3. DETAIL PILIHAN: JIKA PER MATERI --}}
                @if ($tipeLeaderboard === 'permateri')
                    <div class="space-y-3 pt-1">
                        {{-- Pills Tombol Materi --}}
                        <div class="flex flex-wrap gap-2 items-center">
                            @forelse ($daftarMateri as $m)
                                @php
                                    $isActiveMateri = (string)$selectedMateriId === (string)$m->id_materi;
                                @endphp
                                <label class="cursor-pointer">
                                    <input type="radio" name="materi_id" value="{{ $m->id_materi }}" class="hidden"
                                           @checked($isActiveMateri) onchange="this.form.submit()">
                                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-150 {{ $isActiveMateri ? 'bg-[#7B1038] text-white border border-[#7B1038] shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200 shadow-2xs' }}">
                                        {{ $m->judul_materi }}
                                    </span>
                                </label>
                            @empty
                                <div class="py-2 text-xs text-slate-500 font-medium">
                                    Tidak ada materi untuk kelas &amp; jurusan ini.
                                </div>
                            @endforelse
                        </div>

                        {{-- Opsi Tipe Test: Muncul ketika materi sudah dipilih --}}
                        @if ($selectedMateri)
                            @if ($isSiswa)
                                <div class="pt-2 flex items-center justify-between flex-wrap gap-2">
                                    <div class="text-xs text-slate-600 font-medium">
                                        Menampilkan Leaderboard: <strong class="text-slate-900">{{ $selectedMateri->judul_materi }}</strong>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 bg-[#7B1038] text-white text-[11px] font-bold rounded-lg shadow-2xs">
                                        Post-Test
                                    </span>
                                </div>
                            @else
                                {{-- Untuk Guru: Opsi Pre-Test dan Post-Test --}}
                                <div class="pt-1">
                                    <div class="flex flex-wrap gap-2 items-center">
                                        <span class="text-xs font-bold text-slate-600 mr-1">Tipe Test:</span>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="tipe_test" value="pretest" class="hidden"
                                                   @checked($filterTest === 'pretest') onchange="this.form.submit()">
                                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $filterTest === 'pretest' ? 'bg-[#7B1038] text-white border border-[#7B1038] shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200 shadow-2xs' }}">
                                                Pre-Test
                                            </span>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="tipe_test" value="posttest" class="hidden"
                                                   @checked($filterTest === 'posttest') onchange="this.form.submit()">
                                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $filterTest === 'posttest' ? 'bg-[#7B1038] text-white border border-[#7B1038] shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200 shadow-2xs' }}">
                                                Post-Test
                                            </span>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="tipe_test" value="" class="hidden"
                                                   @checked(empty($filterTest)) onchange="this.form.submit()">
                                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ empty($filterTest) ? 'bg-[#7B1038] text-white border border-[#7B1038] shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200 shadow-2xs' }}">
                                                Semua (Pre &amp; Post)
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="p-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-500 flex items-center gap-2 shadow-2xs">
                                <span>Silakan pilih salah satu materi di atas untuk melihat nilai leaderboard.</span>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- OPSI UNTUK KESELURUHAN --}}
                    @if ($isSiswa)
                        <div class="bg-white border border-slate-200 rounded-xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                            <div>
                                <div class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-0.5">
                                    Leaderboard Post-Test Keseluruhan
                                </div>
                                <p class="text-xs text-slate-500">
                                    Akumulasi seluruh nilai Post-Test siswa di Kelas {{ $kelas }} &bull; {{ $jurusan }}.
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-[#7B1038] text-white self-start sm:self-auto shadow-2xs">
                                Post-Test Only
                            </span>
                        </div>
                    @else
                        {{-- Untuk Guru: Opsi Tipe Test Keseluruhan --}}
                        <div class="pt-1">
                            <div class="flex flex-wrap gap-2 items-center">
                                <span class="text-xs font-bold text-slate-600 mr-1">Tipe Test:</span>
                                <label class="cursor-pointer">
                                    <input type="radio" name="tipe_test" value="" class="hidden"
                                           @checked(empty($filterTest)) onchange="this.form.submit()">
                                    <span class="inline-block px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ empty($filterTest) ? 'bg-[#7B1038] text-white border border-[#7B1038] shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200 shadow-2xs' }}">
                                        Semua Test
                                    </span>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="tipe_test" value="pretest" class="hidden"
                                           @checked($filterTest === 'pretest') onchange="this.form.submit()">
                                     <span class="inline-block px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $filterTest === 'pretest' ? 'bg-[#7B1038] text-white border border-[#7B1038] shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200 shadow-2xs' }}">
                                        Pre-Test Only
                                    </span>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="tipe_test" value="posttest" class="hidden"
                                           @checked($filterTest === 'posttest') onchange="this.form.submit()">
                                     <span class="inline-block px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $filterTest === 'posttest' ? 'bg-[#7B1038] text-white border border-[#7B1038] shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200 shadow-2xs' }}">
                                        Post-Test Only
                                    </span>
                                </label>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

        </form>
    </div>

    <!-- Top 3 Podiums (If Data Available) -->
    @if ($leaderboards->count() >= 1)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
            @foreach ($leaderboards->take(3) as $rank => $item)
                @php
                    $colors = [
                        0 => 'bg-gradient-to-b from-amber-100/70 via-amber-50/50 to-white text-amber-900 border-amber-200/80', // Gold
                        1 => 'bg-gradient-to-b from-slate-100/70 via-slate-50/50 to-white text-slate-800 border-slate-200/80',  // Silver
                        2 => 'bg-gradient-to-b from-orange-100/70 via-orange-50/50 to-white text-orange-900 border-orange-200/80', // Bronze
                    ];
                    $badges = [0 => '🥇 PERINGKAT 1', 1 => '🥈 PERINGKAT 2', 2 => '🥉 PERINGKAT 3'];
                @endphp
                <div class="rounded-2xl p-5 text-center shadow-2xs border relative overflow-hidden {{ $colors[$rank] ?? 'bg-white border-slate-200' }}">
                    <div class="text-xs font-black uppercase tracking-wider mb-1.5">{{ $badges[$rank] }}</div>
                    <div class="text-base font-bold text-slate-900 truncate">{{ $item->pengguna->nama_lengkap ?? 'Siswa' }}</div>
                    <div class="text-[11px] text-slate-500 mb-2 font-mono">NISN: {{ $item->nisn }}</div>

                    {{-- Atribut Kelas & Jurusan di Card Podium --}}
                    <div class="inline-flex flex-wrap items-center justify-center gap-1.5 mb-3">
                        @if ($item->pengguna?->kelas)
                            <span class="text-[11px] font-bold px-2 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-md">
                                Kelas {{ $item->pengguna->kelas }}
                            </span>
                        @endif
                        @if ($item->pengguna?->jurusan)
                            <span class="text-[11px] font-medium px-2 py-0.5 bg-slate-100 text-slate-600 border border-slate-200 rounded-md truncate max-w-[200px]" title="{{ $item->pengguna->jurusan }}">
                                {{ $item->pengguna->jurusan }}
                            </span>
                        @endif
                    </div>

                    @if (!$isSiswa)
                    <div class="text-2xl sm:text-3xl font-black text-[#7B1038] mb-1">
                        {{ $item->total_poin }} <span class="text-xs font-semibold text-slate-500">Poin</span>
                    </div>
                    @endif

                    <div class="text-xs text-slate-600 font-medium">
                        @php
                            $testLabel = match (strtolower($filterTest ?: ($item->quiz->tipe_test ?? ''))) {
                                'pretest' => 'Pre-Test',
                                'posttest' => 'Post-Test',
                                default => 'Semua Test',
                            };
                        @endphp
                        @if ($tipeLeaderboard === 'permateri')
                            {{ $selectedMateri->judul_materi ?? ($item->quiz->materi->judul_materi ?? 'Quiz') }}
                            <span class="text-slate-400">({{ $testLabel }})</span>
                        @else
                            {{ $item->total_kuis ?? 1 }} Kuis Diselesaikan
                            <span class="text-slate-400">({{ $testLabel }})</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Table Leaderboard -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase tracking-wider font-bold text-[11px]">
                        <th class="px-4 py-3 text-center w-16">Peringkat</th>
                        <th class="px-4 py-3">Nama Siswa</th>
                        <th class="px-4 py-3">NISN</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Jurusan</th>
                        <th class="px-4 py-3">Materi &amp; Quiz</th>
                        <th class="px-4 py-3 text-center">Tipe Test</th>
                        @if (!$isSiswa)
                        <th class="px-4 py-3 text-center">Total Poin</th>
                        <th class="px-4 py-3 text-right">Waktu Selesai</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($leaderboards as $index => $row)
                        <tr class="hover:bg-slate-50/70 transition duration-75 {{ auth('siswa')->check() && auth('siswa')->id() == $row->nisn ? 'bg-amber-50/50 font-medium' : '' }}">
                            {{-- Peringkat --}}
                            <td class="px-4 py-3.5 text-center font-bold">
                                @if ($index === 0) 🥇 1
                                @elseif ($index === 1) 🥈 2
                                @elseif ($index === 2) 🥉 3
                                @else <span class="text-slate-500">#{{ $index + 1 }}</span>
                                @endif
                            </td>

                            {{-- Nama Siswa --}}
                            <td class="px-4 py-3.5 font-bold text-slate-900 whitespace-nowrap">
                                {{ $row->pengguna->nama_lengkap ?? 'Siswa' }}
                                @if (auth('siswa')->check() && auth('siswa')->id() == $row->nisn)
                                    <span class="ml-1.5 text-[10px] bg-[#7B1038] text-white px-2 py-0.5 rounded-full font-bold shadow-2xs">(Anda)</span>
                                @endif
                            </td>

                            {{-- NISN --}}
                            <td class="px-4 py-3.5 text-slate-500 font-mono text-xs whitespace-nowrap">{{ $row->nisn }}</td>

                            {{-- Atribut KELAS --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if ($row->pengguna?->kelas)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        Kelas {{ $row->pengguna->kelas }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>

                            {{-- Atribut JURUSAN --}}
                            <td class="px-4 py-3.5 text-xs text-slate-600 font-medium whitespace-nowrap max-w-[180px] truncate" title="{{ $row->pengguna?->jurusan ?? '-' }}">
                                {{ $row->pengguna?->jurusan ?? '-' }}
                            </td>

                            {{-- Materi & Quiz --}}
                            <td class="px-4 py-3.5 text-slate-800">
                                @if ($tipeLeaderboard === 'permateri')
                                    <span class="font-bold text-slate-900">
                                        {{ $selectedMateri->judul_materi ?? ($row->quiz->materi->judul_materi ?? '-') }}
                                    </span>
                                @else
                                    <span class="font-bold text-slate-900">Semua Materi</span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">({{ $row->total_kuis ?? 0 }} kuis diselesaikan)</span>
                                @endif
                            </td>

                            {{-- Tipe Test --}}
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @php
                                    $badgeTest = ($tipeLeaderboard === 'keseluruhan')
                                        ? ($filterTest ?? 'Semua')
                                        : ($row->quiz->tipe_test ?? $filterTest ?? '-');

                                    $badgeLabel = match (strtolower($badgeTest)) {
                                        'pretest' => 'Pre-Test',
                                        'posttest' => 'Post-Test',
                                        default => 'Semua Test',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $badgeLabel }}
                                </span>
                            </td>

                            {{-- Total Poin --}}
                            @if (!$isSiswa)
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <span class="font-bold text-[#7B1038] text-sm">{{ $row->total_poin }} Poin</span>
                            </td>
                            @endif

                            {{-- Waktu Selesai --}}
                            @if (!$isSiswa)
                            <td class="px-4 py-3.5 text-right text-xs text-slate-500 whitespace-nowrap">
                                {{ $row->updated_at ? \Carbon\Carbon::parse($row->updated_at)->diffForHumans() : '-' }}
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isSiswa ? 7 : 9 }}" class="px-4 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center max-w-md mx-auto py-4">
                                    <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="font-bold text-xs text-slate-600">Belum ada riwayat nilai kuis untuk filter ini.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        @if ($tipeLeaderboard === 'permateri' && ! $selectedMateriId)
                                            Silakan pilih salah satu materi di atas untuk melihat nilai leaderboard.
                                        @else
                                            Coba ubah filter kelas, jurusan, atau tipe test di panel atas.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('leaderboardFilterForm');

    // === Custom Dropdown Kelas ===
    const btnKelas = document.getElementById('btnDropdownKelas');
    const menuKelas = document.getElementById('menuDropdownKelas');
    const arrowKelas = document.getElementById('arrowDropdownKelas');
    const selectKelas = document.getElementById('filterKelas');
    const labelKelas = document.getElementById('labelDropdownKelas');

    if (btnKelas && menuKelas) {
        btnKelas.addEventListener('click', function (e) {
            e.stopPropagation();
            if (menuJurusan) menuJurusan.classList.add('hidden');
            if (arrowJurusan) arrowJurusan.classList.remove('rotate-180');
            menuKelas.classList.toggle('hidden');
            arrowKelas.classList.toggle('rotate-180');
        });

        menuKelas.querySelectorAll('.dropdown-item-kelas').forEach(function (item) {
            item.addEventListener('click', function () {
                const val = this.dataset.value;
                selectKelas.value = val;
                labelKelas.textContent = val ? ('Kelas ' + val) : 'Semua Kelas';
                menuKelas.classList.add('hidden');
                arrowKelas.classList.remove('rotate-180');
                form.submit();
            });
        });
    }

    // === Custom Dropdown Jurusan ===
    const btnJurusan = document.getElementById('btnDropdownJurusan');
    const menuJurusan = document.getElementById('menuDropdownJurusan');
    const arrowJurusan = document.getElementById('arrowDropdownJurusan');
    const selectJurusan = document.getElementById('filterJurusan');
    const labelJurusan = document.getElementById('labelDropdownJurusan');

    if (btnJurusan && menuJurusan) {
        btnJurusan.addEventListener('click', function (e) {
            e.stopPropagation();
            if (menuKelas) menuKelas.classList.add('hidden');
            if (arrowKelas) arrowKelas.classList.remove('rotate-180');
            menuJurusan.classList.toggle('hidden');
            arrowJurusan.classList.toggle('rotate-180');
        });

        menuJurusan.querySelectorAll('.dropdown-item-jurusan').forEach(function (item) {
            item.addEventListener('click', function () {
                const val = this.dataset.value;
                selectJurusan.value = val;
                labelJurusan.textContent = val;
                menuJurusan.classList.add('hidden');
                arrowJurusan.classList.remove('rotate-180');
                form.submit();
            });
        });
    }

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function () {
        if (menuKelas) {
            menuKelas.classList.add('hidden');
            if (arrowKelas) arrowKelas.classList.remove('rotate-180');
        }
        if (menuJurusan) {
            menuJurusan.classList.add('hidden');
            if (arrowJurusan) arrowJurusan.classList.remove('rotate-180');
        }
    });
});
</script>
@endsection
