@extends('layouts.app')

@section('title', 'Upload Sertifikat')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#7B1038]/10 text-[#7B1038]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Terbitkan Sertifikat untuk Siswa</h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-500">Unggah berkas dokumen sertifikat resmi yang akan didistribusikan ke seluruh siswa terpilih.</p>
        </div>

        <a href="{{ route('sertifikat.index') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 p-4 text-xs sm:text-sm shadow-2xs">
            <p class="font-bold mb-1">Terjadi kesalahan input:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Terbitkan Sertifikat --}}
    <div class="bg-slate-50/70 rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-2xs">
        <form action="{{ route('sertifikat.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- Daftar Siswa Penerima Terpilih --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700">
                        Siswa Penerima
                    </label>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#7B1038]/10 text-[#7B1038] border border-[#7B1038]/20">
                        {{ $siswaTerpilih->count() }} siswa terpilih
                    </span>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white overflow-hidden max-h-48 overflow-y-auto divide-y divide-slate-100 shadow-2xs">
                    @foreach ($siswaTerpilih as $s)
                        <div class="flex items-center gap-3 px-3.5 py-2.5">
                            <div class="w-7 h-7 rounded-lg bg-[#7B1038]/10 text-[#7B1038] flex items-center justify-center font-bold text-xs shrink-0 border border-[#7B1038]/20">
                                {{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}
                            </div>
                            <div class="truncate">
                                <p class="text-xs font-bold text-slate-900 leading-tight truncate">{{ $s->nama_lengkap }}</p>
                                <p class="text-[11px] text-slate-400">Kelas {{ str_replace(' ', '.', $s->kelas) }} — {{ $s->jurusan }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-1.5">
                    <a href="{{ route('sertifikat.index') }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#7B1038] hover:text-[#5E0C2B] transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Ubah daftar pilihan siswa
                    </a>
                </div>
            </div>

            {{-- Judul Sertifikat --}}
            <div>
                <label for="judul_sertifikat" class="block text-xs font-bold text-slate-700 mb-1">
                    Judul Sertifikat <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="judul_sertifikat"
                    id="judul_sertifikat"
                    value="{{ old('judul_sertifikat') }}"
                    placeholder="Contoh: Sertifikat Kelulusan Modul Pemrograman Web"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition"
                    required
                >
            </div>

            {{-- Grid Mini: Instansi Penerbit & Tanggal Input --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="penerbit" class="block text-xs font-bold text-slate-700 mb-1">
                        Instansi Penerbit
                    </label>
                    <input
                        type="text"
                        name="penerbit"
                        id="penerbit"
                        value="{{ old('penerbit', 'SMKN 2 Jember') }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Tanggal Input
                    </label>
                    <input
                        type="text"
                        value="{{ now()->translatedFormat('d F Y') }}"
                        readonly
                        disabled
                        class="w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3.5 py-2 text-xs font-semibold text-slate-500 cursor-not-allowed select-none"
                        title="Tanggal diisi otomatis oleh sistem"
                    >
                </div>
            </div>

            {{-- Deskripsi / Catatan --}}
            <div>
                <label for="deskripsi" class="block text-xs font-bold text-slate-700 mb-1">
                    Deskripsi / Catatan <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <textarea
                    name="deskripsi"
                    id="deskripsi"
                    rows="3"
                    placeholder="Catatan prestasi, kompetensi, atau rincian capaian pembelajaran siswa..."
                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition"
                >{{ old('deskripsi') }}</textarea>
            </div>

            {{-- Berkas File Sertifikat --}}
            <div>
                <label for="file_sertifikat" class="block text-xs font-bold text-slate-700 mb-1">
                    Berkas Sertifikat <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input
                        type="file"
                        name="file_sertifikat"
                        id="file_sertifikat"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#7B1038]/10 file:text-[#7B1038] hover:file:bg-[#7B1038]/20 file:transition file:cursor-pointer border border-slate-300 rounded-xl bg-white p-1.5 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20"
                        required
                    >
                </div>
                <p class="text-slate-400 text-[10px] mt-1">Format: PDF, JPG, JPEG, PNG (Maks. 2 MB) — berkas akan didistribusikan langsung ke portofolio seluruh siswa terpilih.</p>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                <a
                    href="{{ route('sertifikat.index') }}"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-200/80 hover:bg-slate-300 transition"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition duration-150 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    Terbitkan ke {{ $siswaTerpilih->count() }} Siswa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
