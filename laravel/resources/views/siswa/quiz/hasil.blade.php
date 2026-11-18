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

    {{-- Ikon Status Evaluasi --}}
    <div class="flex justify-center select-none" aria-hidden="true">
        <div class="w-12 h-12 rounded-2xl {{ $persen >= 75 ? 'bg-emerald-50 text-emerald-600' : ($persen >= 60 ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-500') }} flex items-center justify-center shadow-2xs">
            @if ($persen >= 75)
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.003 0V7.5a4.5 4.5 0 00-9 0v6.75m9 0h1.875a2.625 2.625 0 002.625-2.625v-.75a2.625 2.625 0 00-2.625-2.625h-.875M7.5 14.25H5.625A2.625 2.625 0 013 11.625v-.75A2.625 2.625 0 015.625 8.25H6.5" />
                </svg>
            @elseif ($persen >= 60)
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @else
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            @endif
        </div>
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
            <div class="bg-slate-50 border border-slate-200 rounded-2xl py-3.5 px-4 text-center shadow-2xs">
                <div class="text-3xl font-normal text-[#1F2937]">
                    {{ $totalPoin }}
                    <span class="text-sm font-normal text-[#6B7280]">Poin</span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="space-y-2 pt-1">
                @if ($quiz->tipe_test === 'posttest')
                <a href="{{ route('leaderboard.index') }}"
                   class="w-full flex items-center justify-center bg-[#7B1038] hover:bg-[#5E0C2B] text-white font-semibold py-3 rounded-xl shadow-xs transition duration-150 text-sm">
                    <span>Lihat Peringkat Leaderboard</span>
                </a>
                @endif
                <a href="{{ route('portal.quiz.index', $quiz->materi) }}"
                   class="w-full flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-[#5E0C2B] font-semibold py-2.5 rounded-xl transition duration-150 text-xs sm:text-sm">
                    <span>Kembali ke Daftar Quiz</span>
                </a>
                <a href="{{ route('portal.materi.show', $quiz->materi) }}"
                   class="inline-flex items-center text-xs font-semibold text-[#6B7280] hover:text-[#7B1038] transition pt-1">
                    <span>Buka Modul Bahan Ajar</span>
                </a>
            </div>

        </div>

    </div>

</div>
@endsection
