@extends('layouts.app')

@section('title', 'Penerbitan Sertifikat')

@section('content')
<div class="space-y-6">

    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mb-1">Penerbitan Sertifikat Siswa</h1>
            <p class="text-xs sm:text-sm text-slate-500">Filter dan pilih siswa penerima, lalu lanjutkan untuk mengunggah dokumen sertifikat kelulusan / pencapaian.</p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200/90 shadow-2xs">
                {{ $sertifikat->count() }} Sertifikat Diterbitkan
            </span>
        </div>
    </div>

    {{-- Notifikasi Sukses / Error --}}
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-xs sm:text-sm flex items-center gap-2 shadow-2xs">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-xs sm:text-sm flex items-center gap-2 shadow-2xs">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- PANEL 1: PILIH SISWA TARGET PENERIMA SERTIFIKAT --}}
    <div class="bg-slate-50/70 rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs space-y-4">
        <div class="border-b border-slate-200/70 pb-3">
            <h2 class="text-sm font-bold text-slate-900">Pilih Siswa Target</h2>
            <p class="text-[11px] text-slate-500 mt-0.5">Gunakan filter kelas, jurusan, atau pencarian nama untuk menandai siswa penerima</p>
        </div>

        @if ($errors->has('student_ids'))
            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-2xs">
                {{ $errors->first('student_ids') }}
            </div>
        @endif

        <div class="space-y-4">

            {{-- Filter Kelas & Jurusan (Maroon Custom Dropdowns) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {{-- Custom Dropdown Kelas --}}
                <div class="relative" id="customDropdownKelas">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Filter Kelas</label>
                    <select id="filterKelas" class="hidden">
                        <option value="" selected>Semua Kelas</option>
                        @foreach(['10 1', '10 2'] as $k)
                            <option value="{{ $k }}">Kelas {{ str_replace(' ', '.', $k) }}</option>
                        @endforeach
                    </select>
                    <button type="button"
                            id="btnDropdownKelas"
                            class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs font-medium text-slate-800 hover:border-[#7B1038] focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition cursor-pointer text-left">
                        <span id="labelDropdownKelas" class="truncate">Semua Kelas</span>
                        <svg id="arrowDropdownKelas" class="custom-select-arrow w-4 h-4 text-slate-400 transition-transform duration-150 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="menuDropdownKelas"
                         class="custom-select-menu hidden absolute z-30 mt-1 w-full rounded-xl bg-white border border-slate-200 shadow-xl py-1 max-h-60 overflow-y-auto">
                        <div class="dropdown-item px-3 py-2 text-xs font-medium text-white bg-[#7B1038] hover:bg-[#7B1038] hover:text-white cursor-pointer transition flex items-center justify-between is-selected"
                             data-value="">
                            <span>Semua Kelas</span>
                            <span class="chk-icon">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        </div>
                        @foreach(['10 1', '10 2'] as $k)
                            <div class="dropdown-item px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 cursor-pointer transition flex items-center justify-between"
                                 data-value="{{ $k }}">
                                <span>Kelas {{ str_replace(' ', '.', $k) }}</span>
                                <span class="chk-icon hidden">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Custom Dropdown Jurusan --}}
                <div class="relative" id="customDropdownJurusan">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Filter Jurusan</label>
                    <select id="filterJurusan" class="hidden">
                        <option value="" selected>Semua Jurusan</option>
                        @foreach([
                            'Teknik Alat Berat',
                            'Teknik Kendaraan Ringan',
                            'Teknik Sepeda Motor',
                            'Teknik Pemesinan',
                            'Teknik Instalasi Listrik',
                            'Teknik Pembangkit Listrik',
                            'Teknik Mekatronika',
                            'Teknik Audio Video',
                            'Teknik Komputer & Jaringan',
                            'Teknik Konstruksi & Perumahan',
                            'Desain Permodelan & Informasi Bangunan',
                            'Desain Komunikasi Visual',
                        ] as $j)
                            <option value="{{ $j }}">{{ $j }}</option>
                        @endforeach
                    </select>
                    <button type="button"
                            id="btnDropdownJurusan"
                            class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs font-medium text-slate-800 hover:border-[#7B1038] focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition cursor-pointer text-left">
                        <span id="labelDropdownJurusan" class="truncate">Semua Jurusan</span>
                        <svg id="arrowDropdownJurusan" class="custom-select-arrow w-4 h-4 text-slate-400 transition-transform duration-150 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="menuDropdownJurusan"
                         class="custom-select-menu hidden absolute z-30 mt-1 w-full rounded-xl bg-white border border-slate-200 shadow-xl py-1 max-h-60 overflow-y-auto">
                        <div class="dropdown-item px-3 py-2 text-xs font-medium text-white bg-[#7B1038] hover:bg-[#7B1038] hover:text-white cursor-pointer transition flex items-center justify-between is-selected"
                             data-value="">
                            <span class="truncate">Semua Jurusan</span>
                            <span class="chk-icon">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        </div>
                        @foreach([
                            'Teknik Alat Berat',
                            'Teknik Kendaraan Ringan',
                            'Teknik Sepeda Motor',
                            'Teknik Pemesinan',
                            'Teknik Instalasi Listrik',
                            'Teknik Pembangkit Listrik',
                            'Teknik Mekatronika',
                            'Teknik Audio Video',
                            'Teknik Komputer & Jaringan',
                            'Teknik Konstruksi & Perumahan',
                            'Desain Permodelan & Informasi Bangunan',
                            'Desain Komunikasi Visual',
                        ] as $j)
                            <div class="dropdown-item px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 cursor-pointer transition flex items-center justify-between"
                                 data-value="{{ $j }}">
                                <span class="truncate">{{ $j }}</span>
                                <span class="chk-icon hidden">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Header Checklist & Search Siswa --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <label class="text-xs font-bold text-slate-700">
                        Daftar Siswa Tersedia
                        <span id="jumlahTampil" class="ml-1 text-[11px] font-normal text-slate-400"></span>
                    </label>

                    <div class="flex items-center gap-2">
                        <button type="button" id="btnCentangSemua"
                                class="px-3 py-1.5 text-xs font-bold rounded-xl bg-white text-slate-700 hover:bg-slate-100 border border-slate-300 transition duration-150 shadow-2xs disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">
                            ☑ Pilih Semua
                        </button>
                        <button type="button" id="btnHapusCentang"
                                class="px-3 py-1.5 text-xs font-bold rounded-xl bg-white text-slate-700 hover:bg-slate-100 border border-slate-300 transition duration-150 shadow-2xs disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                                disabled>
                            ☐ Hapus Semua
                        </button>
                    </div>
                </div>

                {{-- Input Cari Nama Siswa --}}
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           id="searchSiswa"
                           placeholder="Cari nama siswa berdasarkan kata kunci..."
                           class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-300 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition"
                           autocomplete="off">
                </div>

                {{-- Checkbox List Box --}}
                <div class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-2xs">
                    <ul id="siswaCheckboxList" class="divide-y divide-slate-100 max-h-72 overflow-y-auto"></ul>
                    <div id="siswaKosong" class="hidden px-4 py-8 text-center text-xs text-slate-400">
                        <svg class="w-8 h-8 mx-auto mb-1.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Tidak ada siswa yang sesuai filter atau pencarian.
                    </div>
                    <div id="siswaLoading" class="px-4 py-8 text-center text-xs text-slate-400">
                        Memuat data siswa...
                    </div>
                </div>
            </div>

            {{-- Ringkasan Siswa yang Dicentang --}}
            <div id="ringkasanTerpilih" class="hidden bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#7B1038]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Siswa Terpilih:
                        <span id="badgeJumlah" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">0</span>
                    </span>
                    <span class="text-[11px] text-slate-400">Klik tanda silang untuk membatalkan</span>
                </div>
                <div id="badgeContainer" class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto pt-1"></div>
            </div>

            {{-- Form Submit Lanjut ke Upload --}}
            <form action="{{ route('sertifikat.create') }}" method="POST" id="formPilihSiswa">
                @csrf
                <div id="hiddenInputsContainer"></div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-slate-200">
                    <p class="text-[11px] text-slate-500">Pilih siswa yang diinginkan di atas, lalu klik tombol untuk melanjutkan ke form berkas.</p>
                    <button type="submit"
                            id="btnTambahkanSiswa"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition duration-150 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                            disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                        Tambahkan Siswa &amp; Lanjut Upload
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- PANEL 2: RIWAYAT SERTIFIKAT YANG TELAH DITERBITKAN --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900 tracking-tight">
                    Riwayat Sertifikat yang Diterbitkan
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar sertifikat resmi yang telah diunggah kepada siswa.</p>
            </div>

            {{-- Input Pencarian Realtime Riwayat --}}
            <div class="relative w-full sm:w-64">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       id="search-riwayat"
                       placeholder="Cari siswa atau judul sertifikat..."
                       class="w-full pl-9 pr-3.5 py-1.5 text-xs rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
            </div>
        </div>

        {{-- Tabel Riwayat Responsif --}}
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-xs" id="table-riwayat">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase tracking-wider font-bold text-[11px]">
                        <th class="px-4 py-3">Nama Siswa</th>
                        <th class="px-4 py-3">Kelas &amp; Jurusan</th>
                        <th class="px-4 py-3">Judul Sertifikat</th>
                        <th class="px-4 py-3 text-center">Berkas</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($sertifikat as $item)
                        <tr class="hover:bg-slate-50/70 transition duration-75">
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-full bg-[#7B1038]/10 text-[#7B1038] flex items-center justify-center font-bold text-[10px] shrink-0 border border-[#7B1038]/20">
                                        {{ strtoupper(substr($item->siswa->nama_lengkap ?? 'S', 0, 1)) }}
                                    </span>
                                    <span class="font-bold text-slate-900">{{ $item->siswa->nama_lengkap ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-slate-600">
                                @if ($item->siswa)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        Kelas {{ str_replace(' ', '.', $item->siswa->kelas) }}
                                    </span>
                                    <span class="text-[11px] text-slate-500 ml-1">{{ $item->siswa->jurusan }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-slate-800">
                                {{ $item->judul_sertifikat }}
                            </td>
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @if($item->file_sertifikat)
                                    <a href="{{ asset('storage/' . $item->file_sertifikat) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#7B1038]/10 text-[#7B1038] hover:bg-[#7B1038] hover:text-white font-bold text-[11px] transition duration-150 border border-[#7B1038]/20">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Lihat Berkas
                                    </a>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <form action="{{ route('sertifikat.destroy', $item->id_sertifikat) }}"
                                      method="POST"
                                      class="inline m-0 p-0"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat sertifikat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Hapus Sertifikat"
                                            class="p-1.5 rounded-lg text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition duration-150 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-riwayat">
                            <td colspan="5" class="px-4 py-10 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="font-semibold text-xs text-slate-500">Belum ada sertifikat yang diterbitkan oleh guru.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Pilih siswa pada panel di atas untuk menerbitkan sertifikat baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100">
            <span>Sertifikat yang diterbitkan akan langsung masuk ke portofolio akun masing-masing siswa.</span>
            <span id="match-count-riwayat" class="font-medium text-slate-500"></span>
        </div>
    </div>
</div>

<style>
    #siswaCheckboxList { scrollbar-width: thin; scrollbar-color: #cbd5e1 #f8fafc; }
    #siswaCheckboxList::-webkit-scrollbar { width: 4px; }
    #siswaCheckboxList::-webkit-scrollbar-track { background: #f8fafc; }
    #siswaCheckboxList::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }

    /* Custom Dropdown Scrollbar */
    .custom-select-menu { scrollbar-width: thin; scrollbar-color: #E5CDD5 #ffffff; }
    .custom-select-menu::-webkit-scrollbar { width: 5px; }
    .custom-select-menu::-webkit-scrollbar-track { background: #ffffff; }
    .custom-select-menu::-webkit-scrollbar-thumb { background: #E5CDD5; border-radius: 4px; }
    .custom-select-menu::-webkit-scrollbar-thumb:hover { background: #7B1038; }

    /* Baris Siswa: abu-abu netral agar tidak terkesan merah pudar / warning */
    .siswa-row { transition: background-color .12s ease; cursor: pointer; user-select: none; }
    .siswa-row:hover { background-color: #f8fafc; }
    .siswa-row.is-checked { background-color: #f1f5f9; }
</style>

<script>
(function () {
    'use strict';

    /* ══ STATE ══ */
    let allSiswa      = [];
    let filteredSiswa = [];
    let checked       = new Set();

    /* ══ DOM ══ */
    const elFilterKelas  = document.getElementById('filterKelas');
    const elFilterJurusan= document.getElementById('filterJurusan');
    const elSearch       = document.getElementById('searchSiswa');
    const elList         = document.getElementById('siswaCheckboxList');
    const elEmpty        = document.getElementById('siswaKosong');
    const elLoading      = document.getElementById('siswaLoading');
    const elJumlah       = document.getElementById('jumlahTampil');
    const elBtnSemua     = document.getElementById('btnCentangSemua');
    const elBtnHapus     = document.getElementById('btnHapusCentang');
    const elRingkasan    = document.getElementById('ringkasanTerpilih');
    const elBadges       = document.getElementById('badgeContainer');
    const elBadgeJumlah  = document.getElementById('badgeJumlah');
    const elHidden       = document.getElementById('hiddenInputsContainer');
    const elBtnSubmit    = document.getElementById('btnTambahkanSiswa');
    const elForm         = document.getElementById('formPilihSiswa');

    /* ══ FETCH ══ */
    async function fetchSiswa() {
        elLoading.classList.remove('hidden');
        try {
            const r = await fetch('{{ route('sertifikat.siswa-json') }}');
            allSiswa = await r.json();
        } catch (e) {
            console.error(e);
            allSiswa = [];
        }
        elLoading.classList.add('hidden');
        applyFilter();
    }

    /* ══ FILTER ══ */
    function applyFilter() {
        const k = elFilterKelas.value;
        const j = elFilterJurusan.value;
        const q = elSearch.value.trim().toLowerCase();

        filteredSiswa = allSiswa.filter(s =>
            (!k || s.kelas   === k) &&
            (!j || s.jurusan === j) &&
            (!q || s.nama_lengkap.toLowerCase().includes(q))
        );

        renderList();
        syncButtons();
    }

    /* ══ RENDER LIST ══ */
    function renderList() {
        elList.innerHTML = '';

        if (!filteredSiswa.length) {
            elEmpty.classList.remove('hidden');
            elList.classList.add('hidden');
            elJumlah.textContent = '';
            return;
        }
        elEmpty.classList.add('hidden');
        elList.classList.remove('hidden');
        elJumlah.textContent = `(${filteredSiswa.length} siswa tampil)`;

        filteredSiswa.forEach(s => {
            const on = checked.has(s.nisn);
            const li = document.createElement('li');
            li.className    = 'siswa-row' + (on ? ' is-checked' : '');
            li.dataset.nisn = s.nisn;
            li.innerHTML    = itemHTML(s, on);

            li.addEventListener('click', () => toggle(s.nisn));

            elList.appendChild(li);
        });
    }

    function itemHTML(s, on) {
        const boxCls = on
            ? 'border-[#7B1038] bg-[#7B1038]'
            : 'border-slate-300 bg-white';
        const tick = on
            ? '<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>'
            : '';
        return `
            <div class="flex items-center gap-3 px-4 py-3">
                <span class="chk flex-shrink-0 flex w-4 h-4 rounded-md border-2 items-center justify-center transition-all ${boxCls}">${tick}</span>
                <span class="leading-tight">
                    <span class="block text-xs font-bold text-slate-800">${esc(s.nama_lengkap)}</span>
                    <span class="block text-[11px] text-slate-400">Kelas ${esc(s.kelas)} &mdash; ${esc(s.jurusan)}</span>
                </span>
            </div>`;
    }

    /* ══ TOGGLE ══ */
    function toggle(nisn) {
        checked.has(nisn) ? checked.delete(nisn) : checked.add(nisn);

        const li = elList.querySelector(`[data-nisn="${nisn}"]`);
        if (li) {
            const on  = checked.has(nisn);
            const box = li.querySelector('.chk');
            li.classList.toggle('is-checked', on);
            if (box) {
                box.className = box.className
                    .replace(on ? 'border-slate-300 bg-white' : 'border-[#7B1038] bg-[#7B1038]',
                             on ? 'border-[#7B1038] bg-[#7B1038]' : 'border-slate-300 bg-white');
                box.innerHTML = on
                    ? '<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>'
                    : '';
            }
        }
        syncAll();
    }

    function syncAll() { syncButtons(); renderBadges(); updateHidden(); }

    /* ══ CENTANG SEMUA / HAPUS ══ */
    elBtnSemua.addEventListener('click', () => {
        filteredSiswa.forEach(s => checked.add(s.nisn));
        renderList(); syncAll();
    });
    elBtnHapus.addEventListener('click', () => {
        filteredSiswa.forEach(s => checked.delete(s.nisn));
        renderList(); syncAll();
    });

    /* ══ BADGE RINGKASAN TERPILIH (ABU-ABU NETRAL) ══ */
    function renderBadges() {
        elBadges.innerHTML = '';
        if (!checked.size) { elRingkasan.classList.add('hidden'); return; }

        elRingkasan.classList.remove('hidden');
        elBadgeJumlah.textContent = checked.size;

        checked.forEach(nisn => {
            const s = allSiswa.find(x => x.nisn === nisn);
            if (!s) return;
            const span = document.createElement('span');
            span.className = 'inline-flex items-center gap-1.5 pl-2.5 pr-1 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-300 shadow-2xs';
            span.innerHTML = `<span>${esc(s.nama_lengkap)}</span><span class="text-slate-500 text-[10px]">· ${esc(s.kelas)}</span>
                <button type="button" class="ml-0.5 w-4 h-4 rounded-md bg-slate-200 hover:bg-slate-300 text-slate-600 hover:text-slate-900 flex items-center justify-center transition cursor-pointer" title="Hapus">
                    <svg class="w-2.5 h-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>`;
            span.querySelector('button').addEventListener('click', () => toggle(nisn));
            elBadges.appendChild(span);
        });
    }

    /* ══ HIDDEN INPUTS ══ */
    function updateHidden() {
        elHidden.innerHTML = '';
        checked.forEach(nisn => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'student_ids[]'; inp.value = nisn;
            elHidden.appendChild(inp);
        });
    }

    /* ══ BUTTON STATES ══ */
    function syncButtons() {
        const anyInFilter = filteredSiswa.some(s => checked.has(s.nisn));
        const allInFilter = filteredSiswa.length > 0 && filteredSiswa.every(s => checked.has(s.nisn));
        elBtnSemua.disabled  = !filteredSiswa.length || allInFilter;
        elBtnHapus.disabled  = !anyInFilter;
        elBtnSubmit.disabled = !checked.size;
    }

    /* ══ FORM GUARD ══ */
    elForm.addEventListener('submit', e => {
        if (!checked.size) { e.preventDefault(); alert('Pilih minimal satu siswa terlebih dahulu.'); }
    });

    /* ══ EVENTS ══ */
    function onDropdownFilterChange() {
        checked.clear();
        applyFilter();
        syncAll();
    }

    elFilterKelas.addEventListener('change', onDropdownFilterChange);
    elFilterJurusan.addEventListener('change', onDropdownFilterChange);
    elSearch.addEventListener('input', applyFilter);

    /* ══ UTIL ══ */
    function esc(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ══ CUSTOM DROPDOWN ENGINE (MAROON THEME) ══ */
    function initCustomDropdown(cfg) {
        const wrapper = document.getElementById(cfg.wrapperId);
        if (!wrapper) return;

        const select = document.getElementById(cfg.selectId);
        const btn    = document.getElementById(cfg.btnId);
        const label  = document.getElementById(cfg.labelId);
        const arrow  = document.getElementById(cfg.arrowId);
        const menu   = document.getElementById(cfg.menuId);
        if (!select || !btn || !label || !menu) return;

        const items  = menu.querySelectorAll('.dropdown-item');

        function openMenu() {
            document.querySelectorAll('.custom-select-menu').forEach(m => {
                if (m !== menu) {
                    m.classList.add('hidden');
                    const parent = m.closest('.relative');
                    if (parent) {
                        const arr = parent.querySelector('.custom-select-arrow');
                        if (arr) arr.classList.remove('rotate-180');
                    }
                }
            });
            menu.classList.remove('hidden');
            if (arrow) arrow.classList.add('rotate-180');
        }

        function closeMenu() {
            menu.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }

        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (menu.classList.contains('hidden')) {
                openMenu();
            } else {
                closeMenu();
            }
        });

        items.forEach(item => {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                const val = this.dataset.value;
                const text = this.querySelector('span:first-child').textContent.trim();

                label.textContent = text;

                items.forEach(it => {
                    const chk = it.querySelector('.chk-icon');
                    if (it === item) {
                        it.className = 'dropdown-item px-3 py-2 text-xs font-medium text-white bg-[#7B1038] hover:bg-[#7B1038] hover:text-white cursor-pointer transition flex items-center justify-between is-selected';
                        if (chk) chk.classList.remove('hidden');
                    } else {
                        it.className = 'dropdown-item px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 cursor-pointer transition flex items-center justify-between';
                        if (chk) chk.classList.add('hidden');
                    }
                });

                if (select.value !== val) {
                    select.value = val;
                    select.dispatchEvent(new Event('change'));
                }

                closeMenu();
            });
        });

        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                closeMenu();
            }
        });
    }

    /* ══ INIT ══ */
    initCustomDropdown({
        wrapperId: 'customDropdownKelas',
        selectId:  'filterKelas',
        btnId:     'btnDropdownKelas',
        labelId:   'labelDropdownKelas',
        arrowId:   'arrowDropdownKelas',
        menuId:    'menuDropdownKelas'
    });

    initCustomDropdown({
        wrapperId: 'customDropdownJurusan',
        selectId:  'filterJurusan',
        btnId:     'btnDropdownJurusan',
        labelId:   'labelDropdownJurusan',
        arrowId:   'arrowDropdownJurusan',
        menuId:    'menuDropdownJurusan'
    });

    fetchSiswa();

    /* ══ SEARCH RIWAYAT REALTIME ══ */
    const searchRiwayat = document.getElementById('search-riwayat');
    const tableBody = document.querySelector('#table-riwayat tbody');
    if (searchRiwayat && tableBody) {
        const rows = tableBody.querySelectorAll('tr:not(#empty-riwayat)');
        const matchCount = document.getElementById('match-count-riwayat');

        searchRiwayat.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            let visible = 0;

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if (query === '' || text.includes(query)) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (matchCount) {
                matchCount.textContent = query !== '' ? `Ditemukan: ${visible} dari ${rows.length}` : '';
            }
        });
    }
})();
</script>
@endsection
