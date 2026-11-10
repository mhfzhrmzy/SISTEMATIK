@extends('layouts.app')

@section('title', 'Daftar Siswa')

@section('content')
<div>
    <h1 class="text-xl font-semibold">Daftar Siswa Terdaftar</h1>
    <p class="text-sm text-gray-500 mt-1 mb-5">Daftar akun siswa yang sudah tersimpan di sistem.</p>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead><tr class="border-b bg-gray-50">
                <th class="px-3 py-2">No</th><th class="px-3 py-2">NISN</th>
                <th class="px-3 py-2">Nama</th><th class="px-3 py-2">Email</th><th class="px-3 py-2">Terdaftar</th>
            </tr></thead>
            <tbody>
            @forelse($siswas as $siswa)
                <tr class="border-b">
                    <td class="px-3 py-2">{{ $loop->iteration }}</td>
                    <td class="px-3 py-2">{{ $siswa->nisn }}</td>
                    <td class="px-3 py-2">{{ $siswa->nama_lengkap }}</td>
                    <td class="px-3 py-2">{{ $siswa->email }}</td>
                    <td class="px-3 py-2">{{ $siswa->created_at?->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-3 py-6 text-center text-gray-400">Belum ada akun siswa.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
