@extends('layouts.app')

@section('title', 'Upload Sertifikat Pribadi — Portal Siswa')

@section('content')
<div class="w-full space-y-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-600">
        <a href="{{ route('portal.materi.index') }}" class="hover:text-[#7B1038] font-medium transition">Portal Siswa</a>
        <span>›</span>
        <a href="{{ route('siswa.sertifikat.index') }}" class="hover:text-[#7B1038] font-medium transition">Sertifikat</a>
        <span>›</span>
        <span class="text-[#1F2937] font-semibold">Upload Sertifikat Baru</span>
    </nav>

    <x-card class="overflow-hidden p-8">
        <div class="border-b border-gray-100 pb-5 mb-6">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide bg-[#7B1038] text-white mb-2 shadow-2xs">
                UNGGAH PORTOFOLIO
            </span>
            <h1 class="text-3xl font-bold text-[#1F2937] mb-1">Upload Sertifikat Pribadi</h1>
            <p class="text-base text-gray-600">Unggah dokumen sertifikat prestasi atau kegiatan mandiri Anda.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
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
            <div>
                <label for="judul_sertifikat" class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Sertifikat <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="judul_sertifikat"
                    id="judul_sertifikat"
                    value="{{ old('judul_sertifikat') }}"
                    placeholder="Contoh: Juara 1 Desain Poster Digital"
                    class="w-full px-4 py-3 rounded-xl text-base text-[#1F2937] bg-gray-50 border-0 placeholder-gray-500 focus:ring-2 focus:ring-[#7B1038] focus:outline-none transition shadow-xs"
                    required
                >
            </div>

            {{-- Dua Kolom: Penerbit & Tanggal Terbit --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="penerbit" class="block text-sm font-medium text-gray-700 mb-2">
                        Penerbit / Penyelenggara
                    </label>
                    <input
                        type="text"
                        name="penerbit"
                        id="penerbit"
                        value="{{ old('penerbit') }}"
                        placeholder="Contoh: Kemendikbudristek"
                        class="w-full px-4 py-3 rounded-xl text-base text-[#1F2937] bg-gray-50 border-0 placeholder-gray-500 focus:ring-2 focus:ring-[#7B1038] focus:outline-none transition shadow-xs"
                    >
                </div>

                <div>
                    <label for="tanggal_terbit" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Terbit
                    </label>
                    <input
                        type="date"
                        name="tanggal_terbit"
                        id="tanggal_terbit"
                        value="{{ old('tanggal_terbit') }}"
                        class="w-full px-4 py-3 rounded-xl text-base text-[#1F2937] bg-gray-50 border-0 placeholder-gray-500 focus:ring-2 focus:ring-[#7B1038] focus:outline-none transition shadow-xs"
                    >
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi / Catatan Tambahan
                </label>
                <textarea
                    name="deskripsi"
                    id="deskripsi"
                    rows="4"
                    placeholder="Keterangan singkat mengenai kompetensi atau capaian..."
                    class="w-full px-4 py-3 rounded-xl text-base text-[#1F2937] bg-gray-50 border-0 placeholder-gray-500 focus:ring-2 focus:ring-[#7B1038] focus:outline-none transition shadow-xs"
                >{{ old('deskripsi') }}</textarea>
            </div>

            {{-- File Input --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Berkas Sertifikat (PDF / JPG / PNG, Max 2MB) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <label for="file_sertifikat" class="flex items-center gap-3 w-full p-2 bg-gray-50 hover:bg-gray-100/70 border border-gray-200 rounded-xl cursor-pointer hover:border-[#7B1038] transition group shadow-xs">
                        <span class="inline-flex items-center px-5 py-2.5 bg-[#7B1038] group-hover:bg-[#5E0C2B] text-white text-base font-semibold rounded-xl transition duration-150 shadow-xs shrink-0 select-none">
                            Pilih File
                        </span>
                        <span id="file-chosen-text" class="text-sm text-gray-600 truncate font-medium flex-1">
                            Belum ada berkas dipilih
                        </span>
                    </label>
                    <input
                        type="file"
                        name="file_sertifikat"
                        id="file_sertifikat"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="sr-only"
                        onchange="const f = this.files[0]; const t = document.getElementById('file-chosen-text'); if(f) { t.innerHTML = '<span class=\'inline-flex items-center text-emerald-700 font-bold\'><span>' + f.name + '</span></span> <span class=\'text-gray-600 font-normal\'>(' + (f.size/1024/1024).toFixed(2) + ' MB)</span>'; } else { t.textContent = 'Belum ada berkas dipilih'; }"
                        required
                    >
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a
                    href="{{ route('siswa.sertifikat.index') }}"
                    class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-base font-semibold text-[#5E0C2B] transition"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-5 py-2.5 bg-[#7B1038] hover:bg-[#5E0C2B] text-white font-semibold rounded-xl text-base shadow-xs transition duration-150"
                >
                    Unggah Sertifikat
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
