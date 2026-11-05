@extends('layouts.app')

@section('title', 'Kelola Bahan Ajar')

@section('content')
<div class="space-y-6">

    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#7B1038]/10 text-[#7B1038]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Kelola Bahan Ajar</h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-500">Unggah modul pembelajaran baru dan kelola daftar materi berdasarkan kelas, jurusan, serta tahun ajaran.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-[#7B1038]/10 text-[#7B1038] border border-[#7B1038]/20 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-[#7B1038]"></span>
                {{ count($materis ?? []) }} Modul Terdaftar
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

    @if($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 p-4 text-xs sm:text-sm shadow-2xs">
            <p class="font-bold flex items-center gap-1.5 mb-1.5">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Gagal menyimpan materi:
            </p>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Layout Grid 2 Kolom: Form (Kiri) & Tabel (Kanan) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- KOLOM KIRI: FORM TAMBAH BAHAN AJAR --}}
        <div class="lg:col-span-4 bg-slate-50/70 rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs">
            <div class="flex items-center gap-2 border-b border-slate-200/70 pb-3 mb-4">
                <div class="w-7 h-7 rounded-lg bg-[#7B1038] text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                    +
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Tambah Bahan Ajar Baru</h2>
                    <p class="text-[11px] text-slate-500">Lengkapi formulir modul materi pelajaran</p>
                </div>
            </div>

            <form action="{{ route('materi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf

                {{-- Diampu Oleh (Guru) --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Diampu Oleh (Guru)</label>
                    <div class="relative">
                        <input type="text"
                               value="{{ auth('admin')->user()->nama_lengkap ?? 'Guru Terautentikasi' }}"
                               readonly
                               class="w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3.5 py-2 text-xs font-semibold text-slate-600 cursor-not-allowed">
                        <input type="hidden" name="nip" value="{{ auth('admin')->id() }}">
                    </div>
                </div>

                {{-- Baris Mini Grid: Tahun Ajaran & Kelas --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Tahun <span class="text-rose-500">*</span>
                        </label>
                        <input type="number"
                               name="tahun"
                               id="tahun"
                               value="{{ old('tahun', date('Y')) }}"
                               min="2020"
                               required
                               class="w-full rounded-xl border {{ $errors->has('tahun') ? 'border-rose-400 bg-rose-50' : 'border-slate-300 bg-white' }} px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
                        @error('tahun')
                            <p class="text-rose-600 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Kelas <span class="text-rose-500">*</span>
                        </label>
                        <select name="kelas"
                                id="kelas"
                                required
                                class="w-full rounded-xl border {{ $errors->has('kelas') ? 'border-rose-400 bg-rose-50' : 'border-slate-300 bg-white' }} px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
                            <option value="" disabled {{ old('kelas') ? '' : 'selected' }}>Pilih Kelas</option>
                            @foreach(['10 1', '10 2', '11 1', '11 2', '12 1', '12 2'] as $k)
                                <option value="{{ $k }}" {{ old('kelas') == $k ? 'selected' : '' }}>
                                    Kelas {{ str_replace(' ', '.', $k) }}
                                </option>
                            @endforeach
                        </select>
                        @error('kelas')
                            <p class="text-rose-600 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Jurusan --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Jurusan <span class="text-rose-500">*</span>
                    </label>
                    <select name="jurusan"
                            id="jurusan"
                            required
                            class="w-full rounded-xl border {{ $errors->has('jurusan') ? 'border-rose-400 bg-rose-50' : 'border-slate-300 bg-white' }} px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
                        <option value="" disabled {{ old('jurusan') ? '' : 'selected' }}>-- Pilih Jurusan --</option>
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
                            <option value="{{ $j }}" {{ old('jurusan') == $j ? 'selected' : '' }}>
                                {{ $j }}
                            </option>
                        @endforeach
                    </select>
                    @error('jurusan')
                        <p class="text-rose-600 text-[11px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Judul Materi --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Judul Materi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="judul_materi"
                           id="judul_materi"
                           value="{{ old('judul_materi') }}"
                           pattern="[a-zA-Z0-9 ]+"
                           oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '')"
                           placeholder="Contoh: Pemrograman Web Dasar 1"
                           required
                           class="w-full rounded-xl border {{ $errors->has('judul_materi') ? 'border-rose-400 bg-rose-50' : 'border-slate-300 bg-white' }} px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
                    @error('judul_materi')
                        <p class="text-rose-600 text-[11px] mt-1 font-medium">{{ $message }}</p>
                    @else
                        <p class="text-slate-400 text-[10px] mt-1">Hanya huruf, angka, dan spasi yang diperbolehkan.</p>
                    @enderror
                </div>

                {{-- Deskripsi Materi --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Deskripsi Materi <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea name="isi_materi"
                              rows="3"
                              placeholder="Tuliskan ringkasan pokok bahasan atau tujuan materi..."
                              class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">{{ old('isi_materi') }}</textarea>
                </div>

                {{-- Upload File Modul --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Upload File Modul <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="file"
                               name="upload_file"
                               accept=".pdf,.ppt,.pptx,.doc,.docx"
                               required
                               class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#7B1038]/10 file:text-[#7B1038] hover:file:bg-[#7B1038]/20 file:transition file:cursor-pointer border border-slate-300 rounded-xl bg-white p-1.5 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20">
                    </div>
                    <p class="text-slate-400 text-[10px] mt-1">Format: PDF, PPT, PPTX, DOC, DOCX (Maks. 25 MB)</p>
                    @error('upload_file')
                        <p class="text-rose-600 text-[11px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol Submit --}}
                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-[#7B1038] hover:bg-[#5E0C2B] text-white font-bold py-2.5 px-4 rounded-xl shadow-xs hover:shadow transition duration-150 cursor-pointer text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Simpan Bahan Ajar
                    </button>
                </div>
            </form>
        </div>

        {{-- KOLOM KANAN: TABEL DAFTAR BAHAN AJAR --}}
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs space-y-4">

            {{-- Toolbar Atas: Judul & Input Pencarian --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#7B1038]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Daftar Bahan Ajar
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar semua materi pelajaran yang telah diunggah ke sistem.</p>
                </div>

                {{-- Input Pencarian Real-time --}}
                <div class="relative w-full sm:w-64">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           id="search-materi"
                           placeholder="Cari judul, kelas, jurusan..."
                           class="w-full pl-9 pr-3.5 py-1.5 text-xs rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
                </div>
            </div>

            {{-- Wrapper Tabel Responsif --}}
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs" id="table-materi">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase tracking-wider font-bold text-[11px]">
                            <th class="px-4 py-3">Materi</th>
                            <th class="px-4 py-3">Kelas &amp; Jurusan</th>
                            <th class="px-4 py-3 text-center">Tahun</th>
                            <th class="px-4 py-3">Diampu Oleh</th>
                            <th class="px-4 py-3 text-center">File</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($materis ?? $materi ?? [] as $item)
                            <tr class="hover:bg-slate-50/70 transition duration-75">

                                {{-- Judul & Deskripsi --}}
                                <td class="px-4 py-3 max-w-[220px]">
                                    <div class="font-bold text-slate-900 line-clamp-1" title="{{ $item->judul_materi ?? $item->judul }}">
                                        {{ $item->judul_materi ?? $item->judul }}
                                    </div>
                                    @if(!empty($item->isi_materi))
                                        <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5" title="{{ $item->isi_materi }}">
                                            {{ $item->isi_materi }}
                                        </p>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Tidak ada deskripsi</span>
                                    @endif
                                </td>

                                {{-- Kelas & Jurusan --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="space-y-1">
                                        @if($item->kelas)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#7B1038]/10 text-[#7B1038] border border-[#7B1038]/20">
                                                Kelas {{ str_replace(' ', '.', $item->kelas) }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">-</span>
                                        @endif
                                        <div class="text-[11px] text-slate-600 font-medium truncate max-w-[160px]" title="{{ $item->jurusan }}">
                                            {{ $item->jurusan ?? '-' }}
                                        </div>
                                    </div>
                                </td>

                                {{-- Tahun Ajaran --}}
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-semibold text-[11px] border border-slate-200">
                                        {{ $item->tahun ?? '-' }}
                                    </span>
                                </td>

                                {{-- Diampu Oleh --}}
                                <td class="px-4 py-3 whitespace-nowrap text-slate-700 font-medium">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-[10px] shrink-0">
                                            {{ strtoupper(substr($item->adminGuru->nama_lengkap ?? 'G', 0, 1)) }}
                                        </span>
                                        <span class="truncate max-w-[130px]" title="{{ $item->adminGuru->nama_lengkap ?? '-' }}">
                                            {{ $item->adminGuru->nama_lengkap ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- File Modul --}}
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if($item->upload_file || $item->file)
                                        <a href="{{ route('materi.show', $item) }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#7B1038]/10 text-[#7B1038] hover:bg-[#7B1038] hover:text-white font-bold text-[11px] transition duration-150 border border-[#7B1038]/20">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Buka File
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Kosong</span>
                                    @endif
                                </td>

                                {{-- Aksi Edit & Hapus --}}
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('materi.edit', $item) }}"
                                           title="Edit Materi"
                                           class="p-1.5 rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <form action="{{ route('materi.destroy', $item) }}"
                                              method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi «{{ addslashes($item->judul_materi ?? $item->judul) }}»? Semua kuis dan pengerjaan siswa terkait akan ikut terhapus.')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Hapus Materi"
                                                    class="p-1.5 rounded-lg text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition duration-150 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row">
                                <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <p class="font-semibold text-xs text-slate-500">Belum ada bahan ajar yang diunggah.</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Gunakan formulir di sebelah kiri untuk menambahkan modul baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Info Tambahan di Footer Tabel --}}
            <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                <span>Tips: Menghapus modul materi otomatis menghapus kuis dan riwayat pengerjaan siswa terkait.</span>
                <span id="match-count" class="font-medium text-slate-500"></span>
            </div>
        </div>
    </div>
</div>

{{-- Script Pencarian Realtime --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-materi');
        const tableBody = document.querySelector('#table-materi tbody');
        if (!searchInput || !tableBody) return;

        const rows = tableBody.querySelectorAll('tr:not(#empty-row)');
        const matchCount = document.getElementById('match-count');

        searchInput.addEventListener('input', function(e) {
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
    });
</script>
@endpush
@endsection
