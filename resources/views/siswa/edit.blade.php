@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<div class="mx-auto max-w-xl rounded-lg bg-white p-6 shadow">
    <h2 class="mb-4 text-lg font-semibold">Edit Data Siswa</h2>

    <form action="{{ route('siswa.update', $siswa) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="mb-1 block text-sm font-medium">NISN</label>
            <input type="text" name="nisn" value="{{ old('nisn', $siswa->nisn) }}" maxlength="10" inputmode="numeric" pattern="[0-9]*" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            @error('nisn')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            @error('nama_lengkap')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email', $siswa->email) }}" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Password <span class="text-xs font-normal text-gray-500">(kosongkan jika tidak diubah)</span></label>
            <input type="password" name="password" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">Simpan Perubahan</button>
            <a href="{{ route('siswa.index') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-800 transition hover:bg-gray-300">Batal</a>
        </div>
    </form>
</div>
@endsection