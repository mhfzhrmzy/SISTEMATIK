@extends('layouts.app')

@section('title', 'Hasil ' . ucfirst($tipe))

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-md mx-auto text-center">
    <h2 class="text-lg font-semibold mb-2">Hasil {{ ucfirst($tipe) }}</h2>
    <p class="text-sm text-gray-500 mb-4">{{ $materi->judul_materi }}</p>

    <p class="text-4xl font-bold text-blue-600 mb-2">{{ $skor }}</p>
    <p class="text-sm text-gray-600 mb-6">{{ $benar }} benar dari {{ $total }} soal</p>

    <a href="{{ route('portal.materi.show', $materi) }}"
       class="inline-block bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium px-5 py-2 rounded-md">
        Kembali ke Materi
    </a>
</div>
@endsection