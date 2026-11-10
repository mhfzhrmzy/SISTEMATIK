@extends('layouts.app')

@section('title', 'Admin (Guru)')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 bg-white rounded-lg shadow p-6 h-fit">
        <h2 class="text-lg font-semibold mb-4">Tambah Admin (Guru)</h2>
        <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">NIP</label>
                <input type="text" name="nip" value="{{ old('nip') }}" maxlength="18" inputmode="numeric" pattern="[0-9]*"
                       placeholder="18 digit angka"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
                @error('nip')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                       placeholder="Hanya huruf"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
                @error('nama_lengkap')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
                @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Foto Profil</label>
                <input type="file" name="foto_profile" accept="image/*" class="w-full text-sm">
                @error('foto_profile')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-md transition">
                Simpan Admin
            </button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Daftar Admin (Guru)</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="px-3 py-2">Foto</th>
                        <th class="px-3 py-2">NIP</th>
                        <th class="px-3 py-2">Nama Lengkap</th>
                        <th class="px-3 py-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr class="border-b">
                            <td class="px-3 py-2">
                                @if ($admin->foto_profile)
                                    <img src="{{ Storage::url($admin->foto_profile) }}"
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-200"></div>
                                @endif
                            </td>
                            <td class="px-3 py-2">{{ $admin->nip }}</td>
                            <td class="px-3 py-2">{{ $admin->nama_lengkap }}</td>
                            <td class="px-3 py-2 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.edit', $admin) }}" class="text-amber-600 hover:underline">Edit</a>
                                    <form action="{{ route('admin.destroy', $admin) }}" method="POST"
                                          onsubmit="return confirm('Hapus admin ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-6 text-center text-gray-400">Belum ada data admin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection