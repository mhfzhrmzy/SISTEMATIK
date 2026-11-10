@extends('layouts.app')
@section('title', 'Hasil '.ucfirst($tipe))

@section('content')
    <h1>Hasil {{ ucfirst($tipe) }}: {{ $materi->judul }}</h1>

    <p style="font-size:18px;">
        Skor kamu: <strong>{{ $skor }}</strong> ({{ $benar }} benar dari {{ $total }} soal)
    </p>

    <a href="{{ route('siswa.materi.show', $materi) }}" class="btn btn-secondary">Kembali ke Materi</a>
@endsection
