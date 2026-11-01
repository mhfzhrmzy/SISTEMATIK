@extends('layouts.app')

@section('title', 'Upload Sertifikat Pribadi — Portal Siswa')

@section('content')
<div class="w-full space-y-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-[#6B7280]">
        <a href="{{ route('portal.materi.index') }}" class="hover:text-[#7B1038] font-medium transition">Portal Siswa</a>
        <span>›</span>
        <a href="{{ route('siswa.sertifikat.index') }}" class="hover:text-[#7B1038] font-medium transition">Sertifikat</a>
        <span>›</span>
        <span class="text-[#1F2937] font-semibold">Upload Sertifikat Baru</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-xs overflow-hidden p-6 sm:p-8">
        <div class="border-b border-gray-100 pb-5 mb-6">
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#7B1038]/10 text-[#7B1038] mb-1">
                UNGGAH PORTOFOLIO
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-[#1F2937]">Upload Sertifikat Pribadi</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Unggah dokumen sertifikat prestasi atau kegiatan mandiri Anda.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('siswa.sertifikat.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Judul Sertifikat --}}
            <div class="space-y-1.5">
                <label for="judul_sertifikat" class="block text-xs font-semibold text-[#6B7280]">
                    Judul Sertifikat <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="judul_sertifikat" 
                    id="judul_sertifikat" 
                    value="{{ old('judul_sertifikat') }}"
                    placeholder="Contoh: Juara 1 Desain Poster Digital" 
                    class="w-full px-4 py-2.5 rounded-xl text-sm text-[#1F2937] bg-gray-50 border-0 focus:ring-2 focus:ring-[#7B1038] focus:outline-none transition shadow-xs"
                    required
                >
            </div>

            {{-- Dua Kolom: Penerbit & Tanggal Terbit --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="penerbit" class="block text-xs font-semibold text-[#6B7280]">
                        Penerbit / Penyelenggara
                    </label>
                    <input 
                        type="text" 
                        name="penerbit" 
                        id="penerbit" 
                        value="{{ old('penerbit') }}"
                        placeholder="Contoh: Kemendikbudristek" 
                        class="w-full px-4 py-2.5 rounded-xl text-sm text-[#1F2937] bg-gray-50 border-0 focus:ring-2 focus:ring-[#7B1038] focus:outline-none transition shadow-xs"
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="tanggal_terbit" class="block text-xs font-semibold text-[#6B7280]">
                        Tanggal Terbit
                    </label>
                    <input 
                        type="date" 
                        name="tanggal_terbit" 
                        id="tanggal_terbit" 
                        value="{{ old('tanggal_terbit') }}"
                        class="w-full px-4 py-2.5 rounded-xl text-sm text-[#1F2937] bg-gray-50 border-0 focus:ring-2 focus:ring-[#7B1038] focus:outline-none transition shadow-xs"
                    >
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="space-y-1.5">
                <label for="deskripsi" class="block text-xs font-semibold text-[#6B7280]">
                    Deskripsi / Catatan Tambahan
                </label>
                <textarea 
                    name="deskripsi" 
                    id="deskripsi" 
                    rows="3" 
                    placeholder="Keterangan singkat mengenai kompetensi atau capaian..." 
                    class="w-full px-4 py-2.5 rounded-xl text-sm text-[#1F2937] bg-gray-50 border-0 focus:ring-2 focus:ring-[#7B1038] focus:outline-none transition shadow-xs"
                >{{ old('deskripsi') }}</textarea>
            </div>

            {{-- File Input --}}
            <div class="space-y-1.5">
                <label for="file_sertifikat" class="block text-xs font-semibold text-[#6B7280]">
                    Berkas Sertifikat (PDF / JPG / PNG, Max 2MB) <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="file" 
                    name="file_sertifikat" 
                    id="file_sertifikat" 
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full text-xs text-[#6B7280] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#7B1038]/10 file:text-[#7B1038] hover:file:bg-[#7B1038]/20 bg-gray-50 rounded-xl p-2 cursor-pointer shadow-xs transition"
                    required
                >
            </div>

            {{-- Tombol Aksi --}}
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a 
                    href="{{ route('siswa.sertifikat.index') }}" 
                    class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-xs sm:text-sm font-semibold text-[#5E0C2B] transition"
                >
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 bg-[#7B1038] hover:bg-[#5E0C2B] text-white font-semibold rounded-xl text-xs sm:text-sm shadow-xs transition duration-150"
                >
                    Unggah Sertifikat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
