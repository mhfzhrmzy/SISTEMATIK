@extends('layouts.app')
@section('title', 'Leaderboard Pembelajaran')
@section('content')
@php
    $isEmptyBoard = $leaderboards->isEmpty();
@endphp
@if ($isEmptyBoard)
@push('styles')
<style>
    @media (min-width: 768px) {
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        body > main {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        body > main > div {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
    }
</style>
@endpush
@endif
<div class="lb-page {{ $isEmptyBoard ? 'lb-empty-root pb-0' : 'pb-8' }}">

    <!-- Header Leaderboard -->
    <div class="lb-header-card {{ !$isSiswa ? 'lb-header-card--guru' : '' }}">
        <div class="lb-header-top">
            <div class="lb-header-text">
                <h1 class="lb-header-title">Peringkat Poin Siswa</h1>
                <p class="lb-header-subtitle">
                    @if ($isSiswa)
                        Peringkat perolehan poin Post-Test siswa
                    @else
                        Peringkat perolehan poin dari pengerjaan kuis Pre-Test dan Post-Test siswa.
                    @endif
                </p>
            </div>

            <div class="lb-header-actions">
                <span class="lb-chip">
                    {{ $leaderboards->count() }} Siswa Terdaftar
                </span>

                @if (! $isSiswa)
                    @php
                        $exportRoute = $isGuru ? 'guru.leaderboard.export' : 'leaderboard.export';

                        $exportQuery = array_filter(
                            request()->query() + [
                                'kelas' => $kelas,
                                'jurusan' => $jurusan,
                                'tipe_leaderboard' => $tipeLeaderboard,
                                'materi_id' => $selectedMateriId,
                                'tipe_test' => $filterTest,
                            ],
                            fn ($value) => $value !== null && $value !== ''
                        );
                    @endphp
                    <a href="{{ route($exportRoute, $exportQuery) }}" class="lb-chip lb-chip--link">
                        Export
                    </a>
                @endif

                @if (! $isSiswa && ($kelas || $jurusan || $selectedMateriId || $filterTest))
                    <a href="{{ url()->current() }}" class="lb-chip lb-chip--link">
                        Reset Filter
                    </a>
                @endif
            </div>
        </div>

        @if ($isSiswa)
            <div class="lb-header-chips">
                <span class="lb-chip">Kelas {{ $kelas }}</span>
                <span class="lb-chip">{{ $jurusan }}</span>
            </div>
        @endif
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ url()->current() }}" id="leaderboardFilterForm">

        @if ($isSiswa)
            <div class="lb-filter-group">
                {{-- Tombol Filter Keseluruhan / Permateri --}}
                <div class="lb-filter-row">
                    {{-- Tombol Keseluruhan --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="tipe_leaderboard" value="keseluruhan" class="hidden"
                               @checked($tipeLeaderboard === 'keseluruhan') onchange="this.form.submit()">
                        <span class="lb-filter-btn {{ $tipeLeaderboard === 'keseluruhan' ? 'lb-filter-btn--active' : 'lb-filter-btn--inactive' }}">
                            Keseluruhan
                        </span>
                    </label>

                    {{-- Tombol Permateri --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="tipe_leaderboard" value="permateri" class="hidden"
                               @checked($tipeLeaderboard === 'permateri') onchange="this.form.submit()">
                        <span class="lb-filter-btn {{ $tipeLeaderboard === 'permateri' ? 'lb-filter-btn--active' : 'lb-filter-btn--inactive' }}">
                            Per materi
                        </span>
                    </label>
                </div>

                {{-- Jika Tab Permateri: Tombol Pilih Materi --}}
                @if ($tipeLeaderboard === 'permateri')
                    <div class="lb-filter-row">
                        @forelse ($daftarMateri as $m)
                            @php
                                $isActiveMateri = (string)$selectedMateriId === (string)$m->id_materi;
                            @endphp
                            <label class="cursor-pointer">
                                <input type="radio" name="materi_id" value="{{ $m->id_materi }}" class="hidden"
                                       @checked($isActiveMateri) onchange="this.form.submit()">
                                <span class="lb-filter-btn {{ $isActiveMateri ? 'lb-filter-btn--active' : 'lb-filter-btn--inactive' }}">
                                    {{ $m->judul_materi }}
                                </span>
                            </label>
                        @empty
                            <div class="text-sm text-gray-600 font-medium">
                                Tidak ada materi untuk kelas &amp; jurusan ini.
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>
        @else
            {{-- Dropdown Tingkat Kelas & Jurusan Guru --}}
            <div class="flex items-center gap-3 flex-wrap mb-4">
                <div class="relative min-w-[160px]" id="customDropdownKelas">
                    <select name="kelas" id="filterKelas" class="hidden">
                        <option value="" @selected(empty($kelas))>Semua Kelas</option>
                        @foreach ($daftarKelas as $k)
                            <option value="{{ $k }}" @selected((string)$kelas === (string)$k)>Kelas {{ $k }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="btnDropdownKelas" class="lb-control-radius w-full flex items-center justify-between px-4 py-2.5 border border-gray-300 bg-white text-sm font-semibold text-gray-800 hover:border-[#7B1038] focus:outline-none transition cursor-pointer shadow-2xs">
                        <span id="labelDropdownKelas" class="truncate">{{ $kelas ? 'Kelas ' . $kelas : 'Semua Kelas' }}</span>
                        <svg id="arrowDropdownKelas" class="w-4 h-4 text-gray-400 transition-transform duration-150 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="menuDropdownKelas" class="hidden absolute z-30 mt-1 w-full rounded-xl bg-white border border-slate-200 shadow-xl py-1 max-h-60 overflow-y-auto">
                        <div class="dropdown-item-kelas px-3.5 py-2 text-sm font-medium {{ empty($kelas) ? 'text-white bg-[#7B1038] font-semibold' : 'text-gray-700 hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }} cursor-pointer transition flex items-center justify-between" data-value="">
                            <span>Semua Kelas</span>
                            <span class="chk-icon {{ empty($kelas) ? '' : 'hidden' }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                        </div>
                        @foreach ($daftarKelas as $k)
                            @php $isSel = (string)$kelas === (string)$k; @endphp
                            <div class="dropdown-item-kelas px-3.5 py-2 text-sm font-medium {{ $isSel ? 'text-white bg-[#7B1038] font-semibold' : 'text-gray-700 hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }} cursor-pointer transition flex items-center justify-between" data-value="{{ $k }}">
                                <span>Kelas {{ $k }}</span>
                                <span class="chk-icon {{ $isSel ? '' : 'hidden' }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="relative min-w-[220px]" id="customDropdownJurusan">
                    <select name="jurusan" id="filterJurusan" class="hidden">
                        @foreach ($daftarJurusan as $j)
                            <option value="{{ $j }}" @selected($jurusan === $j)>{{ $j }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="btnDropdownJurusan" class="lb-control-radius w-full flex items-center justify-between px-4 py-2.5 border border-gray-300 bg-white text-sm font-semibold text-gray-800 hover:border-[#7B1038] focus:outline-none transition cursor-pointer shadow-2xs">
                        <span id="labelDropdownJurusan" class="truncate">{{ $jurusan ?? $daftarJurusan[0] }}</span>
                        <svg id="arrowDropdownJurusan" class="w-4 h-4 text-gray-400 transition-transform duration-150 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="menuDropdownJurusan" class="hidden absolute z-30 mt-1 w-full rounded-xl bg-white border border-slate-200 shadow-xl py-1 max-h-60 overflow-y-auto">
                        @foreach ($daftarJurusan as $j)
                            @php $isSel = $jurusan === $j; @endphp
                            <div class="dropdown-item-jurusan px-3.5 py-2 text-sm font-medium {{ $isSel ? 'text-white bg-[#7B1038] font-semibold' : 'text-gray-700 hover:bg-[#7B1038]/10 hover:text-[#7B1038]' }} cursor-pointer transition flex items-center justify-between" data-value="{{ $j }}">
                                <span class="truncate">{{ $j }}</span>
                                <span class="chk-icon {{ $isSel ? '' : 'hidden' }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="lb-filter-group">
                {{-- Tombol Keseluruhan / Permateri Guru --}}
                <div class="lb-filter-row">
                    <label class="cursor-pointer">
                        <input type="radio" name="tipe_leaderboard" value="keseluruhan" class="hidden"
                               @checked($tipeLeaderboard === 'keseluruhan') onchange="this.form.submit()">
                        <span class="lb-filter-btn {{ $tipeLeaderboard === 'keseluruhan' ? 'lb-filter-btn--active' : 'lb-filter-btn--inactive' }}">
                            Keseluruhan
                        </span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="tipe_leaderboard" value="permateri" class="hidden"
                               @checked($tipeLeaderboard === 'permateri') onchange="this.form.submit()">
                        <span class="lb-filter-btn {{ $tipeLeaderboard === 'permateri' ? 'lb-filter-btn--active' : 'lb-filter-btn--inactive' }}">
                            Per materi
                        </span>
                    </label>
                </div>

                {{-- Jika Tab Permateri: Tombol Pilih Materi --}}
                @if ($tipeLeaderboard === 'permateri')
                    <div class="lb-filter-row">
                        @forelse ($daftarMateri as $m)
                            @php
                                $isActiveMateri = (string)$selectedMateriId === (string)$m->id_materi;
                            @endphp
                            <label class="cursor-pointer">
                                <input type="radio" name="materi_id" value="{{ $m->id_materi }}" class="hidden"
                                       @checked($isActiveMateri) onchange="this.form.submit()">
                                <span class="lb-filter-btn {{ $isActiveMateri ? 'lb-filter-btn--active' : 'lb-filter-btn--inactive' }}">
                                    {{ $m->judul_materi }}
                                </span>
                            </label>
                        @empty
                            <div class="text-sm text-gray-600 font-medium">
                                Tidak ada materi untuk kelas &amp; jurusan ini.
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>
        @endif

        {{-- Baris Status & Badge Tipe Test --}}
        <div class="lb-status-bar">
            @if ($tipeLeaderboard === 'permateri')
                @if ($selectedMateri)
                    <div class="text-sm text-gray-600 font-medium">
                        Menampilkan Leaderboard: <strong class="text-base font-semibold text-gray-900">{{ $selectedMateri->judul_materi }}</strong>
                    </div>
                    @if (!$isSiswa)
                        {{-- Opsi Tipe Test Guru --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-semibold text-gray-700 mr-1">Tipe Test:</span>
                            <label class="cursor-pointer">
                                <input type="radio" name="tipe_test" value="pretest" class="hidden" @checked($filterTest === 'pretest') onchange="this.form.submit()">
                                <span class="lb-control-radius inline-flex items-center px-4 py-1.5 text-sm font-semibold transition {{ $filterTest === 'pretest' ? 'bg-[#7B1038] text-white shadow-xs' : 'border border-gray-300 text-gray-700 bg-white hover:border-[#7B1038]' }}">
                                    Pre-Test
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="tipe_test" value="posttest" class="hidden" @checked($filterTest === 'posttest') onchange="this.form.submit()">
                                <span class="lb-control-radius inline-flex items-center px-4 py-1.5 text-sm font-semibold transition {{ $filterTest === 'posttest' ? 'bg-[#7B1038] text-white shadow-xs' : 'border border-gray-300 text-gray-700 bg-white hover:border-[#7B1038]' }}">
                                    Post-Test
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="tipe_test" value="" class="hidden" @checked(empty($filterTest)) onchange="this.form.submit()">
                                <span class="lb-control-radius inline-flex items-center px-4 py-1.5 text-sm font-semibold transition {{ empty($filterTest) ? 'bg-[#7B1038] text-white shadow-xs' : 'border border-gray-300 text-gray-700 bg-white hover:border-[#7B1038]' }}">
                                    Semua
                                </span>
                            </label>
                        </div>
                    @endif
                @else
                    <p class="text-sm text-gray-600">
                        Silakan pilih salah satu materi di atas untuk melihat nilai leaderboard.
                    </p>
                    <div></div>
                @endif
            @else
                {{-- State Keseluruhan --}}
                <p class="text-sm text-gray-600">
                    Akumulasi seluruh nilai Post-Test siswa di Kelas {{ $kelas }} | {{ $jurusan }}.
                </p>
                @if (!$isSiswa)
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-semibold text-gray-700 mr-1">Tipe Test:</span>
                        <label class="cursor-pointer">
                            <input type="radio" name="tipe_test" value="" class="hidden" @checked(empty($filterTest)) onchange="this.form.submit()">
                            <span class="lb-control-radius inline-flex items-center px-4 py-1.5 text-sm font-semibold transition {{ empty($filterTest) ? 'bg-[#7B1038] text-white shadow-xs' : 'border border-gray-300 text-gray-700 bg-white hover:border-[#7B1038]' }}">
                                Semua
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="tipe_test" value="pretest" class="hidden" @checked($filterTest === 'pretest') onchange="this.form.submit()">
                            <span class="lb-control-radius inline-flex items-center px-4 py-1.5 text-sm font-semibold transition {{ $filterTest === 'pretest' ? 'bg-[#7B1038] text-white shadow-xs' : 'border border-gray-300 text-gray-700 bg-white hover:border-[#7B1038]' }}">
                                Pre-Test
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="tipe_test" value="posttest" class="hidden" @checked($filterTest === 'posttest') onchange="this.form.submit()">
                            <span class="lb-control-radius inline-flex items-center px-4 py-1.5 text-sm font-semibold transition {{ $filterTest === 'posttest' ? 'bg-[#7B1038] text-white shadow-xs' : 'border border-gray-300 text-gray-700 bg-white hover:border-[#7B1038]' }}">
                                Post-Test
                            </span>
                        </label>
                    </div>
                @endif
            @endif
        </div>

    </form>

    <!-- Table Leaderboard -->
    <div class="lb-table-card {{ $isSiswa ? 'lb-table-card--siswa' : '' }} {{ $isEmptyBoard ? 'lb-table-card--empty' : '' }}">
        <div class="lb-table-scroll">
            <table class="lb-table">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-semibold text-xs">
                        <th class="lb-col-rank">Peringkat</th>
                        <th class="lb-col-name">Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Materi &amp; Quiz</th>
                        <th class="text-center">Tipe Test</th>
                        @if (!$isSiswa)
                        <th class="text-center">Total Poin</th>
                        <th class="text-right">Waktu Selesai</th>
                        @endif
                    </tr>
                </thead>
                @if (!$isEmptyBoard)
                <tbody class="divide-y divide-gray-100">
                    @foreach ($leaderboards as $index => $row)
                        @php
                            $isOwnAccount = auth('siswa')->check() && (string)auth('siswa')->id() === (string)$row->nisn;
                        @endphp
                        <tr class="{{ $isOwnAccount ? 'lb-row--own' : '' }}">
                            {{-- Peringkat --}}
                            <td class="lb-col-rank">
                                @if ($isSiswa)
                                    <span class="text-sm font-medium text-gray-700 tabular-nums">{{ $index + 1 }}</span>
                                @else
                                    <span class="lb-rank-circle {{ $index === 0 ? 'lb-rank-circle--first' : 'lb-rank-circle--other' }}">
                                        {{ $index + 1 }}
                                    </span>
                                @endif
                            </td>

                            {{-- Nama Siswa --}}
                            <td class="lb-col-name whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-700">{{ $row->pengguna->nama_lengkap ?? 'Siswa' }}</span>
                            </td>

                            {{-- NISN --}}
                            <td class="text-sm font-medium text-gray-700 tabular-nums whitespace-nowrap">
                                {{ $row->nisn }}
                            </td>

                            {{-- Atribut KELAS --}}
                            <td class="text-sm font-medium text-gray-700 whitespace-nowrap">
                                {{ $row->pengguna?->kelas ? 'Kelas ' . $row->pengguna->kelas : '-' }}
                            </td>

                            {{-- Atribut JURUSAN --}}
                            <td class="lb-cell-wrap text-sm font-medium text-gray-700">
                                {{ $row->pengguna?->jurusan ?? '-' }}
                            </td>

                            {{-- Materi & Quiz --}}
                            <td class="lb-cell-wrap">
                                @if ($tipeLeaderboard === 'permateri')
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ $selectedMateri->judul_materi ?? ($row->quiz->materi->judul_materi ?? '-') }}
                                    </span>
                                @else
                                    <span class="text-sm font-medium text-gray-700">Semua Materi</span>
                                    <span class="text-xs text-gray-500 block mt-0.5">({{ $row->total_kuis ?? 0 }} kuis diselesaikan)</span>
                                @endif
                            </td>

                            {{-- Tipe Test --}}
                            <td class="text-center text-sm font-medium text-gray-700 whitespace-nowrap">
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
                                {{ $badgeLabel }}
                            </td>

                            {{-- Total Poin --}}
                            @if (!$isSiswa)
                            <td class="text-center whitespace-nowrap">
                                <span class="text-sm font-medium text-[#7B1038] tabular-nums">{{ $row->total_poin }} Poin</span>
                            </td>
                            <td class="text-right text-sm font-medium text-gray-500 whitespace-nowrap">
                                {{ $row->updated_at ? \Carbon\Carbon::parse($row->updated_at)->diffForHumans() : '-' }}
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
        </div>
        @if ($isEmptyBoard)
            <div class="lb-empty-fill bg-white">
                <div class="flex flex-col items-center justify-center max-w-md mx-auto">
                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-base font-semibold text-gray-800">Belum ada riwayat nilai kuis untuk filter ini.</p>
                    <p class="text-sm text-gray-500 mt-1">
                        @if ($tipeLeaderboard === 'permateri' && ! $selectedMateriId)
                            Silakan pilih salah satu materi di atas untuk melihat nilai leaderboard.
                        @else
                            Coba ubah filter kelas, jurusan, atau tipe test di panel atas.
                        @endif
                    </p>
                </div>
            </div>
        @endif
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
