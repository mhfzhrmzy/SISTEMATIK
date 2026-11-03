@extends('layouts.app')

@section('title', 'Edit Materi')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Edit Data Materi</h2>
            <a href="{{ route('materi.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Daftar Bahan Ajar</a>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('materi.update', $materi) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Diampu Oleh (Guru)</label>
                <input type="text" value="{{ $materi->adminGuru->nama_lengkap ?? auth('admin')->user()->nama_lengkap }}" readonly class="w-full border rounded px-3 py-2 text-gray-600 bg-gray-100 cursor-not-allowed">
                <input type="hidden" name="nip" value="{{ $materi->nip ?? auth('admin')->id() }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tahun Ajaran <span class="text-red-500">*</span></label>
                <input type="number"
                       name="tahun"
                       id="tahun"
                       value="{{ old('tahun', $materi->tahun ?? date('Y')) }}"
                       min="2020"
                       class="w-full border rounded px-3 py-2 text-gray-700 {{ $errors->has('tahun') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                       required>
                @error('tahun')
                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Kelas <span class="text-red-500">*</span></label>
                <select name="kelas" id="kelas" class="w-full border rounded px-3 py-2 text-gray-700 {{ $errors->has('kelas') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach(['10 1', '10 2', '11 1', '11 2', '12 1', '12 2'] as $k)
                        <option value="{{ $k }}" {{ old('kelas', $materi->kelas) == $k ? 'selected' : '' }}>
                            Kelas {{ str_replace(' ', '.', $k) }}
                        </option>
                    @endforeach
                </select>
                @error('kelas')
                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Jurusan <span class="text-red-500">*</span></label>
                <select name="jurusan" id="jurusan" class="w-full border rounded px-3 py-2 text-gray-700 {{ $errors->has('jurusan') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}" required>
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

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Judul Materi <span class="text-red-500">*</span></label>
                <input type="text"
                       name="judul_materi"
                       id="judul_materi"
                       value="{{ old('judul_materi', $materi->judul_materi) }}"
                       pattern="[a-zA-Z0-9 ]+"
                       oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '')"
                       placeholder="Contoh: Pemrograman Web Dasar 1"
                       class="w-full border rounded px-3 py-2 text-gray-700 {{ $errors->has('judul_materi') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                       required>
                @error('judul_materi')
                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @else
                    <p class="text-gray-400 text-xs mt-1">Hanya huruf dan angka yang diperbolehkan.</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi Materi</label>
                <textarea name="isi_materi" rows="5" class="w-full border rounded px-3 py-2 text-gray-700">{{ old('isi_materi', $materi->isi_materi) }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Ganti File Modul (PDF/Word/PPT)</label>
                <input type="file" name="upload_file" class="w-full text-sm text-gray-500 mb-2">

                @if($materi->upload_file)
                    <div class="p-3 bg-gray-50 border rounded text-xs text-gray-600 flex items-center justify-between flex-wrap gap-2">
                        <span>File Terpasang: <strong>{{ basename($materi->upload_file) }}</strong></span>
                        <a href="{{ route('materi.show', $materi) }}" target="_blank" class="text-blue-600 hover:underline font-semibold">
                            📄 Lihat File Modul
                        </a>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">* Biarkan kosong jika tidak ingin mengganti file yang ada.</p>
                @endif
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Simpan Perubahan
                </button>
                <a href="{{ route('materi.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection