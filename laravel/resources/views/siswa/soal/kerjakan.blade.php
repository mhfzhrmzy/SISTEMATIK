@extends('layouts.app')
@section('title', ucfirst($tipe).' - '.$materi->judul)

@section('content')
    <a href="{{ route('siswa.materi.show', $materi) }}">&larr; Kembali ke materi</a>
    <h1>{{ ucfirst($tipe) }}: {{ $materi->judul }}</h1>

    @if($soals->isEmpty())
        <p>Belum ada soal {{ $tipe }} untuk materi ini.</p>
    @else
        <form action="{{ route('siswa.soal.submit', [$materi, $tipe]) }}" method="POST">
            @csrf

            @foreach($soals as $i => $soal)
                <div class="soal-card">
                    <h4>{{ $i + 1 }}. {{ $soal->pertanyaan }}</h4>
                    @foreach(['a','b','c','d'] as $opt)
                        <label style="font-weight:normal; display:block; margin-bottom:4px;">
                            <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $opt }}" required>
                            {{ strtoupper($opt) }}. {{ $soal->{'pilihan_'.$opt} }}
                        </label>
                    @endforeach
                </div>
            @endforeach

            <button type="submit" class="btn btn-success">Kumpulkan Jawaban</button>
        </form>
    @endif
@endsection
