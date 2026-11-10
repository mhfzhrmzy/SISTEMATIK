@extends('layouts.app')
@section('title', 'Hasil Evaluasi — ' . ($quiz->materi->judul_materi ?? 'Quiz'))
@section('content')

@php
    $persen = $total > 0 ? round(($benar / $total) * 100) : 0;
    $grade = match(true) {
        $persen >= 90 => ['label' => 'Sempurna!',    'color' => 'text-[#1F9254]', 'bg' => 'bg-[#E8F8F0]'],
        $persen >= 75 => ['label' => 'Bagus Sekali!', 'color' => 'text-[#1F9254]', 'bg' => 'bg-[#E8F8F0]'],
        $persen >= 60 => ['label' => 'Cukup Baik',   'color' => 'text-amber-700', 'bg' => 'bg-amber-50'],
        default       => ['label' => 'Perlu Belajar Lebih Giat', 'color' => 'text-rose-700', 'bg' => 'bg-rose-50'],
    };
    $circumference = 2 * 3.14159 * 45;
    $dash = round($circumference * $persen / 100, 2);
@endphp

<div class="w-full max-w-3xl mx-auto py-4 space-y-4">

    {{-- Emoticon Perayaan --}}
    <div class="text-center text-3xl select-none" aria-hidden="true">
        @if ($persen >= 75) 🎉🎊🏆 @elseif ($persen >= 60) 👍✨ @else 💪📚 @endif
    </div>

    {{-- Main Result Card (Clean & Minimalis) --}}
    <div class="bg-white rounded-2xl shadow-xs overflow-hidden">

        {{-- Maroon Accent Header --}}
        <div class="bg-[#7B1038] px-6 pt-7 pb-9 text-center text-white">
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/20 text-white mb-2">
                HASIL {{ strtoupper($quiz->tipe_test) }}
            </span>
            <h1 class="text-lg sm:text-xl font-bold leading-snug">{{ $quiz->materi->judul_materi ?? 'Quiz' }}</h1>
            <p class="text-white/80 text-xs mt-1">Evaluasi Pengerjaan Selesai</p>
        </div>

        {{-- Score Ring (Pulls up from header) --}}
        <div class="-mt-8 flex justify-center mb-3">
            <div class="relative w-28 h-28 bg-white rounded-full shadow-md p-1.5 flex items-center justify-center">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="45" fill="none" stroke="#F3F4F6" stroke-width="8"/>
                    <circle cx="50" cy="50" r="45" fill="none"
                            stroke="{{ $persen >= 75 ? '#1F9254' : ($persen >= 60 ? '#D97706' : '#E11D48') }}"
                            stroke-width="8"
                            stroke-linecap="round"
                            stroke-dasharray="{{ $dash }} {{ $circumference }}"
                            style="transition: stroke-dasharray 1s ease;"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-black text-[#1F2937]">{{ $persen }}%</span>
                    <span class="text-[10px] text-[#6B7280] font-semibold uppercase tracking-wider">Skor</span>
                </div>
            </div>
        </div>

        <div class="px-6 pb-6 text-center space-y-5">

            {{-- Grade badge --}}
            <div>
                <span class="inline-flex items-center gap-1.5 {{ $grade['bg'] }} {{ $grade['color'] }} text-xs font-bold px-4 py-1.5 rounded-full">
                    {{ $grade['label'] }}
                </span>
            </div>

            {{-- Stats Grid (Benar, Salah, Total) --}}
            <div class="grid grid-cols-3 gap-2.5">
                <div class="bg-[#E8F8F0] rounded-xl p-3 text-center">
                    <div class="text-xl font-black text-[#1F9254]">{{ $benar }}</div>
                    <div class="text-[11px] text-[#1F9254] font-medium mt-0.5">Benar</div>
                </div>
                <div class="bg-rose-50 rounded-xl p-3 text-center">
                    <div class="text-xl font-black text-rose-600">{{ $total - $benar }}</div>
                    <div class="text-[11px] text-rose-700 font-medium mt-0.5">Salah</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                    <div class="text-xl font-black text-[#1F2937]">{{ $total }}</div>
                    <div class="text-[11px] text-[#6B7280] font-medium mt-0.5">Total Soal</div>
                </div>
            </div>

            {{-- Total Points Card --}}
            <div class="bg-[#7B1038]/5 rounded-xl p-4 text-center">
                <div class="text-[10px] font-bold text-[#7B1038] uppercase tracking-wider mb-1">Total Poin Masuk Leaderboard</div>
                <div class="text-3xl font-black text-[#7B1038]">
                    {{ $totalPoin }}
                    <span class="text-sm font-semibold text-[#6B7280]">Poin</span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="space-y-2 pt-1">
                <a href="{{ route('leaderboard.index') }}"
                   class="w-full flex items-center justify-center gap-2 bg-[#7B1038] hover:bg-[#5E0C2B] text-white font-semibold py-3 rounded-xl shadow-xs transition duration-150 text-sm">
                    🏆 Lihat Peringkat Leaderboard
                </a>
                <a href="{{ route('portal.quiz.index', $quiz->materi) }}"
                   class="w-full flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-[#5E0C2B] font-semibold py-2.5 rounded-xl transition duration-150 text-xs sm:text-sm">
                    📋 Kembali ke Daftar Quiz
                </a>
                <a href="{{ route('portal.materi.show', $quiz->materi) }}"
                   class="inline-block text-xs font-semibold text-[#6B7280] hover:text-[#7B1038] transition pt-1">
                    ← Buka Modul Bahan Ajar
                </a>
            </div>

        </div>

    </div>

</div>
@endsection
