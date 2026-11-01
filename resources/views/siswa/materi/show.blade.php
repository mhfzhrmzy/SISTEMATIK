@extends('layouts.app')

@section('title', $materi->judul_materi . ' — Detail Modul')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-[#6B7280]">
        <a href="{{ route('portal.materi.index') }}" class="hover:text-[#7B1038] font-medium transition">Portal Siswa</a>
        <span>›</span>
        <span class="text-[#1F2937] font-semibold truncate">{{ $materi->judul_materi }}</span>
    </nav>

    {{-- Header Modul (Clean Maroon Border Accent) --}}
    <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border-l-4 border-[#7B1038] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#7B1038]/10 text-[#7B1038] mb-1.5">
                BAHAN AJAR &amp; MODUL
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-[#1F2937] leading-snug">
                {{ $materi->judul_materi }}
            </h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1 flex items-center gap-1.5">
                <span>Diampu oleh</span>
                <strong class="text-[#1F2937] font-semibold">{{ $materi->adminGuru->nama_lengkap ?? 'Guru Pengampu' }}</strong>
            </p>
        </div>

        <div class="shrink-0">
            <a href="{{ route('portal.quiz.index', $materi) }}"
               class="inline-flex items-center gap-2 bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-xl shadow-xs transition">
                📝 Evaluasi Quiz
            </a>
        </div>
    </div>

    {{-- Deskripsi Materi --}}
    <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs space-y-3">
        <h2 class="text-xs font-bold text-[#5E0C2B] uppercase tracking-wider">Deskripsi Pembelajaran</h2>
        <p class="text-xs sm:text-sm text-[#1F2937] leading-relaxed whitespace-pre-line">
            {{ $materi->isi_materi ?: 'Membahas pengenalan konsep dasar, penerapan langsung di bidang keahlian, latihan mandiri, dan evaluasi pemahaman standar industri.' }}
        </p>
    </div>

    {{-- Modul / File Materi --}}
    <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-xs font-bold text-[#5E0C2B] uppercase tracking-wider">Berkas Modul Pembelajaran</h2>
            @if ($materi->upload_file && $pretestCompleted)
                @php $fileUrl = route('portal.materi.file', $materi); @endphp
                <a href="{{ $fileUrl }}" target="_blank"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold bg-gray-100 hover:bg-[#7B1038]/10 text-[#7B1038] px-3.5 py-1.5 rounded-xl transition">
                    📄 Buka / Download Berkas
                </a>
            @endif
        </div>

        @if ($materi->upload_file)
            @if ($pretestCompleted)
                @php $fileUrl = route('portal.materi.file', $materi); @endphp
                <div class="rounded-2xl overflow-hidden bg-gray-50">
                    <iframe src="{{ $fileUrl }}"
                            class="w-full rounded-2xl"
                            style="height: 75vh;"
                            title="Preview {{ $materi->judul_materi }}">
                    </iframe>
                </div>
                <p class="text-[11px] text-[#6B7280]">
                    * Jika berkas tidak dapat dipratinjau langsung di browser Anda, silakan klik tombol Download di atas.
                </p>
            @else
                {{-- Siswa belum mengerjakan pre-test → tampilkan kartu terkunci (Clean & Minimalis) --}}
                <div class="bg-[#7B1038]/5 rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5 border-l-4 border-[#7B1038]">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🔒</span>
                            <h3 class="text-sm sm:text-base font-bold text-[#7B1038]">Berkas Materi Terkunci</h3>
                        </div>
                        <p class="text-xs sm:text-sm text-[#6B7280] leading-relaxed max-w-xl">
                            Anda perlu menyelesaikan <strong>Pre-Test</strong> terlebih dahulu untuk dapat mengakses berkas modul pembelajaran ini. Setelah menyelesaikan Pre-Test, modul akan terbuka secara otomatis.
                        </p>
                    </div>
                    <div class="shrink-0">
                        <a href="{{ route('portal.quiz.index', $materi) }}"
                           class="inline-flex items-center gap-2 bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs sm:text-sm font-semibold px-5 py-2.5 rounded-xl shadow-xs transition">
                            🧪 Kerjakan Pre-Test Sekarang →
                        </a>
                    </div>
                </div>
            @endif
        @else
            <div class="p-8 text-center bg-gray-50 rounded-2xl text-xs text-[#6B7280]">
                Belum ada berkas modul yang diunggah untuk materi ini.
            </div>
        @endif
    </div>

    {{-- Aksi Konfirmasi Penyelesaian Modul Belajar --}}
    @if ($pretestCompleted)
        <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-5 border-l-4 {{ ($materiSiswaRecord && $materiSiswaRecord->is_selesai) ? 'border-[#1F9254]' : 'border-[#7B1038]' }}">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    @if ($materiSiswaRecord && $materiSiswaRecord->is_selesai)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#E8F8F0] text-[#1F9254]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#1F9254]"></span>
                            SELESAI DIPELAJARI
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#7B1038]/10 text-[#7B1038]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#7B1038]"></span>
                            SEDANG DIPELAJARI
                        </span>
                    @endif
                </div>
                <h3 class="text-base sm:text-lg font-bold text-[#1F2937]">
                    @if ($materiSiswaRecord && $materiSiswaRecord->is_selesai)
                        Anda telah menyelesaikan modul pembelajaran ini
                    @else
                        Sudah selesai membaca materi ini?
                    @endif
                </h3>
                <p class="text-xs sm:text-sm text-[#6B7280]">
                    @if ($materiSiswaRecord && $materiSiswaRecord->is_selesai)
                        Modul telah ditandai selesai{{ $materiSiswaRecord->selesai_at ? ' pada ' . $materiSiswaRecord->selesai_at->translatedFormat('d F Y H:i') : '' }}. Status kemajuan belajar Anda pada modul ini sudah tercatat 100%.
                    @else
                        Tandai selesai setelah Anda memahami modul untuk memperbarui kemajuan belajar dan membuka evaluasi akhir.
                    @endif
                </p>
            </div>

            <form action="{{ route('portal.materi.selesai', $materi) }}" method="POST" class="shrink-0">
                @csrf
                @if ($materiSiswaRecord && $materiSiswaRecord->is_selesai)
                    <button type="submit"
                            title="Klik untuk membatalkan status selesai"
                            class="inline-flex items-center gap-2 bg-[#E8F8F0] hover:bg-rose-50 text-[#1F9254] hover:text-rose-600 border border-[#1F9254]/30 hover:border-rose-200 text-xs sm:text-sm font-semibold px-5 py-2.5 rounded-xl transition duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>✓ Selesai (Batal?)</span>
                    </button>
                @else
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs sm:text-sm font-semibold px-5 py-2.5 rounded-xl shadow-xs transition duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Tandai Selesai Membaca</span>
                    </button>
                @endif
            </form>
        </div>
    @endif

    {{-- Uji Pemahaman Quick Info --}}
    @php
        $pretestCount  = $materi->quiz->where('tipe_test', 'pretest')->count();
        $posttestCount = $materi->quiz->where('tipe_test', 'posttest')->count();
        $totalQuiz     = $materi->quiz->count();
    @endphp
    @if ($totalQuiz > 0)
        <div class="bg-white rounded-2xl p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <h3 class="text-xs font-bold text-[#5E0C2B] uppercase tracking-wider">Evaluasi &amp; Kuis Terkait</h3>
                <p class="text-xs text-[#6B7280]">
                    Modul ini memiliki {{ $pretestCount }} Pre-Test dan {{ $posttestCount }} Post-Test untuk mengukur pemahaman belajar Anda.
                </p>
            </div>
            <a href="{{ route('portal.quiz.index', $materi) }}"
               class="inline-flex items-center gap-2 bg-gray-100 hover:bg-[#7B1038]/10 text-[#7B1038] text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-xl transition shrink-0">
                Lihat Daftar Quiz →
            </a>
        </div>
    @endif

</div>
@endsection
