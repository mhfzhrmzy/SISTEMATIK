@extends('layouts.app')

@section('title', 'Edit Admin (Guru)')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold mb-4">Edit Data Admin (Guru)</h2>

    <form action="{{ route('admin.update', $admin) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">NIP</label>
            <input type="text" name="nip" value="{{ old('nip', $admin->nip) }}" maxlength="18" inputmode="numeric" pattern="[0-9]*"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
            @error('nip')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $admin->nama_lengkap) }}"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
            @error('nama_lengkap')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $admin->email) }}"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
            @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Password <span class="text-xs text-gray-500">(Kosongkan jika tidak ingin diubah)</span></label>
            <input type="password" name="password"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
            @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Foto Profil Saat Ini</label>
            @if ($admin->foto_profile)
                <div class="mb-2">
                    <img src="{{ Storage::url($admin->foto_profile) }}" class="w-16 h-16 rounded-full object-cover">
                </div>
            @else
                <p class="text-xs text-gray-400 mb-2">Belum ada foto profil.</p>
            @endif
            <input type="file" name="foto_profile" accept="image/*" class="w-full text-sm">
            @error('foto_profile')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium px-4 py-2 rounded-md transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection