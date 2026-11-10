@extends('layouts.app')

@section('title', 'Materi')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 bg-white rounded-lg shadow p-6 h-fit">
        <h2 class="text-lg font-semibold mb-4">Bahan Ajar</h2>
        <form action="{{ route('materi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Judul Materi</label>
                <input type="text" name="judul_materi" value="{{ old('judul_materi') }}"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                @error('judul_materi')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Deskripsi Materi</label>
                <textarea name="isi_materi" rows="4"
                          class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('isi_materi') }}</textarea>
                @error('isi_materi')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Upload File</label>
                <input type="file" name="upload_file" class="w-full text-sm">
                @error('upload_file')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-md">
                Simpan Materi
            </button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Daftar Bahan Ajar</h2>
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="px-3 py-2">Judul</th>
                    <th class="px-3 py-2">Diampu oleh</th>
                    <th class="px-3 py-2">File</th>
                    <th class="px-3 py-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($materis as $materi)
                    <tr class="border-b align-top">
                        <td class="px-3 py-2">{{ $materi->judul_materi }}</td>
                        <td class="px-3 py-2">{{ $materi->adminGuru->nama_lengkap ?? '-' }}</td>
                        <td class="px-3 py-2">
                            @if ($materi->upload_file)
                                <a href="{{ Storage::url($materi->upload_file) }}" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
                            @else - @endif
                        </td>
                        <td class="px-3 py-2 text-right">
                            <form action="{{ route('materi.destroy', $materi) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-3 py-6 text-center text-gray-400">Belum ada materi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection