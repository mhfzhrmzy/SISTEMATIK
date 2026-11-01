@extends('layouts.app')

@section('title', 'Daftar Quiz — ' . $materi->judul_materi)

@section('content')
<div class="w-full space-y-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-[#6B7280]">
        <a href="{{ route('portal.materi.index') }}" class="hover:text-[#7B1038] font-medium transition">Portal Siswa</a>
        <span>›</span>
        <a href="{{ route('portal.materi.show', $materi) }}" class="hover:text-[#7B1038] font-medium transition truncate max-w-[200px] sm:max-w-none">{{ $materi->judul_materi }}</a>
        <span>›</span>
        <span class="text-[#1F2937] font-semibold">Quiz Evaluasi</span>
    </nav>

    {{-- Header Banner Materi (Maroon & Putih, Clean, Minimalis) --}}
    <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border-l-4 border-[#7B1038] flex flex-col md:flex-row md:items-center justify-between gap-5">
        <div class="space-y-1.5">
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#7B1038]/10 text-[#7B1038]">
                MODUL EVALUASI
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-[#1F2937] leading-tight">
                {{ $materi->judul_materi }}
            </h1>
            <p class="text-xs sm:text-sm text-[#6B7280] flex items-center gap-1.5">
                <span>Diampu oleh</span>
                <strong class="text-[#1F2937] font-semibold">{{ $materi->adminGuru->nama_lengkap ?? 'Guru Pengampu' }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <div class="bg-gray-50 rounded-xl px-4 py-2.5 text-center">
                <span class="text-[10px] text-[#6B7280] uppercase tracking-wider block font-semibold">Tersedia</span>
                <span class="text-base font-bold text-[#1F2937]">{{ $quizzes->count() }} Quiz</span>
            </div>
            <div class="bg-[#7B1038]/10 rounded-xl px-4 py-2.5 text-center">
                <span class="text-[10px] text-[#7B1038] uppercase tracking-wider block font-semibold">Selesai</span>
                <span class="text-base font-bold text-[#7B1038]">{{ $attemptedIds->count() }} Dikerjakan</span>
            </div>
        </div>
    </div>

    {{-- Error / Session Alert --}}
    @if(session('errors'))
        <div class="bg-rose-50 rounded-2xl p-4 text-rose-700 text-xs sm:text-sm shadow-xs space-y-1">
            @foreach($errors->all() as $error)
                <p class="flex items-center gap-1.5">
                    <span>⚠️</span>
                    <span>{{ $error }}</span>
                </p>
            @endforeach
        </div>
    @endif

    @if ($quizzes->isEmpty())
        <div class="bg-white rounded-2xl shadow-xs p-12 text-center">
            <div class="text-4xl mb-3">📭</div>
            <h3 class="text-base font-bold text-[#1F2937] mb-1">Belum Ada Quiz</h3>
            <p class="text-xs text-[#6B7280]">Guru belum menambahkan quiz untuk modul materi ini.</p>
            <a href="{{ route('portal.materi.show', $materi) }}"
               class="inline-block mt-4 text-xs font-semibold text-[#7B1038] hover:underline">
                ← Kembali ke Materi
            </a>
        </div>
    @else
        @php
            $pretests  = $quizzes->where('tipe_test', 'pretest');
            $posttests = $quizzes->where('tipe_test', 'posttest');
        @endphp

        {{-- 1. Pre-Test Section --}}
        @if ($pretests->isNotEmpty())
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🧪</span>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-[#5E0C2B]">Pre-Test</h2>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#7B1038]/10 text-[#7B1038]">
                        {{ $pretests->count() }} Pre-Test
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach ($pretests as $quiz)
                        @php
                            $attempted = $attemptedIds->has($quiz->id_quiz);
                            $poin      = $attemptedIds->get($quiz->id_quiz);
                        @endphp
                        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-2 flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#7B1038]/10 text-[#7B1038]">Pre-Test</span>
                                    @if ($attempted)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#E8F8F0] text-[#1F9254]">
                                            ✓ Sudah Dikerjakan
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-[#6B7280]">
                                            Belum Dikerjakan
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base font-bold text-[#1F2937]">
                                    Evaluasi Awal (Pre-Test)
                                </h3>

                                <div class="flex flex-wrap items-center gap-3 text-xs text-[#6B7280]">
                                    <span class="flex items-center gap-1">📝 {{ $quiz->soal->count() }} Soal</span>
                                    <span>•</span>
                                    <span>⭐ 100 Poin Total</span>
                                    <span>•</span>
                                    <span>⏱ {{ $quiz->timer }} Menit</span>
                                    @if($quiz->soal->count() > 0)
                                        <span>•</span>
                                        <span>🎯 ±{{ round(100 / $quiz->soal->count(), 2) }} Poin/Soal</span>
                                    @endif
                                </div>

                                @if ($attempted)
                                    <div class="inline-flex items-center gap-1.5 bg-[#E8F8F0] text-[#1F9254] text-xs font-bold px-3 py-1.5 rounded-xl">
                                        🏆 Skor Anda: {{ $poin }} / 100 Poin
                                    </div>
                                @endif
                            </div>

                            <div class="shrink-0 self-start sm:self-center">
                                @if ($quiz->soal->isEmpty())
                                    <span class="text-xs text-gray-400 italic">Soal belum siap</span>
                                @else
                                    <a href="{{ route('portal.quiz.kerjakan', $quiz) }}"
                                       class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-xs {{ $attempted ? 'bg-gray-100 hover:bg-[#7B1038]/10 text-[#7B1038]' : 'bg-[#7B1038] hover:bg-[#5E0C2B] text-white' }}">
                                        {{ $attempted ? '🔄 Kerjakan Ulang' : '▶ Mulai Pre-Test' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 2. Post-Test Section --}}
        @if ($posttests->isNotEmpty())
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🎓</span>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-[#5E0C2B]">Post-Test</h2>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#7B1038]/10 text-[#7B1038]">
                        {{ $posttests->count() }} Post-Test
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach ($posttests as $quiz)
                        @php
                            $attempted = $attemptedIds->has($quiz->id_quiz);
                            $poin      = $attemptedIds->get($quiz->id_quiz);
                            $statusJadwal = $quiz->statusJadwal();
                        @endphp
                        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-2 flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#7B1038]/10 text-[#7B1038]">Post-Test</span>
                                    @if($statusJadwal === 'terjadwal')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-[#6B7280]">
                                            ⏰ Terjadwal
                                        </span>
                                    @elseif($statusJadwal === 'berlangsung')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#E8F8F0] text-[#1F9254]">
                                            • Sedang Berlangsung
                                        </span>
                                    @elseif($statusJadwal === 'selesai')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                            Selesai
                                        </span>
                                    @endif

                                    @if ($attempted)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#E8F8F0] text-[#1F9254]">
                                            ✓ Sudah Dikerjakan
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base font-bold text-[#1F2937]">
                                    Evaluasi Akhir (Post-Test)
                                </h3>

                                @if($quiz->jadwalLengkap())
                                    <p class="text-xs text-[#6B7280] flex items-center gap-1.5">
                                        <span>📅 Jadwal:</span>
                                        <strong>{{ $quiz->waktuMulai()->translatedFormat('l, d M Y') }}</strong>
                                        <span>·</span>
                                        <span>{{ $quiz->waktuMulai()->format('H:i') }} – {{ $quiz->waktuSelesai()->format('H:i') }} WIB</span>
                                    </p>
                                @endif

                                <div class="flex flex-wrap items-center gap-3 text-xs text-[#6B7280]">
                                    <span class="flex items-center gap-1">📝 {{ $quiz->soal->count() }} Soal</span>
                                    <span>•</span>
                                    <span>⭐ 100 Poin Total</span>
                                    <span>•</span>
                                    <span>⏱ {{ $quiz->timer }} Menit</span>
                                    @if($quiz->soal->count() > 0)
                                        <span>•</span>
                                        <span>🎯 ±{{ round(100 / $quiz->soal->count(), 2) }} Poin/Soal</span>
                                    @endif
                                </div>

                                @if ($attempted)
                                    <div class="inline-flex items-center gap-1.5 bg-[#E8F8F0] text-[#1F9254] text-xs font-bold px-3 py-1.5 rounded-xl">
                                        🏆 Skor Anda: {{ $poin }} / 100 Poin
                                    </div>
                                @endif
                            </div>

                            <div class="shrink-0 self-start sm:self-center">
                                @if ($quiz->soal->isEmpty())
                                    <span class="text-xs text-gray-400 italic">Soal belum siap</span>
                                @elseif($statusJadwal === 'terjadwal')
                                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 text-xs font-semibold px-4 py-2.5 rounded-xl cursor-not-allowed">
                                        🔒 Belum Dimulai
                                    </span>
                                @elseif($statusJadwal === 'berlangsung')
                                    <a href="{{ route('portal.quiz.kerjakan', $quiz) }}"
                                       class="inline-flex items-center gap-1.5 bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-xs transition">
                                        ▶ Mulai Post-Test
                                    </a>
                                @elseif($statusJadwal === 'selesai')
                                    @if($attempted)
                                        <span class="inline-flex items-center gap-1.5 bg-[#E8F8F0] text-[#1F9254] text-xs font-semibold px-4 py-2.5 rounded-xl">
                                            ✓ Telah Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-400 text-xs font-semibold px-4 py-2.5 rounded-xl cursor-not-allowed">
                                            ⏰ Waktu Berakhir
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    {{-- Link Kembali --}}
    <div class="pt-2">
        <a href="{{ route('portal.materi.show', $materi) }}"
           class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[#5E0C2B] hover:text-[#7B1038] transition">
            ← Kembali ke Modul Materi
        </a>
    </div>

</div>
@endsection
