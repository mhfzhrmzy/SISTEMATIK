@extends('layouts.app')

@section('title', $materi->judul_materi . ' — Detail Modul')

@section('content')
<div class="portal-stack">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-600">
        <a href="{{ route('portal.materi.index') }}" class="hover:text-[#7B1038] font-medium transition">Portal Siswa</a>
        <span>›</span>
        <span class="text-[#1F2937] font-semibold truncate">{{ $materi->judul_materi }}</span>
    </nav>

    {{-- Header Modul --}}
    <x-card padding="" class="portal-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="portal-badge text-xs font-semibold uppercase tracking-wide bg-[#7B1038] text-white mb-2 shadow-2xs">
                BAHAN AJAR &amp; MODUL
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#1F2937] leading-snug mb-1">
                {{ $materi->judul_materi }}
            </h1>
            <p class="text-base text-gray-600 mt-1 flex items-center gap-1.5">
                <span>Diampu oleh</span>
                <strong class="text-[#1F2937] font-semibold">{{ $materi->guru?->nama_lengkap ?? 'Guru Pengampu' }}</strong>
            </p>
        </div>

        <div class="shrink-0">
            <a href="{{ route('portal.quiz.index', $materi) }}"
               class="btn-primary bg-[#7B1038] text-white shadow-xs">
                <span>Evaluasi Quiz</span>
            </a>
        </div>
    </x-card>

    {{-- Deskripsi Materi --}}
    <x-card padding="" class="portal-card space-y-2">
        <h2 class="text-base font-semibold text-[#7B1038] uppercase tracking-wider">Deskripsi Pembelajaran</h2>
        <p class="text-sm sm:text-base text-[#1F2937] leading-relaxed break-words">{{ trim($materi->isi_materi) ?: 'Membahas pengenalan konsep dasar, penerapan langsung di bidang keahlian, latihan mandiri, dan evaluasi pemahaman standar industri.' }}</p>
    </x-card>

    {{-- Modul / File Materi --}}
    <x-card padding="" class="portal-card space-y-4">
        @if ($materi->upload_file)
            @if ($pretestCompleted)
                @php
                    $isPdf = str_ends_with(strtolower($materi->upload_file), '.pdf');
                @endphp

                @if ($isPdf)
                    {{-- Tombol Unduh File Saja --}}
                    <div class="flex items-center justify-end pb-1">
                        <a href="{{ route('portal.materi.file', $materi) }}"
                           download="{{ basename($materi->upload_file) }}"
                           class="btn-primary bg-[#7B1038] text-white shadow-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <span>Unduh File</span>
                        </a>
                    </div>

                    {{-- PDF.js Interactive Reader (1 Kotak Scrollable Sesuai Orientasi PDF) --}}
                    <div id="pdf-container-wrap" class="portal-panel w-full bg-gray-50 flex flex-col items-center">
                        <div id="pdf-loading" class="py-12 flex flex-col items-center justify-center gap-2.5 text-gray-500 text-sm font-medium">
                            <svg class="animate-spin h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memuat modul...</span>
                        </div>
                        <div id="pdf-error" class="hidden py-10 text-center text-sm text-rose-600 font-medium">Gagal memuat modul PDF. Silakan muat ulang halaman.</div>

                        {{-- 1 Kotak Viewer Scrollable --}}
                        <div id="pdf-scroll-box"
                             class="portal-pdf-preview hidden w-full overflow-y-auto overscroll-contain border border-gray-200 bg-white/70 shadow-sm p-3 sm:p-4 my-2">
                            <div id="pdf-pages" class="flex flex-col items-center gap-4 w-full"></div>
                        </div>
                    </div>
                @else
                    {{-- Non-PDF file banner --}}
                    <div class="portal-panel bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">{{ basename($materi->upload_file) }}</h4>
                            <p class="text-sm text-gray-600">Berkas ini dalam format dokumen Office. Silakan unduh untuk membaca atau membuka materi secara offline.</p>
                        </div>
                        <a href="{{ route('portal.materi.file', $materi) }}" download
                           class="btn-primary bg-[#7B1038] text-white shrink-0">
                            Unduh Berkas Modul
                        </a>
                    </div>
                @endif
            @else
                {{-- Siswa belum mengerjakan pre-test: tampilkan kartu terkunci (Clean & Minimalis) --}}
                <div class="portal-panel bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                    <div class="space-y-1">
                        <h3 class="text-sm sm:text-base font-bold text-[#7B1038]">Berkas Materi Terkunci</h3>
                        <p class="text-sm text-gray-600 leading-relaxed max-w-xl">
                            Anda perlu menyelesaikan <strong>Pre-Test</strong> terlebih dahulu untuk dapat mengakses berkas modul pembelajaran ini. Setelah menyelesaikan Pre-Test, modul akan terbuka secara otomatis.
                        </p>
                    </div>
                    <div class="shrink-0">
                        <a href="{{ route('portal.quiz.index', $materi) }}"
                           class="btn-primary bg-[#7B1038] text-white shadow-xs">
                            <span>Kerjakan Pre-Test Sekarang</span>
                        </a>
                    </div>
                </div>
            @endif
        @else
            <div class="portal-panel text-center bg-gray-50 text-sm text-gray-600">
                Belum ada berkas modul yang diunggah untuk materi ini.
            </div>
        @endif
    </x-card>

    {{-- Aksi Konfirmasi Penyelesaian Modul Belajar --}}
    @if ($pretestCompleted)
        <x-card padding="" class="portal-card flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    @if ($materiSiswaRecord && $materiSiswaRecord->is_selesai)
                        <span class="portal-badge inline-flex items-center text-sm font-bold uppercase tracking-wider bg-[#E8F8F0] text-[#1F9254]">
                            SELESAI DIPELAJARI
                        </span>
                    @else
                        <span class="portal-badge inline-flex items-center text-sm font-bold uppercase tracking-wider bg-slate-100 text-[#7B1038] border border-slate-200">
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
                <p class="text-sm text-gray-600">
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
                            title="Klik untuk menandai modul belum selesai"
                            class="portal-btn bg-[#E8F8F0] hover:bg-slate-100 text-[#1F9254] hover:text-slate-700 border border-[#1F9254]/30 hover:border-slate-300 cursor-pointer">
                        <span>Tandai Belum Selesai</span>
                    </button>
                @else
                    <button type="submit"
                            class="btn-primary bg-[#7B1038] text-white shadow-xs cursor-pointer">
                        <span>Tandai Selesai Membaca</span>
                    </button>
                @endif
            </form>
        </x-card>
    @endif

</div>

@if ($materi->upload_file && $pretestCompleted && str_ends_with(strtolower($materi->upload_file), '.pdf'))
@push('styles')
<style>
    #pdf-scroll-box::-webkit-scrollbar {
        width: 8px;
    }
    #pdf-scroll-box::-webkit-scrollbar-track {
        background: #F3F4F6;
        border-radius: 8px;
    }
    #pdf-scroll-box::-webkit-scrollbar-thumb {
        background: #D1D5DB;
        border-radius: 8px;
    }
    #pdf-scroll-box::-webkit-scrollbar-thumb:hover {
        background: #9CA3AF;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const PDF_URL = @js(route('portal.materi.file', $materi));
        const loadingEl = document.getElementById('pdf-loading');
        const errorEl = document.getElementById('pdf-error');
        const scrollBox = document.getElementById('pdf-scroll-box');
        const pagesContainer = document.getElementById('pdf-pages');

        if (!window.pdfjsLib) {
            if (loadingEl) loadingEl.classList.add('hidden');
            if (errorEl) errorEl.classList.remove('hidden');
            return;
        }

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const pageData = new Map();
        let observer = null;

        function renderPageCanvas(pageNum) {
            const data = pageData.get(pageNum);
            if (!data || !data.page) return;

            const wrapper = document.querySelector(`.pdf-page-wrapper[data-page-number="${pageNum}"]`);
            if (!wrapper) return;

            const canvas = wrapper.querySelector('canvas');
            if (!canvas) return;

            const containerWidth = wrapper.clientWidth;
            if (containerWidth === 0) return;

            if (data.rendering && data.renderTask) {
                data.renderTask.cancel();
            }

            data.rendering = true;

            const unscaledViewport = data.page.getViewport({ scale: 1 });
            const scale = containerWidth / unscaledViewport.width;
            const dpr = Math.min(window.devicePixelRatio || 1, 2);

            canvas.width = Math.floor(containerWidth * dpr);
            canvas.height = Math.floor(unscaledViewport.height * scale * dpr);
            canvas.style.width = '100%';
            canvas.style.height = 'auto';

            const ctx = canvas.getContext('2d');
            const renderViewport = data.page.getViewport({ scale: scale * dpr });

            data.renderTask = data.page.render({
                canvasContext: ctx,
                viewport: renderViewport
            });

            data.renderTask.promise.then(function () {
                data.rendering = false;
                data.rendered = true;
            }).catch(function (err) {
                if (err && err.name === 'RenderingCancelledException') {
                    return;
                }
                data.rendering = false;
            });
        }

        pdfjsLib.getDocument({ url: PDF_URL, withCredentials: true }).promise.then(async function (pdf) {
            const numPages = pdf.numPages;
            const page1 = await pdf.getPage(1);
            const view1 = page1.getViewport({ scale: 1 });
            const isLandscape = view1.width > view1.height;

            if (scrollBox) {
                if (isLandscape) {
                    scrollBox.classList.add('max-w-4xl', 'lg:max-w-5xl');
                } else {
                    scrollBox.classList.add('max-w-2xl', 'lg:max-w-3xl');
                }
                scrollBox.style.maxHeight = '72vh';
                scrollBox.style.aspectRatio = `${view1.width} / ${view1.height}`;
            }

            observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        const pageNum = parseInt(entry.target.getAttribute('data-page-number'), 10);
                        const data = pageData.get(pageNum);
                        if (data && !data.rendered && !data.rendering) {
                            renderPageCanvas(pageNum);
                        }
                    }
                });
            }, {
                root: scrollBox,
                rootMargin: '250px 0px 250px 0px',
                threshold: 0.01
            });

            for (let i = 1; i <= numPages; i++) {
                const page = i === 1 ? page1 : await pdf.getPage(i);
                const view = page.getViewport({ scale: 1 });
                const pageIsLandscape = view.width > view.height;

                pageData.set(i, {
                    page: page,
                    rendered: false,
                    rendering: false,
                    renderTask: null,
                    isLandscape: pageIsLandscape
                });

                const wrapper = document.createElement('div');
                wrapper.className = `pdf-page-wrapper w-full mx-auto bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden flex items-center justify-center transition-all duration-200 shrink-0`;
                wrapper.setAttribute('data-page-number', i);
                wrapper.style.aspectRatio = `${view.width} / ${view.height}`;

                const canvas = document.createElement('canvas');
                canvas.className = 'w-full h-auto block select-none pointer-events-auto';
                canvas.addEventListener('contextmenu', function (e) {
                    e.preventDefault();
                });

                wrapper.appendChild(canvas);
                pagesContainer.appendChild(wrapper);
                observer.observe(wrapper);
            }

            if (loadingEl) loadingEl.classList.add('hidden');
            if (scrollBox) {
                scrollBox.classList.remove('hidden');
            }

            // Segera render halaman pertama
            renderPageCanvas(1);
        }).catch(function (err) {
            console.error('PDF.js Error:', err);
            if (loadingEl) loadingEl.classList.add('hidden');
            if (errorEl) errorEl.classList.remove('hidden');
        });

        let resizeTimeout = null;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function () {
                pageData.forEach(function (data, pageNum) {
                    if (data.rendered) {
                        renderPageCanvas(pageNum);
                    }
                });
            }, 200);
        });
    });
</script>
@endpush
@endif
@endsection
