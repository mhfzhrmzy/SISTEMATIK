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
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#7B1038] text-white mb-1.5 shadow-2xs">
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
        @if ($materi->upload_file)
            @if ($pretestCompleted)
                @php
                    $fileBaseUrl = route('portal.materi.file', $materi);
                    $pdfFitPageUrl = $fileBaseUrl . '#page=1&view=Fit&navpanes=0';
                    $isPdf = str_ends_with(strtolower($materi->upload_file), '.pdf');
                @endphp

                <div class="flex items-center justify-between flex-wrap gap-3 pb-1">
                    <div>
                        <h2 class="text-xs font-bold text-[#5E0C2B] uppercase tracking-wider">Berkas Modul Pembelajaran</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tampilan disetel otomatis <strong>Pas 1 Halaman</strong> agar seluruh isi terbaca langsung tanpa scroll ganda.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ $pdfFitPageUrl }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold bg-slate-100 hover:bg-[#7B1038] hover:text-white text-[#7B1038] border border-slate-200 px-3.5 py-2 rounded-xl transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Buka di Tab Baru</span>
                        </a>
                        <a href="{{ $fileBaseUrl }}" download
                           class="inline-flex items-center gap-1.5 text-xs font-semibold bg-[#7B1038] hover:bg-[#5E0C2B] text-white px-3.5 py-2 rounded-xl transition shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Berkas</span>
                        </a>
                    </div>
                </div>

                @if ($isPdf)
                    {{-- Interactive PDF Reader Container --}}
                    <div id="pdf-reader-container" class="rounded-2xl overflow-hidden bg-slate-900 border border-slate-700/80 shadow-md flex flex-col transition-all duration-150">
                        {{-- Reader Top Toolbar --}}
                        <div class="bg-slate-800 text-slate-200 px-4 py-2.5 flex items-center justify-between flex-wrap gap-2 text-xs border-b border-slate-700 select-none">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-[#7B1038] text-white text-[10px] font-bold tracking-wide uppercase">
                                    PDF MODUL
                                </span>
                                <span class="font-medium text-slate-200 truncate max-w-[200px] sm:max-w-xs" title="{{ $materi->judul_materi }}">
                                    {{ $materi->judul_materi }}
                                </span>
                            </div>

                            {{-- Mode Controls --}}
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[11px] text-slate-400 font-medium mr-1 hidden sm:inline">Mode:</span>
                                <button type="button"
                                        id="btn-mode-fit"
                                        onclick="switchPdfMode('fit')"
                                        class="pdf-mode-btn inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold transition bg-[#7B1038] text-white shadow-2xs"
                                        title="Satu Halaman Penuh Pas Layar (Tidak perlu scroll isi slide)">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
                                    <span>1 Halaman Penuh</span>
                                </button>
                                <button type="button"
                                        id="btn-mode-width"
                                        onclick="switchPdfMode('width')"
                                        class="pdf-mode-btn inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold transition bg-slate-700 hover:bg-slate-600 text-slate-300"
                                        title="Pas Lebar (Zoom mengikuti lebar container)">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8M8 12h8M8 17h8M4 7h.01M4 12h.01M4 17h.01"/></svg>
                                    <span>Pas Lebar</span>
                                </button>
                                <span class="w-px h-4 bg-slate-700 mx-1"></span>
                                <button type="button"
                                        onclick="togglePdfFullscreen()"
                                        id="btn-fullscreen"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-slate-200 transition"
                                        title="Tampilkan Layar Penuh">
                                    <svg class="w-3.5 h-3.5" id="icon-fs-enter" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    <svg class="w-3.5 h-3.5 hidden" id="icon-fs-exit" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span id="text-fs">Layar Penuh</span>
                                </button>
                            </div>
                        </div>

                        {{-- Viewer Frame --}}
                        <div class="relative w-full bg-slate-900 flex-1">
                            <iframe id="pdf-viewer-frame"
                                    src="{{ $pdfFitPageUrl }}"
                                    class="w-full border-0 block"
                                    style="height: 84vh; min-height: 680px;"
                                    allow="fullscreen"
                                    title="Modul {{ $materi->judul_materi }}">
                            </iframe>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-500 flex-wrap gap-2 pt-1">
                        <span>💡 <strong>Tips:</strong> Gunakan mode <em>1 Halaman Penuh</em> atau <em>Layar Penuh</em> untuk membaca slide presentasi secara optimal tanpa scroll.</span>
                        <a href="{{ $pdfFitPageUrl }}" target="_blank" class="text-[#7B1038] hover:underline font-semibold">
                            Kendala pratinjau? Buka langsung di tab browser →
                        </a>
                    </div>
                @else
                    {{-- Non-PDF file banner --}}
                    <div class="p-6 bg-slate-50 border border-slate-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-[#7B1038] text-white flex items-center justify-center font-bold text-sm shrink-0">
                                📎
                            </span>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-800">{{ basename($materi->upload_file) }}</h4>
                                <p class="text-[11px] text-slate-500">Berkas ini dalam format dokumen Office. Silakan unduh untuk membaca atau membuka materi secara offline.</p>
                            </div>
                        </div>
                        <a href="{{ $fileBaseUrl }}" download
                           class="inline-flex items-center gap-2 bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs font-semibold px-4 py-2 rounded-xl transition shrink-0">
                            Unduh Berkas Modul
                        </a>
                    </div>
                @endif
            @else
                {{-- Siswa belum mengerjakan pre-test → tampilkan kartu terkunci (Clean & Minimalis) --}}
                <div class="bg-slate-50 border border-slate-200 border-l-4 border-l-[#7B1038] rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
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
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-[#7B1038] border border-slate-200">
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
                        Modul telah ditandai selesai{{ $materiSiswaRecord->selesai_at ? ' pada ' . $materiSiswaRecord->selesai_at->translatedFormat('d F Y H:i') : '' }}. Status kemajuan membaca bahan ajar telah tercatat selesai.
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
                            class="inline-flex items-center gap-2 bg-[#E8F8F0] hover:bg-slate-100 text-[#1F9254] hover:text-slate-700 border border-[#1F9254]/30 hover:border-slate-300 text-xs sm:text-sm font-semibold px-5 py-2.5 rounded-xl transition duration-150">
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
               class="inline-flex items-center gap-2 bg-slate-100 hover:bg-[#7B1038] hover:text-white text-[#7B1038] border border-slate-200 text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-xl transition shrink-0">
                Lihat Daftar Quiz →
            </a>
        </div>
    @endif

</div>

@if ($materi->upload_file && $pretestCompleted && str_ends_with(strtolower($materi->upload_file), '.pdf'))
@push('scripts')
<script>
    const PDF_BASE_URL = @js(route('portal.materi.file', $materi));

    function switchPdfMode(mode) {
        const iframe = document.getElementById('pdf-viewer-frame');
        const btnFit = document.getElementById('btn-mode-fit');
        const btnWidth = document.getElementById('btn-mode-width');
        if (!iframe) return;

        if (mode === 'fit') {
            iframe.src = PDF_BASE_URL + '#page=1&view=Fit&navpanes=0';
            btnFit?.classList.remove('bg-slate-700', 'text-slate-300', 'hover:bg-slate-600');
            btnFit?.classList.add('bg-[#7B1038]', 'text-white', 'shadow-2xs');
            btnWidth?.classList.remove('bg-[#7B1038]', 'text-white', 'shadow-2xs');
            btnWidth?.classList.add('bg-slate-700', 'text-slate-300', 'hover:bg-slate-600');
        } else {
            iframe.src = PDF_BASE_URL + '#page=1&view=FitH&navpanes=0';
            btnWidth?.classList.remove('bg-slate-700', 'text-slate-300', 'hover:bg-slate-600');
            btnWidth?.classList.add('bg-[#7B1038]', 'text-white', 'shadow-2xs');
            btnFit?.classList.remove('bg-[#7B1038]', 'text-white', 'shadow-2xs');
            btnFit?.classList.add('bg-slate-700', 'text-slate-300', 'hover:bg-slate-600');
        }
    }

    function togglePdfFullscreen() {
        const container = document.getElementById('pdf-reader-container');
        if (!container) return;

        if (!document.fullscreenElement) {
            if (container.requestFullscreen) {
                container.requestFullscreen();
            } else if (container.webkitRequestFullscreen) {
                container.webkitRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }

    document.addEventListener('fullscreenchange', () => {
        const isFs = !!document.fullscreenElement;
        const iframe = document.getElementById('pdf-viewer-frame');
        const iconEnter = document.getElementById('icon-fs-enter');
        const iconExit = document.getElementById('icon-fs-exit');
        const textFs = document.getElementById('text-fs');

        if (iframe) {
            iframe.style.height = isFs ? 'calc(100vh - 44px)' : '84vh';
        }
        if (iconEnter && iconExit && textFs) {
            iconEnter.classList.toggle('hidden', isFs);
            iconExit.classList.toggle('hidden', !isFs);
            textFs.innerText = isFs ? 'Keluar' : 'Layar Penuh';
        }
    });
</script>
@endpush
@endif
@endsection
