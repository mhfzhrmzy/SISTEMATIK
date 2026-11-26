@extends('layouts.app')

@section('title', 'Daftar Quiz — ' . $materi->judul_materi)

@section('content')
@php
    $isEmptyQuiz = $quizzes->isEmpty();
@endphp
@if ($isEmptyQuiz)
@push('styles')
<style>
    @media (min-width: 768px) {
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        body > main {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        body > main > div {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
    }
</style>
@endpush
@endif
<div class="portal-stack w-full {{ $isEmptyQuiz ? 'portal-empty-root' : '' }}">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-600">
        <a href="{{ route('portal.materi.index') }}" class="hover:text-[#7B1038] font-medium transition">Portal Siswa</a>
        <span>›</span>
        <a href="{{ route('portal.materi.show', $materi) }}" class="hover:text-[#7B1038] font-medium transition truncate max-w-[200px] sm:max-w-none">{{ $materi->judul_materi }}</a>
        <span>›</span>
        <span class="text-[#1F2937] font-semibold">Quiz Evaluasi</span>
    </nav>

    {{-- Tombol Kembali --}}
    <div>
        <a href="{{ route('portal.materi.index') }}"
           class="inline-flex items-center text-sm font-semibold text-[#5E0C2B] hover:text-[#7B1038] transition">
            Kembali
        </a>
    </div>

    {{-- Header Banner Materi --}}
    <x-card padding="" class="portal-card flex flex-col md:flex-row md:items-center justify-between gap-5">
        <div>
            <span class="portal-badge text-xs font-semibold uppercase tracking-wide bg-[#7B1038] text-white mb-2 shadow-2xs">
                MODUL EVALUASI
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#1F2937] leading-tight mb-1">
                {{ $materi->judul_materi }}
            </h1>
            <p class="text-base text-gray-600 flex items-center gap-1.5">
                <span>Diampu oleh</span>
                <strong class="text-[#1F2937] font-semibold">{{ $materi->guru?->nama_lengkap ?? 'Guru Pengampu' }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-6 shrink-0">
            <div class="text-center">
                <span class="text-xs sm:text-sm text-gray-500 uppercase tracking-wider block font-semibold">Tersedia</span>
                <span class="text-base font-bold text-[#1F2937]">{{ $quizzes->count() }} Quiz</span>
            </div>
            <div class="w-px h-8 bg-gray-200" aria-hidden="true"></div>
            <div class="text-center">
                <span class="text-xs sm:text-sm text-gray-500 uppercase tracking-wider block font-semibold">Selesai</span>
                <span class="text-base font-bold text-[#7B1038]">{{ $attemptedIds->count() }} Dikerjakan</span>
            </div>
        </div>
    </x-card>

    @if ($isEmptyQuiz)
        <x-card padding="" class="portal-card portal-empty-card text-center">
            <div class="flex flex-col items-center justify-center max-w-md mx-auto">
                <h3 class="text-lg font-bold text-[#1F2937] mb-1.5">Belum Ada Quiz</h3>
                <p class="text-sm sm:text-base text-gray-600">Guru belum menambahkan quiz untuk modul materi ini.</p>
                <a href="{{ route('portal.materi.index') }}"
                   class="inline-flex items-center justify-center mt-6 px-5 py-2.5 rounded-xl border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition">
                    Kembali ke Portal
                </a>
            </div>
        </x-card>
    @else
        @php
            $pretests  = $quizzes->where('tipe_test', 'pretest');
            $posttests = $quizzes->where('tipe_test', 'posttest');
        @endphp

        {{-- 1. Pre-Test Section --}}
        @if ($pretests->isNotEmpty())
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold uppercase tracking-wider text-[#7B1038]">Pre-Test</h2>
                </div>

                <div class="space-y-3">
                    @foreach ($pretests as $quiz)
                        @php
                            $attempted = $attemptedIds->has($quiz->id_quiz);
                            $poin      = $attemptedIds->get($quiz->id_quiz);
                            $statusJadwal = $quiz->statusJadwal();
                        @endphp
                        <x-card padding="" class="portal-card hover:shadow-md transition-all duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-2 flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="portal-badge text-xs font-semibold uppercase tracking-wide bg-[#7B1038] text-white shadow-2xs">Pre-Test</span>
                                    @if($statusJadwal === 'terjadwal')
                                        <span class="portal-badge text-xs font-semibold bg-gray-100 text-gray-600">
                                            Terjadwal
                                        </span>
                                    @elseif($statusJadwal === 'berlangsung')
                                        <span class="portal-badge text-xs font-semibold bg-[#E8F8F0] text-[#1F9254]">
                                            Sedang Berlangsung
                                        </span>
                                    @elseif($statusJadwal === 'selesai')
                                        <span class="portal-badge text-xs font-semibold bg-gray-100 text-gray-600">
                                            Jadwal Berakhir
                                        </span>
                                    @endif

                                    @if ($attempted)
                                        <span class="portal-badge text-xs font-semibold bg-[#E8F8F0] text-[#1F9254]">
                                            Sudah Dikerjakan
                                        </span>
                                    @elseif(!$statusJadwal)
                                        <span class="portal-badge text-xs font-semibold bg-gray-100 text-gray-600">
                                            Belum Dikerjakan
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base sm:text-lg font-bold text-[#1F2937]">
                                    Evaluasi Awal (Pre-Test)
                                </h3>

                                @if($quiz->jadwalLengkap())
                                    <p class="text-sm text-gray-600 flex items-center gap-1.5 flex-wrap">
                                        <span class="font-medium">Jadwal:</span>
                                        <strong>{{ $quiz->waktuMulai()->locale('id')->translatedFormat('l, d F Y') }}</strong>
                                        <span>|</span>
                                        <span>{{ $quiz->waktuMulai()->format('H:i') }} – {{ $quiz->waktuSelesai()->format('H:i') }} WIB</span>
                                    </p>
                                @endif

                                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                    <span>{{ $quiz->soal->count() }} Soal</span>
                                    <span class="text-slate-300 font-light">|</span>
                                    <span>100 Poin Total</span>
                                    <span class="text-slate-300 font-light">|</span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4 shrink-0 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke-width="1.75"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 7v5l3 3"/>
                                        </svg>
                                        <span>{{ $quiz->timer }} Menit</span>
                                    </span>
                                </div>
                            </div>

                            <div class="shrink-0 self-start sm:self-center">
                                @if ($quiz->soal->isEmpty())
                                    <span class="text-sm text-gray-600 italic">Soal belum siap</span>
                                @elseif ($attempted)
                                    <span class="portal-btn bg-[#E8F8F0] text-[#1F9254] cursor-not-allowed">
                                        Sudah Dikerjakan
                                    </span>
                                @elseif ($statusJadwal === 'terjadwal')
                                    <span class="portal-btn bg-gray-100 text-gray-600 cursor-not-allowed">
                                        Belum Dimulai
                                    </span>
                                @elseif ($statusJadwal === 'selesai')
                                    <span class="portal-btn bg-gray-100 text-gray-600 cursor-not-allowed">
                                        Waktu Berakhir
                                    </span>
                                @else
                                    <a href="{{ route('portal.quiz.kerjakan', $quiz) }}"
                                       class="btn-primary bg-[#7B1038] text-white shadow-xs">
                                        Mulai Pre-Test
                                    </a>
                                @endif
                            </div>
                        </x-card>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 2. Post-Test Section --}}
        @if ($posttests->isNotEmpty())
            <div class="space-y-3 pt-2">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold uppercase tracking-wider text-[#7B1038]">Post-Test</h2>
                </div>

                <div class="space-y-3">
                    @foreach ($posttests as $quiz)
                        @php
                            $attempted = $attemptedIds->has($quiz->id_quiz);
                            $poin      = $attemptedIds->get($quiz->id_quiz);
                            $statusJadwal = $quiz->statusJadwal();
                        @endphp
                        <x-card padding="" class="portal-card hover:shadow-md transition-all duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-2 flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="portal-badge text-xs font-semibold uppercase tracking-wide bg-[#7B1038] text-white shadow-2xs">Post-Test</span>
                                    @if($statusJadwal === 'terjadwal')
                                        <span class="portal-badge text-xs font-semibold bg-gray-100 text-gray-600">
                                            Terjadwal
                                        </span>
                                    @elseif($statusJadwal === 'berlangsung')
                                        <span class="portal-badge text-xs font-semibold bg-[#E8F8F0] text-[#1F9254]">
                                            Sedang Berlangsung
                                        </span>
                                    @elseif($statusJadwal === 'selesai')
                                        <span class="portal-badge text-xs font-semibold bg-gray-100 text-gray-600">
                                            Jadwal Berakhir
                                        </span>
                                    @endif

                                    @if ($attempted)
                                        <span class="portal-badge text-xs font-semibold bg-[#E8F8F0] text-[#1F9254]">
                                            Sudah Dikerjakan
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base sm:text-lg font-bold text-[#1F2937]">
                                    Evaluasi Akhir (Post-Test)
                                </h3>

                                @if($quiz->jadwalLengkap())
                                    <p class="text-sm text-gray-600 flex items-center gap-1.5 flex-wrap">
                                        <span class="font-medium">Jadwal:</span>
                                        <strong>{{ $quiz->waktuMulai()->locale('id')->translatedFormat('l, d F Y') }}</strong>
                                        <span>|</span>
                                        <span>{{ $quiz->waktuMulai()->format('H:i') }} – {{ $quiz->waktuSelesai()->format('H:i') }} WIB</span>
                                    </p>
                                @endif

                                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                    <span>{{ $quiz->soal->count() }} Soal</span>
                                    <span class="text-slate-300 font-light">|</span>
                                    <span>100 Poin Total</span>
                                    <span class="text-slate-300 font-light">|</span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4 shrink-0 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke-width="1.75"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 7v5l3 3"/>
                                        </svg>
                                        <span>{{ $quiz->timer }} Menit</span>
                                    </span>
                                </div>
                            </div>

                            <div class="shrink-0 self-start sm:self-center">
                                @if ($quiz->soal->isEmpty())
                                    <span class="text-sm text-gray-600 italic">Soal belum siap</span>
                                @elseif($statusJadwal === 'terjadwal')
                                    <span class="portal-btn bg-gray-100 text-gray-600 cursor-not-allowed">
                                        Belum Dimulai
                                    </span>
                                @elseif($statusJadwal === 'berlangsung')
                                    @if($attempted)
                                        <span class="portal-btn bg-[#E8F8F0] text-[#1F9254] cursor-not-allowed">
                                            Sudah Dikerjakan
                                        </span>
                                    @else
                                        <a href="{{ route('portal.quiz.kerjakan', $quiz) }}"
                                           class="btn-primary bg-[#7B1038] text-white shadow-xs">
                                            Mulai Post-Test
                                        </a>
                                    @endif
                                @elseif($statusJadwal === 'selesai')
                                    @if($attempted)
                                        <span class="portal-btn bg-[#E8F8F0] text-[#1F9254] cursor-not-allowed">
                                            Sudah Dikerjakan
                                        </span>
                                    @else
                                        <span class="portal-btn bg-gray-100 text-gray-600 cursor-not-allowed">
                                            Waktu Berakhir
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </x-card>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

</div>
@endsection
