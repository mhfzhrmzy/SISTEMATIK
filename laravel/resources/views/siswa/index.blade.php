@extends('layouts.app')

@section('title', 'Siswa')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 bg-white rounded-lg shadow p-6 h-fit">
        <h2 class="text-lg font-semibold mb-4">Tambah Siswa</h2>
        <form action="{{ route('siswa.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">NISN</label>
                <input type="text" name="nisn" value="{{ old('nisn') }}" maxlength="10" inputmode="numeric" pattern="[0-9]*"
                       placeholder="10 digit angka"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
                @error('nisn')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                       placeholder="Hanya huruf"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
                @error('nama_lengkap')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
                @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
                @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-md transition">
                Simpan Siswa
            </button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Daftar Siswa</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="px-3 py-2">NISN</th>
                        <th class="px-3 py-2">Nama Lengkap</th>
                        <th class="px-3 py-2">Email</th>
                        <th class="px-3 py-2">Poin</th>
                        <th class="px-3 py-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($siswas as $siswa)
                        <tr class="border-b">
                            <td class="px-3 py-2">{{ $siswa->nisn }}</td>
                            <td class="px-3 py-2">{{ $siswa->nama_lengkap }}</td>
                            <td class="px-3 py-2">{{ $siswa->email }}</td>
                            <td class="px-3 py-2">{{ $siswa->poin }}</td>
                            <td class="px-3 py-2 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('siswa.edit', $siswa) }}" class="text-amber-600 hover:underline">Edit</a>
                                    <form action="{{ route('siswa.destroy', $siswa) }}" method="POST"
                                          onsubmit="return confirm('Hapus siswa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-6 text-center text-gray-400">Belum ada data siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection