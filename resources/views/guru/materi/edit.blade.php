@extends('layouts.app')

@section('title', 'Edit Bahan Ajar')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#7B1038]/10 text-[#7B1038]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Edit Data Bahan Ajar</h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-500">Perbarui rincian materi, kelas, jurusan, atau ganti file lampiran modul pembelajaran.</p>
        </div>

        <a href="{{ route('materi.index') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Notifikasi Error --}}
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
                Gagal memperbarui materi:
            </p>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Edit Card --}}
    <div class="bg-slate-50/70 rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-2xs">
        <form action="{{ route('materi.update', $materi) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Diampu Oleh (Guru) --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Diampu Oleh (Guru)</label>
                <input type="text"
                       value="{{ $materi->adminGuru->nama_lengkap ?? auth('admin')->user()->nama_lengkap }}"
                       readonly
                       class="w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3.5 py-2 text-xs font-semibold text-slate-600 cursor-not-allowed">
                <input type="hidden" name="nip" value="{{ $materi->nip ?? auth('admin')->id() }}">
            </div>

            {{-- Grid Mini: Tahun Ajaran & Kelas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Tahun Ajaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="number"
                           name="tahun"
                           id="tahun"
                           value="{{ old('tahun', $materi->tahun ?? date('Y')) }}"
                           min="2020"
                           required
                           class="w-full rounded-xl border {{ $errors->has('tahun') ? 'border-rose-400 bg-rose-50' : 'border-slate-300 bg-white' }} px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
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
                            class="w-full rounded-xl border {{ $errors->has('kelas') ? 'border-rose-400 bg-rose-50' : 'border-slate-300 bg-white' }} px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach(['10 1', '10 2'] as $k)
                            <option value="{{ $k }}" {{ old('kelas', $materi->kelas) == $k ? 'selected' : '' }}>
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
                    <option value="">-- Pilih Jurusan --</option>
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
                        <option value="{{ $j }}" {{ old('jurusan', $materi->jurusan) == $j ? 'selected' : '' }}>
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
                       value="{{ old('judul_materi', $materi->judul_materi) }}"
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
                          rows="4"
                          placeholder="Tuliskan ringkasan pokok bahasan..."
                          class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">{{ old('isi_materi', $materi->isi_materi) }}</textarea>
            </div>

            {{-- Ganti File Modul --}}
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">
                    File Modul Pembelajaran
                </label>

                @if($materi->upload_file)
                    <div class="p-3 bg-white border border-slate-200 rounded-xl text-xs flex items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-2 truncate">
                            <span class="w-8 h-8 rounded-lg bg-[#7B1038]/10 text-[#7B1038] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                            <div class="truncate">
                                <p class="text-[10px] text-slate-400 uppercase font-bold">File Saat Ini</p>
                                <p class="font-semibold text-slate-800 truncate" title="{{ basename($materi->upload_file) }}">
                                    {{ basename($materi->upload_file) }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('materi.show', $materi) }}"
                           target="_blank"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-[#7B1038]/10 text-[#7B1038] hover:bg-[#7B1038] hover:text-white font-bold text-xs transition duration-150 shrink-0">
                            Buka File
                        </a>
                    </div>
                @endif

                <div class="relative">
                    <input type="file"
                           name="upload_file"
                           accept=".pdf,.ppt,.pptx,.doc,.docx"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#7B1038]/10 file:text-[#7B1038] hover:file:bg-[#7B1038]/20 file:transition file:cursor-pointer border border-slate-300 rounded-xl bg-white p-1.5 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20">
                </div>
                <p class="text-slate-400 text-[10px]">
                    * Biarkan kosong jika tidak ingin mengganti file yang ada. Format: PDF, PPT, PPTX, DOC, DOCX (Maks. 25 MB).
                </p>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center gap-3 pt-3 border-t border-slate-200">
                <button type="submit"
                        class="flex items-center gap-2 bg-[#7B1038] hover:bg-[#5E0C2B] text-white font-bold py-2.5 px-5 rounded-xl shadow-xs hover:shadow transition duration-150 cursor-pointer text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('materi.index') }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-200/80 hover:bg-slate-300 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
