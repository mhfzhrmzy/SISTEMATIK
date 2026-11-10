@extends('layouts.app')
@section('title', 'Kerjakan Quiz')
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold mb-4">{{ ucfirst($quiz->tipe_test) }}: {{ $quiz->materi->judul_materi }}</h2>
    <form action="{{ route('portal.quiz.submit', $quiz) }}" method="POST" class="space-y-5">
        @csrf
        @foreach ($quiz->soal as $i => $soal)
            <div class="border border-gray-200 rounded-md p-4 bg-gray-50">
                <p class="font-medium text-sm mb-3">{{ $i+1 }}. {{ $soal->pertanyaan }}</p>
                @foreach(['a','b','c','d'] as $opt)
                    <label class="flex items-center gap-2 text-sm mb-1">
                        <input type="radio" name="jawaban[{{ $soal->id_soal }}]" value="{{ $opt }}" required>
                        {{ strtoupper($opt) }}. {{ $soal->{'pilihan_'.$opt} }}
                    </label>
                @endforeach
            </div>
        @endforeach
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-md">Kumpulkan</button>
    </form>
</div>
@endsection