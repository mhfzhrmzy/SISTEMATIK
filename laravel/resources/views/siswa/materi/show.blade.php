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
               class="inline-flex items-center bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-xl shadow-xs transition">
                <span>Evaluasi Quiz</span>
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

                <div class="flex items-center justify-end flex-wrap gap-2 pb-1">
                    <a href="{{ $pdfFitPageUrl }}" target="_blank"
                       class="inline-flex items-center text-xs font-semibold bg-slate-100 hover:bg-[#7B1038] hover:text-white text-[#7B1038] border border-slate-200 px-3.5 py-2 rounded-xl transition">
                        <span>Buka di Tab Baru</span>
                    </a>
                    <a href="{{ $fileBaseUrl }}" download
                       class="inline-flex items-center text-xs font-semibold bg-[#7B1038] hover:bg-[#5E0C2B] text-white px-3.5 py-2 rounded-xl transition shadow-2xs">
                        <span>Unduh Berkas</span>
                    </a>
                </div>

                @if ($isPdf)
                    {{-- Interactive PDF Reader Container --}}
                    <div id="pdf-reader-container" class="rounded-2xl overflow-hidden bg-[#2B3035] border border-slate-700/80 shadow-lg flex flex-col transition-all duration-200 mx-auto w-full max-w-5xl">
                        {{-- Reader Top Toolbar --}}
                        <div class="bg-slate-800 text-slate-200 px-4 py-2.5 flex items-center justify-between flex-wrap gap-2 text-xs border-b border-slate-700 select-none">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-[#7B1038] text-white text-[10px] font-bold tracking-wide uppercase">
                                    PDF MODUL
                                </span>
                                <span class="font-bold text-slate-100 truncate max-w-[200px] sm:max-w-xs" title="{{ $materi->judul_materi }}">
                                    {{ $materi->judul_materi }}
                                </span>
                            </div>

                            {{-- Mode Controls (Fleksibel Landscape & Portrait) --}}
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[11px] text-slate-400 font-medium mr-1 hidden md:inline">Format:</span>
                                <button type="button"
                                        id="btn-mode-landscape"
                                        onclick="switchPdfMode('landscape')"
                                        class="pdf-mode-btn inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold transition bg-[#7B1038] text-white shadow-2xs cursor-pointer"
                                        title="Format Landscape / Slide Presentasi (16:9 Pas 1 Halaman Tanpa Terpotong)">
                                    <span>Landscape (Slide)</span>
                                </button>
                                <button type="button"
                                        id="btn-mode-portrait"
                                        onclick="switchPdfMode('portrait')"
                                        class="pdf-mode-btn inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold transition bg-slate-700 hover:bg-slate-600 text-slate-300 cursor-pointer"
                                        title="Format Portrait / Dokumen A4">
                                    <span>Portrait (A4)</span>
                                </button>
                                <button type="button"
                                        id="btn-mode-fit"
                                        onclick="switchPdfMode('fit')"
                                        class="pdf-mode-btn inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold transition bg-slate-700 hover:bg-slate-600 text-slate-300 cursor-pointer"
                                        title="Pas Layar Penuh 1 Halaman (Tinggi Otomatis)">
                                    <span>Pas Layar</span>
                                </button>
                                <span class="w-px h-4 bg-slate-700 mx-1"></span>
                                <button type="button"
                                        onclick="togglePdfFullscreen()"
                                        id="btn-fullscreen"
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-slate-200 transition cursor-pointer"
                                        title="Tampilkan Layar Penuh">
                                    <span id="text-fs">Layar Penuh</span>
                                </button>
                            </div>
                        </div>

                        {{-- Viewer Frame --}}
                        <div class="relative w-full bg-[#2B3035] flex items-center justify-center flex-1">
                            <iframe id="pdf-viewer-frame"
                                    src="{{ $pdfFitPageUrl }}"
                                    class="w-full border-0 block"
                                    style="height: 620px;"
                                    allow="fullscreen"
                                    title="Modul {{ $materi->judul_materi }}">
                            </iframe>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-500 flex-wrap gap-2 pt-1">
                        <span><strong>Tips:</strong> Gunakan mode <em>1 Halaman Penuh</em> atau <em>Layar Penuh</em> untuk membaca slide presentasi secara optimal tanpa scroll.</span>
                        <a href="{{ $pdfFitPageUrl }}" target="_blank" class="inline-flex items-center text-[#7B1038] hover:underline font-semibold">
                            <span>Kendala pratinjau? Buka langsung di tab browser</span>
                        </a>
                    </div>
                @else
                    {{-- Non-PDF file banner --}}
                    <div class="p-6 bg-slate-50 border border-slate-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-800">{{ basename($materi->upload_file) }}</h4>
                            <p class="text-[11px] text-slate-500">Berkas ini dalam format dokumen Office. Silakan unduh untuk membaca atau membuka materi secara offline.</p>
                        </div>
                        <a href="{{ $fileBaseUrl }}" download
                           class="inline-flex items-center bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs font-semibold px-4 py-2 rounded-xl transition shrink-0">
                            Unduh Berkas Modul
                        </a>
                    </div>
                @endif
            @else
                {{-- Siswa belum mengerjakan pre-test: tampilkan kartu terkunci (Clean & Minimalis) --}}
                <div class="bg-slate-50 border border-slate-200 border-l-4 border-l-[#7B1038] rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                    <div class="space-y-1">
                        <h3 class="text-sm sm:text-base font-bold text-[#7B1038]">Berkas Materi Terkunci</h3>
                        <p class="text-xs sm:text-sm text-[#6B7280] leading-relaxed max-w-xl">
                            Anda perlu menyelesaikan <strong>Pre-Test</strong> terlebih dahulu untuk dapat mengakses berkas modul pembelajaran ini. Setelah menyelesaikan Pre-Test, modul akan terbuka secara otomatis.
                        </p>
                    </div>
                    <div class="shrink-0">
                        <a href="{{ route('portal.quiz.index', $materi) }}"
                           class="inline-flex items-center bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs sm:text-sm font-semibold px-5 py-2.5 rounded-xl shadow-xs transition">
                            <span>Kerjakan Pre-Test Sekarang</span>
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#E8F8F0] text-[#1F9254]">
                            SELESAI DIPELAJARI
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-[#7B1038] border border-slate-200">
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
                        Bahan ajar telah ditandai selesai.
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
                            class="inline-flex items-center bg-[#E8F8F0] hover:bg-slate-100 text-[#1F9254] hover:text-slate-700 border border-[#1F9254]/30 hover:border-slate-300 text-xs sm:text-sm font-semibold px-5 py-2.5 rounded-xl transition duration-150">
                        <span>Selesai (Batal?)</span>
                    </button>
                @else
                    <button type="submit"
                            class="inline-flex items-center bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs sm:text-sm font-semibold px-5 py-2.5 rounded-xl shadow-xs transition duration-150">
                        <span>Tandai Selesai Membaca</span>
                    </button>
                @endif
            </form>
        </div>
    @endif

</div>

@if ($materi->upload_file && $pretestCompleted && str_ends_with(strtolower($materi->upload_file), '.pdf'))
@push('scripts')
<script>
    const PDF_BASE_URL = @js(route('portal.materi.file', $materi));
    let currentPdfMode = 'landscape';

    function updatePdfDimensions() {
        const container = document.getElementById('pdf-reader-container');
        const iframe = document.getElementById('pdf-viewer-frame');
        if (!container || !iframe) return;

        if (document.fullscreenElement) {
            container.style.maxWidth = '100vw';
            iframe.style.height = 'calc(100vh - 44px)';
            return;
        }

        const availHeight = Math.max(460, window.innerHeight - 200);

        if (currentPdfMode === 'landscape') {
            container.style.maxWidth = '1020px';
            const w = container.clientWidth || 980;
            // 16:9 ratio + ~44px viewer toolbar
            const targetH = Math.round((w * 9 / 16) + 40);
            iframe.style.height = Math.max(460, Math.min(availHeight, targetH)) + 'px';
        } else if (currentPdfMode === 'portrait') {
            container.style.maxWidth = '840px';
            iframe.style.height = Math.max(620, Math.min(860, availHeight)) + 'px';
        } else {
            // Pas Layar Penuh
            container.style.maxWidth = '100%';
            iframe.style.height = availHeight + 'px';
        }
    }

    function switchPdfMode(mode) {
        currentPdfMode = mode;
        const iframe = document.getElementById('pdf-viewer-frame');
        const btnLandscape = document.getElementById('btn-mode-landscape');
        const btnPortrait = document.getElementById('btn-mode-portrait');
        const btnFit = document.getElementById('btn-mode-fit');
        if (!iframe) return;

        [btnLandscape, btnPortrait, btnFit].forEach(btn => {
            if (!btn) return;
            btn.classList.remove('bg-[#7B1038]', 'text-white', 'shadow-2xs');
            btn.classList.add('bg-slate-700', 'text-slate-300', 'hover:bg-slate-600');
        });

        const activeBtn = mode === 'landscape' ? btnLandscape : (mode === 'portrait' ? btnPortrait : btnFit);
        if (activeBtn) {
            activeBtn.classList.remove('bg-slate-700', 'text-slate-300', 'hover:bg-slate-600');
            activeBtn.classList.add('bg-[#7B1038]', 'text-white', 'shadow-2xs');
        }

        updatePdfDimensions();

        // Refresh frame hash with view=Fit to re-scale the page
        iframe.src = PDF_BASE_URL + '#page=1&view=Fit&navpanes=0';
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
        const textFs = document.getElementById('text-fs');

        if (textFs) {
            textFs.innerText = isFs ? 'Keluar Layar Penuh' : 'Layar Penuh';
        }
        updatePdfDimensions();
    });

    window.addEventListener('resize', updatePdfDimensions);
    document.addEventListener('DOMContentLoaded', () => {
        updatePdfDimensions();
        setTimeout(updatePdfDimensions, 250);
    });
</script>
@endpush
@endif
@endsection
