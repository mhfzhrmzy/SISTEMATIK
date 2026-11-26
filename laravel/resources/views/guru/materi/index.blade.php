@extends('layouts.app')

@section('title', 'Kelola Bahan Ajar')

@section('content')
<div class="space-y-6">

    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Kelola Bahan Ajar</h1>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200/90 shadow-2xs">
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
            <div class="border-b border-slate-200/70 pb-3 mb-4">
                <h2 class="text-sm font-bold text-slate-900">Tambah Bahan Ajar Baru</h2>
            </div>

            <form action="{{ route('materi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf

                {{-- Diampu Oleh (Guru) --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Diampu Oleh (Guru)</label>
                    <div class="relative">
                        <input type="text"
                               value="{{ auth('guru')->user()->nama_lengkap ?? 'Guru Terautentikasi' }}"
                               readonly
                               class="w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3.5 py-2 text-xs font-semibold text-slate-600 cursor-not-allowed">
                    </div>
                </div>

                {{-- Baris Mini Grid: Tahun Ajaran & Kelas --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                            @foreach(['X-1', 'X-2'] as $k)
                                <option value="{{ $k }}" {{ old('kelas') == $k ? 'selected' : '' }}>
                                    Kelas {{ $k }}
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
                        <option value="" disabled {{ old('jurusan') ? '' : 'selected' }}>Pilih Jurusan</option>
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
                               class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#7B1038] file:text-white hover:file:bg-[#5E0C2B] file:transition file:cursor-pointer border border-slate-300 rounded-xl bg-white p-1.5 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20">
                    </div>
                    <p class="text-slate-400 text-[10px] mt-1">Format: PDF, PPT, PPTX, DOC, DOCX (Maks. 25 MB)</p>
                    @error('upload_file')
                        <p class="text-rose-600 text-[11px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol Submit --}}
                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex items-center justify-center bg-[#7B1038] hover:bg-[#5E0C2B] text-white font-bold py-2.5 px-4 rounded-xl shadow-xs hover:shadow transition duration-150 cursor-pointer text-xs">
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
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Daftar Bahan Ajar</h2>
                </div>

                {{-- Input Pencarian Real-time --}}
                <div class="guru-search-box flex items-center gap-2 px-3 rounded-xl border border-slate-300 bg-white transition cursor-text" onclick="document.getElementById('search-materi').focus()">
                    <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           id="search-materi"
                           placeholder="Cari judul, kelas, jurusan..."
                           class="w-full text-[13px] bg-transparent border-0 p-0 placeholder-slate-400 focus:outline-none focus:ring-0 text-slate-700">
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
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#7B1038] text-white shadow-2xs">
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
                                            {{ strtoupper(substr($item->guru?->nama_lengkap ?? 'G', 0, 1)) }}
                                        </span>
                                        <span class="truncate max-w-[130px]" title="{{ $item->guru?->nama_lengkap ?? '-' }}">
                                            {{ $item->guru?->nama_lengkap ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- File Modul --}}
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if($item->upload_file || $item->file)
                                        @php
                                            $guruFileUrl = route('materi.show', $item);
                                            $guruFitUrl = $guruFileUrl . '#page=1&view=Fit&navpanes=0';
                                            $isItemPdf = str_ends_with(strtolower($item->upload_file ?? ''), '.pdf');
                                        @endphp
                                        @if($isItemPdf)
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button"
                                                        onclick="openMateriPreview('{{ $guruFitUrl }}', '{{ $guruFileUrl }}', @js($item->judul_materi ?? $item->judul))"
                                                        class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-[#7B1038] hover:bg-[#5E0C2B] text-white font-bold text-[11px] shadow-2xs transition duration-150 cursor-pointer"
                                                        title="Buka pratinjau pas 1 halaman">
                                                    Pratinjau
                                                </button>
                                                <a href="{{ $guruFitUrl }}"
                                                   target="_blank"
                                                   class="p-1 rounded-lg text-slate-500 hover:text-[#7B1038] hover:bg-slate-100 transition duration-150"
                                                   title="Buka di tab baru (Pas 1 Halaman)">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            </div>
                                        @else
                                            <a href="{{ $guruFileUrl }}"
                                               target="_blank"
                                               class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-[#7B1038] hover:bg-[#5E0C2B] text-white font-bold text-[11px] shadow-2xs transition duration-150">
                                                Unduh
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Kosong</span>
                                    @endif
                                </td>

                                {{-- Aksi Edit & Hapus --}}
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('materi.edit', $item) }}"
                                           title="Edit Materi"
                                           class="p-1.5 rounded-lg text-slate-600 bg-slate-100 hover:bg-slate-200 hover:text-slate-900 border border-slate-200 transition duration-150">
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
                                                    class="p-1.5 rounded-lg text-slate-500 bg-slate-100 hover:bg-rose-50 hover:text-rose-700 border border-slate-200 hover:border-rose-200 transition duration-150 cursor-pointer">
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

{{-- Modal Pratinjau PDF Materi Guru --}}
<div id="materi-preview-modal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5">
    <div id="materi-modal-card" class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-6xl h-[92vh] flex flex-col shadow-2xl overflow-hidden">
        {{-- Header Bar --}}
        <div class="bg-slate-800 text-slate-200 px-4 py-3 flex items-center justify-between flex-wrap gap-2 text-xs border-b border-slate-700 select-none">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-[#7B1038] text-white text-[10px] font-bold uppercase tracking-wide">
                    PRATINJAU GURU
                </span>
                <span id="modal-materi-title" class="font-bold text-slate-100 truncate max-w-[220px] sm:max-w-md">
                    Pratinjau Modul
                </span>
            </div>

        {{-- Mode & Action Controls --}}
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button"
                    id="modal-btn-landscape"
                    onclick="switchModalPdfMode('landscape')"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#7B1038] text-white transition shadow-2xs cursor-pointer"
                    title="Format Landscape / Slide Presentasi (16:9 Pas 1 Halaman)">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="2" stroke-width="2"/></svg>
                <span>Landscape</span>
            </button>
            <button type="button"
                    id="modal-btn-portrait"
                    onclick="switchModalPdfMode('portrait')"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-slate-300 transition cursor-pointer"
                    title="Format Portrait / Dokumen A4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="6" y="3" width="12" height="18" rx="2" stroke-width="2"/></svg>
                <span>Portrait</span>
            </button>
            <button type="button"
                    id="modal-btn-fit"
                    onclick="switchModalPdfMode('fit')"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-slate-300 transition cursor-pointer"
                    title="Pas Layar Penuh">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                <span>Pas Layar</span>
            </button>
            <span class="w-px h-4 bg-slate-700 mx-1"></span>
            <button type="button"
                    onclick="toggleModalFullscreen()"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-slate-200 transition cursor-pointer"
                    title="Layar Penuh">
                <span id="modal-text-fs">⛶ Layar Penuh</span>
            </button>
            <a id="modal-tab-link" href="#" target="_blank"
               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-slate-200 transition"
               title="Buka di Tab Baru">
                <span>↗ Tab Baru</span>
            </a>
            <button type="button"
                    onclick="closeMateriPreview()"
                    class="p-1 rounded-lg bg-slate-700 hover:bg-rose-600 text-slate-300 hover:text-white transition ml-1 cursor-pointer"
                    title="Tutup (Esc)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        </div>

        {{-- Iframe --}}
        <div class="relative w-full flex-1 bg-slate-900">
            <iframe id="modal-pdf-iframe"
                    src="about:blank"
                    class="w-full h-full border-0 block"
                    allow="fullscreen"
                    title="Pratinjau Modul">
            </iframe>
        </div>
    </div>
</div>

{{-- Script Pencarian Realtime & Preview --}}
@push('scripts')
<script>
    let currentModalBaseUrl = '';
    let currentModalMode = 'landscape';

    function applyModalCardDimensions() {
        const card = document.getElementById('materi-modal-card');
        if (!card) return;
        if (document.fullscreenElement) {
            card.style.maxWidth = '100vw';
            card.style.height = '100vh';
            return;
        }

        const availHeight = Math.max(480, window.innerHeight - 70);

        if (currentModalMode === 'landscape') {
            card.style.maxWidth = '1020px';
            const w = card.clientWidth || 980;
            const targetH = Math.round((w * 9 / 16) + 48);
            card.style.height = Math.max(480, Math.min(availHeight, targetH)) + 'px';
        } else if (currentModalMode === 'portrait') {
            card.style.maxWidth = '840px';
            card.style.height = Math.min(availHeight, 880) + 'px';
        } else {
            card.style.maxWidth = '1200px';
            card.style.height = availHeight + 'px';
        }
    }

    function openMateriPreview(fitUrl, rawUrl, title) {
        currentModalBaseUrl = rawUrl;
        document.getElementById('modal-materi-title').innerText = title;
        document.getElementById('modal-tab-link').href = fitUrl;
        document.getElementById('materi-preview-modal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        switchModalPdfMode('landscape');
    }

    function closeMateriPreview() {
        const modal = document.getElementById('materi-preview-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.getElementById('modal-pdf-iframe').src = 'about:blank';
        document.body.classList.remove('overflow-hidden');
        if (document.fullscreenElement) {
            document.exitFullscreen().catch(() => {});
        }
    }

    function switchModalPdfMode(mode) {
        currentModalMode = mode;
        const iframe = document.getElementById('modal-pdf-iframe');
        const btnLandscape = document.getElementById('modal-btn-landscape');
        const btnPortrait = document.getElementById('modal-btn-portrait');
        const btnFit = document.getElementById('modal-btn-fit');
        if (!iframe || !currentModalBaseUrl) return;

        [btnLandscape, btnPortrait, btnFit].forEach(btn => {
            if (!btn) return;
            btn.classList.remove('bg-[#7B1038]', 'text-white', 'shadow-2xs');
            btn.classList.add('bg-slate-700', 'text-slate-300', 'hover:bg-slate-600');
        });

        const activeBtn = mode === 'landscape' ? btnLandscape : (mode === 'portrait' ? btnPortrait : btnFit);
        if (activeBtn) {
            activeBtn.classList.remove('bg-slate-700', 'text-slate-300', 'hover:bg-slate-600');
            activeBtn.classList.add('bg-[#7B1038]', 'text-white', 'shadow-2xs');
        }

        applyModalCardDimensions();
        iframe.src = currentModalBaseUrl + '#page=1&view=Fit&navpanes=0';
    }

    function toggleModalFullscreen() {
        const card = document.getElementById('materi-modal-card');
        if (!card) return;
        if (!document.fullscreenElement) {
            if (card.requestFullscreen) card.requestFullscreen();
            else if (card.webkitRequestFullscreen) card.webkitRequestFullscreen();
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('materi-preview-modal');
            if (modal && !modal.classList.contains('hidden') && !document.fullscreenElement) {
                closeMateriPreview();
            }
        }
    });

    document.addEventListener('fullscreenchange', () => {
        const isFs = !!document.fullscreenElement;
        const textFs = document.getElementById('modal-text-fs');
        if (textFs) {
            textFs.innerText = isFs ? '✖ Keluar' : '⛶ Layar Penuh';
        }
        applyModalCardDimensions();
    });

    window.addEventListener('resize', () => {
        const modal = document.getElementById('materi-preview-modal');
        if (modal && !modal.classList.contains('hidden')) {
            applyModalCardDimensions();
        }
    });
        }
    });

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
