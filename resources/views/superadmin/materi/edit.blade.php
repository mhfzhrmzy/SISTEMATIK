@extends('layouts.app')

@section('title', 'Edit Bahan Ajar — Superadmin Portal')

@section('content')
<div class="flex min-h-screen bg-slate-100 font-sans text-slate-800">
    <!-- LEFT SIDEBAR -->
    @include('superadmin.partials.sidebar', ['menu' => 'materi'])

    <!-- MAIN WORKSPACE -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- TOP HEADER BAR -->
        @include('superadmin.partials.header')

        <!-- PAGE CONTENT -->
        <main class="p-6 lg:p-8 space-y-6">
            <div class="max-w-3xl mx-auto bg-white p-6 lg:p-8 rounded-2xl border border-slate-200/70 shadow-sm">
                <div class="flex justify-between items-center pb-4 mb-6 border-b border-slate-100">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Edit Data Bahan Ajar</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi bahan ajar.</p>
                    </div>
                    <a href="{{ route('superadmin.materi.index') }}" class="text-xs font-semibold text-[#6D1837] hover:underline flex items-center gap-1">
                        &larr; Kembali
                    </a>
                </div>

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-xs font-semibold">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-xs">
                        <ul class="list-disc list-inside space-y-0.5 font-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('superadmin.materi.update', $materi) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-1.5">Guru Pengampu <span class="text-red-500">*</span></label>
                        <select name="nip" id="nip" class="w-full border rounded-xl px-3 py-2 text-sm text-slate-700 bg-white {{ $errors->has('nip') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}" required>
                            <option value="">-- Pilih Guru Pengampu --</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->nip }}" {{ old('nip', $materi->nip) == $admin->nip ? 'selected' : '' }}>
                                    {{ $admin->nama_lengkap }} ({{ $admin->nip }})
                                </option>
                            @endforeach
                        </select>
                        @error('nip')
                            <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-1.5">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <input type="number"
                               name="tahun"
                               id="tahun"
                               value="{{ old('tahun', $materi->tahun ?? date('Y')) }}"
                               min="2020"
                               class="w-full border rounded-xl px-3 py-2 text-sm text-slate-700 {{ $errors->has('tahun') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}"
                               required>
                        @error('tahun')
                            <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-1.5">Kelas <span class="text-red-500">*</span></label>
                        <select name="kelas" id="kelas" class="w-full border rounded-xl px-3 py-2 text-sm text-slate-700 {{ $errors->has('kelas') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach(['10 1', '10 2'] as $k)
                                <option value="{{ $k }}" {{ old('kelas', $materi->kelas) == $k ? 'selected' : '' }}>
                                    Kelas {{ str_replace(' ', '.', $k) }}
                                </option>
                            @endforeach
                        </select>
                        @error('kelas')
                            <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-1.5">Jurusan <span class="text-red-500">*</span></label>
                        <select name="jurusan" id="jurusan" class="w-full border rounded-xl px-3 py-2 text-sm text-slate-700 {{ $errors->has('jurusan') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}" required>
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
                            <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-1.5">Judul Materi <span class="text-red-500">*</span></label>
                        <input type="text"
                               name="judul_materi"
                               id="judul_materi"
                               value="{{ old('judul_materi', $materi->judul_materi) }}"
                               pattern="[a-zA-Z0-9 ]+"
                               oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '')"
                               placeholder="Contoh: Pemrograman Web Dasar 1"
                               class="w-full border rounded-xl px-3 py-2 text-sm text-slate-700 {{ $errors->has('judul_materi') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}"
                               required>
                        @error('judul_materi')
                            <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-1.5">Deskripsi Materi</label>
                        <textarea name="isi_materi" rows="4" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#6D1837]">{{ old('isi_materi', $materi->isi_materi) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-1.5">Ganti File Modul (PDF/PPT/Word)</label>
                        <input type="file" name="upload_file" accept=".pdf,.ppt,.pptx,.doc,.docx" class="w-full text-xs text-slate-500 border rounded-xl px-3 py-2 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#6D1837] file:text-white hover:file:bg-[#521128] border-slate-300 mb-2">

                        @if($materi->upload_file)
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 flex items-center justify-between flex-wrap gap-2">
                                <span>File Terpasang: <strong class="text-slate-800">{{ basename($materi->upload_file) }}</strong></span>
                                <a href="{{ route('superadmin.materi.show', $materi) }}" target="_blank" class="text-[#6D1837] hover:underline font-bold">
                                    📄 Lihat File
                                </a>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">* Kosongkan jika tidak ingin mengganti file yang ada.</p>
                        @endif
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-slate-100">
                        <button type="submit" class="bg-[#6D1837] hover:bg-[#521128] text-white font-bold py-2.5 px-6 rounded-xl text-sm transition shadow-sm">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('superadmin.materi.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2.5 px-6 rounded-xl text-sm transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection

