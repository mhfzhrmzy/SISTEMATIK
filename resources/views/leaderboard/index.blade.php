@extends('layouts.app')
@section('title', 'Leaderboard Pembelajaran')
@section('content')
<div class="max-w-6xl mx-auto py-2 space-y-6">

    <!-- Header Leaderboard -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h1 class="text-2xl font-bold text-gray-800">
                    Leaderboard dan Peringkat Nilai Siswa
                </h1>
            </div>
            <p class="text-sm text-gray-600">
                @if ($isSiswa)
                    Peringkat perolehan nilai Post-Test siswa di Kelas {{ $kelas }} - {{ $jurusan }}.
                @else
                    Peringkat perolehan poin dari pengerjaan kuis Pre-Test dan Post-Test siswa.
                @endif
            </p>
        </div>

        @if (! $isSiswa && ($kelas || $jurusan || $selectedMateriId || $filterTest))
            <div>
                <a href="{{ url()->current() }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#5E0C2B] hover:text-[#7B1038] bg-gray-100 hover:bg-[#7B1038]/10 px-3.5 py-1.5 rounded-full transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset Filter
                </a>
            </div>
        @elseif ($isSiswa && ($selectedMateriId || $tipeLeaderboard === 'permateri'))
            <div>
                <a href="{{ route('leaderboard.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#5E0C2B] hover:text-[#7B1038] bg-gray-100 hover:bg-[#7B1038]/10 px-3.5 py-1.5 rounded-full transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Kembali ke Keseluruhan
                </a>
            </div>
        @endif
    </div>

    <!-- Panel Filter Guru / Siswa -->
    <div class="bg-white rounded-2xl p-6 shadow-sm space-y-6">
        <form method="GET" action="{{ url()->current() }}" id="leaderboardFilterForm">

            @if ($isSiswa)
                {{-- Banner Informasi Kelas Siswa --}}
                <div class="bg-[#7B1038]/5 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#7B1038] text-white flex items-center justify-center text-lg font-bold shadow-xs">
                            🎓
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-[#7B1038] uppercase tracking-wider">Ruang Kelas &amp; Jurusan Anda</div>
                            <div class="text-base font-bold text-gray-900">
                                Kelas {{ $kelas }} • {{ $jurusan }}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- 1. PEMILIHAN KELAS DAN JURUSAN (UNTUK GURU) --}}
                <div class="mb-5">
                    <div class="flex items-center gap-2 mb-2">
                        <h3 class="text-xs font-bold text-[#5E0C2B] uppercase tracking-wider">
                            Pilih Kelas &amp; Jurusan Yang Dituju
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {{-- Dropdown Kelas --}}
                        <div>
                            <label for="filterKelas" class="block text-xs font-semibold text-gray-600 mb-1">Tingkat Kelas</label>
                            <select name="kelas" id="filterKelas" onchange="this.form.submit()"
                                    class="w-full bg-gray-50/80 border-0 rounded-xl px-3.5 py-2.5 text-sm font-medium text-gray-800 focus:ring-2 focus:ring-[#7B1038] transition">
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($daftarKelas as $k)
                                    <option value="{{ $k }}" @selected((string)$kelas === (string)$k)>
                                        Kelas {{ $k }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Dropdown Jurusan --}}
                        <div>
                            <label for="filterJurusan" class="block text-xs font-semibold text-gray-600 mb-1">Jurusan / Program Keahlian</label>
                            <select name="jurusan" id="filterJurusan" onchange="this.form.submit()"
                                    class="w-full bg-gray-50/80 border-0 rounded-xl px-3.5 py-2.5 text-sm font-medium text-gray-800 focus:ring-2 focus:ring-[#7B1038] transition">
                                @foreach ($daftarJurusan as $j)
                                    <option value="{{ $j }}" @selected($jurusan === $j)>
                                        {{ $j }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 2. TIPE LEADERBOARD: KESELURUHAN vs PER MATERI (Referensi Desain: Maroon & Putih, Clean, Tanpa Outer Stroke) --}}
            <div class="space-y-4">
                <div class="inline-flex p-1.5 rounded-2xl bg-gray-100/90 gap-1.5">
                    {{-- Tombol Keseluruhan --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="tipe_leaderboard" value="keseluruhan" class="hidden"
                               @checked($tipeLeaderboard === 'keseluruhan') onchange="this.form.submit()">
                        <span class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ $tipeLeaderboard === 'keseluruhan' ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#7B1038] hover:bg-white/80' }}">
                            Keseluruhan
                        </span>
                    </label>

                    {{-- Tombol Permateri --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="tipe_leaderboard" value="permateri" class="hidden"
                               @checked($tipeLeaderboard === 'permateri') onchange="this.form.submit()">
                        <span class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ $tipeLeaderboard === 'permateri' ? 'bg-[#7B1038] text-white shadow-xs' : 'text-[#7B1038] hover:bg-white/80' }}">
                            Permateri
                        </span>
                    </label>
                </div>

                {{-- 3. DETAIL PILIHAN: JIKA PER MATERI (Daftar Materi MS Word, MS Excel, Power Point, dll.) --}}
                @if ($tipeLeaderboard === 'permateri')
                    <div class="space-y-3 pt-1">
                        {{-- Pills Tombol Materi Sesuai Referensi Gambar --}}
                        <div class="flex flex-wrap gap-2 items-center">
                            @forelse ($daftarMateri as $m)
                                @php
                                    $isActiveMateri = (string)$selectedMateriId === (string)$m->id_materi;
                                @endphp
                                <label class="cursor-pointer">
                                    <input type="radio" name="materi_id" value="{{ $m->id_materi }}" class="hidden"
                                           @checked($isActiveMateri) onchange="this.form.submit()">
                                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-150 {{ $isActiveMateri ? 'bg-[#7B1038] text-white shadow-xs' : 'bg-gray-100 hover:bg-[#7B1038]/10 text-[#7B1038]' }}">
                                        {{ $m->judul_materi }}
                                    </span>
                                </label>
                            @empty
                                <div class="py-2 text-xs text-gray-500">
                                    Tidak ada materi untuk kelas &amp; jurusan ini.
                                </div>
                            @endforelse
                        </div>

                        {{-- Opsi Tipe Test: Muncul ketika materi sudah dipilih --}}
                        @if ($selectedMateri)
                            @if ($isSiswa)
                                <div class="pt-2 flex items-center justify-between flex-wrap gap-2">
                                    <div class="text-xs text-gray-600">
                                        Menampilkan Leaderboard: <strong class="text-[#7B1038]">{{ $selectedMateri->judul_materi }}</strong>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-[#7B1038]/10 text-[#7B1038] text-xs font-bold rounded-full">
                                        Post-Test
                                    </span>
                                </div>
                            @else
                                {{-- Untuk Guru: Opsi Pre-Test dan Post-Test --}}
                                <div class="pt-2">
                                    <div class="flex flex-wrap gap-2 items-center">
                                        <span class="text-xs font-semibold text-gray-500 mr-1">Tipe Test:</span>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="tipe_test" value="pretest" class="hidden"
                                                   @checked($filterTest === 'pretest') onchange="this.form.submit()">
                                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $filterTest === 'pretest' ? 'bg-[#7B1038] text-white shadow-xs' : 'bg-gray-100 text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                                                Pre-Test
                                            </span>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="tipe_test" value="posttest" class="hidden"
                                                   @checked($filterTest === 'posttest') onchange="this.form.submit()">
                                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $filterTest === 'posttest' ? 'bg-[#7B1038] text-white shadow-xs' : 'bg-gray-100 text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                                                Post-Test
                                            </span>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="tipe_test" value="" class="hidden"
                                                   @checked(empty($filterTest)) onchange="this.form.submit()">
                                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ empty($filterTest) ? 'bg-[#7B1038] text-white shadow-xs' : 'bg-gray-100 text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                                                Semua (Pre &amp; Post)
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="p-3 bg-gray-50 rounded-xl text-xs text-gray-500 flex items-center gap-2">
                                <span>Silakan pilih salah satu tombol materi di atas untuk melihat nilai leaderboard.</span>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- OPSI UNTUK KESELURUHAN --}}
                    @if ($isSiswa)
                        <div class="bg-[#7B1038]/5 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="text-xs font-bold text-[#7B1038] uppercase tracking-wider mb-1">
                                    Leaderboard Post-Test Keseluruhan
                                </div>
                                <p class="text-xs text-gray-500">
                                    Akumulasi seluruh nilai Post-Test siswa di Kelas {{ $kelas }} - {{ $jurusan }}.
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-[#7B1038] text-white self-start sm:self-auto">
                                Post-Test Only
                            </span>
                        </div>
                    @else
                        {{-- Untuk Guru: Opsi Tipe Test Keseluruhan --}}
                        <div class="pt-1">
                            <div class="flex flex-wrap gap-2 items-center">
                                <span class="text-xs font-semibold text-gray-500 mr-1">Tipe Test:</span>
                                <label class="cursor-pointer">
                                    <input type="radio" name="tipe_test" value="" class="hidden"
                                           @checked(empty($filterTest)) onchange="this.form.submit()">
                                    <span class="inline-block px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ empty($filterTest) ? 'bg-[#7B1038] text-white shadow-xs' : 'bg-gray-100 text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                                        Semua Test
                                    </span>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="tipe_test" value="pretest" class="hidden"
                                           @checked($filterTest === 'pretest') onchange="this.form.submit()">
                                     <span class="inline-block px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $filterTest === 'pretest' ? 'bg-[#7B1038] text-white shadow-xs' : 'bg-gray-100 text-[#7B1038] hover:bg-[#7B1038]/10' }}">
                                        Pre-Test Only
                                    </span>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="tipe_test" value="posttest" class="hidden"
                                           @checked($filterTest === 'posttest') onchange="this.form.submit()">
                                     <span class="inline-block px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $filterTest === 'posttest' ? 'bg-[#7B1038] text-white shadow-xs' : 'bg-gray-100 text-[#7B1038] hover:bg-[#7B1038]/10' }}">
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

    <!-- Active Filter Summary Banner -->
    <div class="flex flex-wrap items-center justify-between gap-2 text-xs bg-white rounded-2xl px-5 py-3 shadow-xs text-gray-600">
        <div class="flex flex-wrap items-center gap-1.5">
            <span class="font-bold text-[#5E0C2B]">Filter Aktif:</span>
            <span class="bg-[#7B1038]/10 text-[#7B1038] font-semibold px-3 py-1 rounded-full">
                {{ $kelas ? 'Kelas ' . $kelas : 'Semua Kelas' }}
            </span>
            <span class="bg-[#7B1038]/10 text-[#7B1038] font-semibold px-3 py-1 rounded-full">
                {{ $jurusan ? $jurusan : 'Semua Jurusan' }}
            </span>
            <span class="bg-[#7B1038]/10 text-[#7B1038] font-semibold px-3 py-1 rounded-full">
                {{ $tipeLeaderboard === 'permateri' ? ('Per Materi: ' . ($selectedMateri->judul_materi ?? 'Semua Materi')) : 'Keseluruhan' }}
            </span>
            <span class="bg-[#7B1038]/10 text-[#7B1038] font-semibold px-3 py-1 rounded-full">
                {{ $filterTest ? ucfirst($filterTest) : 'Semua Test' }}
            </span>
        </div>
        <div class="text-gray-500 font-medium">
            Total Siswa Terdaftar: <span class="font-bold text-[#7B1038]">{{ $leaderboards->count() }}</span>
        </div>
    </div>

    <!-- Top 3 Podiums (If Data Available) -->
    @if ($leaderboards->count() >= 1)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
            @foreach ($leaderboards->take(3) as $rank => $item)
                @php
                    $colors = [
                        0 => 'bg-gradient-to-b from-amber-100/70 via-amber-50/50 to-white text-amber-900', // Gold
                        1 => 'bg-gradient-to-b from-slate-100/70 via-slate-50/50 to-white text-slate-800',  // Silver
                        2 => 'bg-gradient-to-b from-orange-100/70 via-orange-50/50 to-white text-orange-900', // Bronze
                    ];
                    $badges = [0 => '🥇 PERINGKAT 1', 1 => '🥈 PERINGKAT 2', 2 => '🥉 PERINGKAT 3'];
                @endphp
                <div class="rounded-2xl p-5 text-center shadow-xs relative overflow-hidden {{ $colors[$rank] ?? 'bg-white' }}">
                    <div class="text-xs font-extrabold uppercase tracking-wider mb-1.5">{{ $badges[$rank] }}</div>
                    <div class="text-lg font-bold text-gray-900 truncate">{{ $item->pengguna->nama_lengkap ?? 'Siswa' }}</div>
                    <div class="text-xs text-gray-500 mb-2 font-mono">NISN: {{ $item->nisn }}</div>

                    {{-- Atribut Kelas & Jurusan di Card Podium --}}
                    <div class="inline-flex flex-wrap items-center justify-center gap-1.5 mb-3">
                        @if ($item->pengguna?->kelas)
                            <span class="text-xs font-semibold px-2.5 py-0.5 bg-[#7B1038]/10 text-[#7B1038] rounded-full">
                                Kelas {{ $item->pengguna->kelas }}
                            </span>
                        @endif
                        @if ($item->pengguna?->jurusan)
                            <span class="text-xs font-medium px-2.5 py-0.5 bg-gray-100 text-gray-700 rounded-full truncate max-w-[200px]" title="{{ $item->pengguna->jurusan }}">
                                {{ $item->pengguna->jurusan }}
                            </span>
                        @endif
                    </div>

                    @if (!$isSiswa)
                    <div class="text-3xl font-black text-[#7B1038] mb-1">
                        {{ $item->total_poin }} <span class="text-xs font-normal text-gray-500">Poin</span>
                    </div>
                    @endif

                    <div class="text-xs text-gray-600 font-medium">
                        @php
                            $testLabel = match (strtolower($filterTest ?: ($item->quiz->tipe_test ?? ''))) {
                                'pretest' => 'Pre-Test',
                                'posttest' => 'Post-Test',
                                default => 'Semua Test',
                            };
                        @endphp
                        @if ($tipeLeaderboard === 'permateri')
                            {{ $selectedMateri->judul_materi ?? ($item->quiz->materi->judul_materi ?? 'Quiz') }}
                            <span class="text-gray-500">({{ $testLabel }})</span>
                        @else
                            {{ $item->total_kuis ?? 1 }} Kuis Diselesaikan
                            <span class="text-gray-500">({{ $testLabel }})</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Table Leaderboard -->
    <div class="bg-white rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50/80 text-[#5E0C2B] text-xs font-bold uppercase tracking-wider">
                        <th class="px-4 py-3.5 text-center w-16">Peringkat</th>
                        <th class="px-4 py-3.5">Nama Siswa</th>
                        <th class="px-4 py-3.5">NISN</th>
                        <th class="px-4 py-3.5">Kelas</th>
                        <th class="px-4 py-3.5">Jurusan</th>
                        <th class="px-4 py-3.5">Materi &amp; Quiz</th>
                        <th class="px-4 py-3.5 text-center">Tipe Test</th>
                        @if (!$isSiswa)
                        <th class="px-4 py-3.5 text-center">Total Poin</th>
                        <th class="px-4 py-3.5 text-right">Waktu Selesai</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($leaderboards as $index => $row)
                        <tr class="hover:bg-gray-50/70 transition {{ auth('siswa')->check() && auth('siswa')->id() == $row->nisn ? 'bg-[#7B1038]/5 font-medium' : '' }}">
                            {{-- Peringkat --}}
                            <td class="px-4 py-3.5 text-center font-bold">
                                @if ($index === 0) 🥇 1
                                @elseif ($index === 1) 🥈 2
                                @elseif ($index === 2) 🥉 3
                                @else <span class="text-gray-600">#{{ $index + 1 }}</span>
                                @endif
                            </td>

                            {{-- Nama Siswa --}}
                            <td class="px-4 py-3.5 font-medium text-gray-900">
                                {{ $row->pengguna->nama_lengkap ?? 'Siswa' }}
                                @if (auth('siswa')->check() && auth('siswa')->id() == $row->nisn)
                                    <span class="ml-2 text-xs bg-[#7B1038] text-white px-2 py-0.5 rounded-full font-bold">(Anda)</span>
                                @endif
                            </td>

                            {{-- NISN --}}
                            <td class="px-4 py-3.5 text-gray-500 text-xs font-mono">{{ $row->nisn }}</td>

                            {{-- Atribut KELAS --}}
                            <td class="px-4 py-3.5">
                                @if ($row->pengguna?->kelas)
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#7B1038]/10 text-[#7B1038]">
                                        Kelas {{ $row->pengguna->kelas }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>

                            {{-- Atribut JURUSAN --}}
                            <td class="px-4 py-3.5 text-xs text-gray-700 font-medium">
                                {{ $row->pengguna?->jurusan ?? '-' }}
                            </td>

                            {{-- Materi & Quiz --}}
                            <td class="px-4 py-3.5 text-gray-800">
                                @if ($tipeLeaderboard === 'permateri')
                                    <span class="font-semibold text-gray-900">
                                        {{ $selectedMateri->judul_materi ?? ($row->quiz->materi->judul_materi ?? '-') }}
                                    </span>
                                @else
                                    <span class="font-semibold text-gray-800">Semua Materi</span>
                                    <span class="text-xs text-gray-500 block">({{ $row->total_kuis ?? 0 }} kuis diselesaikan)</span>
                                @endif
                            </td>

                            {{-- Tipe Test --}}
                            <td class="px-4 py-3.5 text-center">
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
                                <span class="whitespace-nowrap px-3 py-1 rounded-full text-xs font-bold bg-[#7B1038]/10 text-[#7B1038]">
                                    {{ $badgeLabel }}
                                </span>
                            </td>

                            {{-- Total Poin --}}
                            @if (!$isSiswa)
                            <td class="px-4 py-3.5 text-center font-black text-[#7B1038] text-base">
                                {{ $row->total_poin }}
                            </td>
                            @endif

                            {{-- Waktu Selesai --}}
                            @if (!$isSiswa)
                            <td class="px-4 py-3.5 text-right text-xs text-gray-500">
                                {{ $row->updated_at ? \Carbon\Carbon::parse($row->updated_at)->diffForHumans() : '-' }}
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isSiswa ? 7 : 9 }}" class="px-4 py-12 text-center text-gray-400">
                                <div class="max-w-md mx-auto space-y-2">
                                    <div class="text-3xl">📊</div>
                                    <p class="font-medium text-gray-600">Belum ada riwayat nilai kuis untuk filter ini.</p>
                                    <p class="text-xs text-gray-400">
                                        @if ($tipeLeaderboard === 'permateri' && ! $selectedMateriId)
                                            Silakan pilih materi di atas untuk melihat nilai leaderboard.
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
@endsection
