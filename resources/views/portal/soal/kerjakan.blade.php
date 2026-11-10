@extends('layouts.app')

@section('title', ucfirst($tipe).' - '.$materi->judul_materi)

@section('content')
<a href="{{ route('portal.materi.show', $materi) }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke materi</a>

<div class="bg-white rounded-lg shadow p-6 mt-4">
    <h2 class="text-lg font-semibold mb-4">{{ ucfirst($tipe) }}: {{ $materi->judul_materi }}</h2>

    @if ($soals->isEmpty())
        <p class="text-sm text-gray-400 italic">Belum ada soal {{ $tipe }} untuk materi ini.</p>
    @else
        <form action="{{ route('portal.soal.submit', [$materi, $tipe]) }}" method="POST" class="space-y-5">
            @csrf

            @foreach ($soals as $i => $soal)
                <div class="border border-gray-200 rounded-md p-4 bg-gray-50">
                    <p class="font-medium text-sm mb-3">{{ $i + 1 }}. {{ $soal->pertanyaan }}</p>
                    <div class="space-y-2">
                        @foreach (['a', 'b', 'c', 'd'] as $opt)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $opt }}" required>
                                <span>{{ strtoupper($opt) }}. {{ $soal->{'pilihan_' . $opt} }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-md">
                Kumpulkan Jawaban
            </button>
        </form>
    @endif
</div>
@endsection