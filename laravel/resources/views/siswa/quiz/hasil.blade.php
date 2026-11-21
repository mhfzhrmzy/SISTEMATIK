@extends('layouts.app')
@section('title', 'Hasil Evaluasi — ' . ($quiz->materi->judul_materi ?? 'Quiz'))

@push('styles')
<style>
    html, body {
        height: 100vh;
        max-height: 100vh;
        overflow: hidden;
    }
</style>
@endpush

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

<div class="w-full max-w-4xl mx-auto my-auto">

    {{-- Main Result Card (Clean & Minimalis) --}}
    <div class="bg-white rounded-2xl shadow-xs overflow-hidden">

        {{-- Maroon Accent Header --}}
        <div class="bg-[#7B1038] rounded-2xl px-6 sm:px-8 pt-6 sm:pt-7 pb-11 sm:pb-13 text-center text-white">
            <span class="inline-block px-3.5 py-1 rounded-full text-sm font-bold uppercase tracking-wider bg-white/20 text-white mb-2 sm:mb-2.5">
                HASIL {{ strtoupper($quiz->tipe_test) }}
            </span>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold leading-snug truncate">
                {{ $quiz->materi->judul_materi ?? 'Quiz' }}
            </h1>
        </div>

        {{-- Score Ring (Pulls up from header) --}}
        <div class="-mt-9 sm:-mt-10 flex justify-center mb-2.5 sm:mb-3">
            <div class="relative w-28 h-28 sm:w-32 sm:h-32 bg-white rounded-full shadow-md p-1.5 flex items-center justify-center">
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
                    <span class="text-xl sm:text-2xl font-black text-[#1F2937] leading-none mb-0.5">{{ $persen }}%</span>
                    <span class="text-sm text-gray-600 font-semibold uppercase tracking-wider">Skor</span>
                </div>
            </div>
        </div>

        <div class="px-6 sm:px-8 pb-6 sm:pb-7 text-center space-y-3.5 sm:space-y-4">

            {{-- Grade badge --}}
            <div>
                <span class="inline-flex items-center gap-1.5 {{ $grade['bg'] }} {{ $grade['color'] }} text-sm font-bold px-4 py-1.5 rounded-full">
                    {{ $grade['label'] }}
                </span>
            </div>

            {{-- Stats Grid (Benar, Salah, Total) --}}
            <div class="grid grid-cols-3 gap-2 sm:gap-4">
                <div class="bg-[#E8F8F0] rounded-2xl p-3 sm:p-3.5 text-center">
                    <div class="text-xl sm:text-2xl font-black text-[#1F9254]">{{ $benar }}</div>
                    <div class="text-sm text-[#1F9254] font-medium mt-0.5">Benar</div>
                </div>
                <div class="bg-rose-50 rounded-2xl p-3 sm:p-3.5 text-center">
                    <div class="text-xl sm:text-2xl font-black text-rose-600">{{ $total - $benar }}</div>
                    <div class="text-sm text-rose-700 font-medium mt-0.5">Salah</div>
                </div>
                <div class="bg-gray-50 rounded-2xl p-3 sm:p-3.5 text-center">
                    <div class="text-xl sm:text-2xl font-black text-[#1F2937]">{{ $total }}</div>
                    <div class="text-sm text-gray-600 font-medium mt-0.5">Total Soal</div>
                </div>
            </div>

            {{-- Total Points Card --}}
            <div class="bg-slate-50 border border-slate-200 rounded-2xl py-3 sm:py-3.5 px-4 text-center shadow-2xs">
                <div class="text-3xl sm:text-4xl font-semibold text-gray-600">
                    {{ $totalPoin }}
                    <span class="text-base font-semibold text-gray-600">Poin</span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="space-y-2 pt-1">
                @if ($quiz->tipe_test === 'posttest')
                <a href="{{ route('leaderboard.index') }}"
                   class="w-full flex items-center justify-center bg-[#7B1038] hover:bg-[#5E0C2B] text-white font-semibold py-3 rounded-xl shadow-xs transition duration-150 text-sm sm:text-base">
                    <span>Lihat Peringkat Leaderboard</span>
                </a>
                @endif
                <a href="{{ route('portal.quiz.index', $quiz->materi) }}"
                   class="w-full flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-[#5E0C2B] font-semibold py-2.5 sm:py-3 rounded-xl transition duration-150 text-sm">
                    <span>Kembali ke Daftar Quiz</span>
                </a>
                <a href="{{ route('portal.materi.show', $quiz->materi) }}"
                   class="w-full flex items-center justify-center bg-white hover:bg-gray-50 text-[#1F2937] border border-gray-300 font-semibold py-2.5 sm:py-3 rounded-xl transition duration-150 text-sm shadow-2xs">
                    <span>Buka Modul Bahan Ajar</span>
                </a>
            </div>

        </div>

    </div>

</div>
@endsection
