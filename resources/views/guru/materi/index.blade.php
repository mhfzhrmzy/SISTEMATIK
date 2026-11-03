@extends('layouts.app')

@section('title', 'Daftar Bahan Ajar')

@section('content')
<div class="container mx-auto p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Form Bahan Ajar -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">Bahan Ajar</h2>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded mb-4">
                    <p class="font-semibold text-sm mb-1">Gagal menyimpan materi:</p>
                    <ul class="list-disc list-inside text-sm space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('materi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Diampu Oleh (Guru)</label>
                    <input type="text" value="{{ auth('admin')->user()->nama_lengkap ?? 'Guru Terautentikasi' }}" readonly class="w-full border rounded px-3 py-2 text-gray-600 bg-gray-100 cursor-not-allowed">
                    <input type="hidden" name="nip" value="{{ auth('admin')->id() }}">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tahun Ajaran <span class="text-red-500">*</span></label>
                    <input type="number"
                           name="tahun"
                           id="tahun"
                           value="{{ old('tahun', date('Y')) }}"
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
                            <option value="{{ $k }}" {{ old('kelas') == $k ? 'selected' : '' }}>
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
                            <option value="{{ $j }}" {{ old('jurusan') == $j ? 'selected' : '' }}>
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
                           value="{{ old('judul_materi') }}"
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
                    <textarea name="isi_materi" rows="4" class="w-full border rounded px-3 py-2 text-gray-700">{{ old('isi_materi') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Upload File Modul
                        <span class="text-red-500 ml-0.5">*</span>
                        <span class="text-gray-400 font-normal text-xs ml-1">(PDF, PPT, PPTX, DOC, DOCX — maks. 25 MB)</span>
                    </label>
                    <input type="file" name="upload_file" accept=".pdf,.ppt,.pptx,.doc,.docx"
                           class="w-full text-sm text-gray-500 border rounded px-2 py-1.5 {{ $errors->has('upload_file') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    @error('upload_file')
                        <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full">
                    Simpan Materi
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Bahan Ajar -->
        <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">Daftar Bahan Ajar</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100 border-b">
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Judul</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Deskripsi</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Diampu Oleh</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Kelas</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Tahun</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Jurusan</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">File</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materis ?? $materi ?? [] as $item)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4 font-semibold text-gray-800">{{ $item->judul_materi ?? $item->judul }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600 max-w-xs">
                                    @if(!empty($item->isi_materi))
                                        <div class="line-clamp-2" title="{{ $item->isi_materi }}">
                                            {{ $item->isi_materi }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic text-xs">Tidak ada deskripsi</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">{{ $item->adminGuru->nama_lengkap ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    @if($item->kelas)
                                        <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded">
                                            Kelas {{ $item->kelas }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm">{{ $item->tahun ?? '-' }}</td>
                                <td class="py-3 px-4 text-sm">{{ $item->jurusan ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    @if($item->upload_file || $item->file)
                                        <a href="{{ route('materi.show', $item) }}" target="_blank" class="text-blue-600 hover:underline font-semibold">
                                            Lihat File
                                        </a>
                                    @else
                                        <span class="text-gray-400">Tidak ada file</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 flex items-center gap-3">
                                    {{-- Tombol Edit Materi --}}
                                    <a href="{{ route('materi.edit', $item) }}" class="text-amber-600 hover:text-amber-700 font-semibold text-sm">
                                        Edit
                                    </a>

                                    {{-- Form Hapus Materi --}}
                                    <form action="{{ route('materi.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 font-semibold text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-gray-500">Belum ada data materi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection