@extends('layouts.app')

@section('title', 'Sertifikat & Portofolio Siswa')

@section('content')
<div class="w-full pb-8">

    <div class="lb-header-card">
        <div class="lb-header-top lb-header-top--center">
            <div class="lb-header-text">
                <h1 class="lb-header-title">Daftar Sertifikat &amp; Portofolio</h1>
                <p class="lb-header-subtitle">Koleksi sertifikat kelulusan modul dan sertifikat mandiri Anda.</p>
            </div>

            <div class="lb-header-actions">
                <span class="text-sm font-semibold text-white/90 whitespace-nowrap">
                    {{ $sertifikat->count() }} Sertifikat
                </span>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div id="alert-sertifikat-success"
             class="mb-4 rounded-2xl bg-white border border-gray-200 border-l-4 border-l-[#7B1038] shadow-xs px-5 py-3.5 flex items-center justify-between gap-3 transition-opacity duration-200">
            <p class="text-sm font-medium text-[#1F2937]">{{ session('success') }}</p>
            <button type="button"
                    onclick="document.getElementById('alert-sertifikat-success')?.remove()"
                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition shrink-0 cursor-pointer"
                    aria-label="Tutup notifikasi">
                &times;
            </button>
        </div>
    @endif

    {{-- Filter tanggal --}}
    <div class="mb-4 sm:mb-5">
        @include('siswa.sertifikat._filter')
    </div>

    {{-- Grid Kartu Sertifikat --}}
    <div class="certificate-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 380px), 1fr)); gap: 20px;">
        @forelse ($sertifikat as $item)
            <div class="bg-white rounded-2xl shadow-xs hover:shadow-md transition-all duration-200 p-5 flex flex-col cursor-pointer"
                 role="button"
                 tabindex="0"
                 data-url="{{ asset('storage/' . $item->file_sertifikat) }}"
                 data-judul="{{ $item->judul_sertifikat }}"
                 data-penerbit="{{ $item->penerbit ?? '' }}"
                 onclick="bukaKartuSertifikat(this, event)"
                 onkeydown="if ((event.key === 'Enter' || event.key === ' ') && !event.target.closest('[data-no-preview]')) { event.preventDefault(); bukaKartuSertifikat(this, event); }">
                <div class="flex-1">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-gray-100 text-gray-700 border border-gray-200">
                            {{ $item->tipe_sertifikat === 'materi' ? 'Kelulusan Modul' : 'Mandiri' }}
                        </span>
                        <span class="text-sm text-gray-600">{{ $item->tanggal_terbit ? \Carbon\Carbon::parse($item->tanggal_terbit)->locale('id')->translatedFormat('j F Y') : '-' }}</span>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900 leading-snug line-clamp-2">{{ $item->judul_sertifikat }}</h2>
                    <p class="text-sm text-gray-600 mt-2">Penerbit: {{ $item->penerbit ?? '-' }}</p>
                    @if ($item->materi)
                        <p class="text-sm text-[#7B1038] mt-1.5 font-semibold">Modul: {{ $item->materi->judul_materi }}</p>
                    @endif
                    @if ($item->deskripsi)
                        <p class="text-sm text-gray-700 mt-2.5 bg-gray-50 px-3.5 py-2.5 rounded-xl leading-relaxed line-clamp-3">{{ $item->deskripsi }}</p>
                    @endif
                </div>
                <div class="mt-auto pt-3.5 border-t border-gray-100 flex items-center justify-end gap-2.5" data-no-preview onclick="event.stopPropagation()">
                    @if ($item->tipe_sertifikat === 'mandiri')
                        <form action="{{ route('siswa.sertifikat.destroy', $item->id_sertifikat) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                    data-judul="{{ $item->judul_sertifikat }}"
                                    onclick="bukaModalHapus(this)"
                                    class="text-sm font-semibold px-4 py-2 rounded-xl border border-rose-500 text-rose-600 hover:bg-rose-600 hover:text-white transition cursor-pointer">
                                Hapus
                            </button>
                        </form>
                    @endif
                    @if ($item->file_sertifikat)
                        @php
                            $extSertifikat = pathinfo($item->file_sertifikat, PATHINFO_EXTENSION);
                            $namaUnduh = \Illuminate\Support\Str::slug($item->judul_sertifikat ?: 'sertifikat') . ($extSertifikat ? '.' . $extSertifikat : '');
                        @endphp
                        <a href="{{ asset('storage/' . $item->file_sertifikat) }}"
                           download="{{ $namaUnduh }}"
                           class="text-sm font-semibold px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-700 transition cursor-pointer">
                            Unduh
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-gray-50 py-16 text-center text-base text-gray-600 rounded-2xl">
                @if ($filterAktif)
                    Tidak ada sertifikat pada rentang tanggal ini.
                @else
                    Belum ada sertifikat yang diunggah. Klik tombol <b>+ Upload Sertifikat Baru</b> di atas untuk menambahkan berkas.
                @endif
            </div>
        @endforelse
    </div>

    {{-- Modal Preview Sertifikat (Popup Elegan Berukuran Sedang) --}}
    <div id="modal-preview-sertifikat"
         class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-150 opacity-0"
         role="dialog" aria-modal="true">
        <div id="panel-modal-sertifikat"
             class="w-full max-w-4xl max-h-[90vh] flex flex-col bg-white rounded-2xl shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-150 ease-out">

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-white flex items-center justify-between gap-4 shrink-0">
                <div class="min-w-0 flex-1">
                    <h3 id="judul-modal-sertifikat" class="text-lg font-semibold text-[#1F2937] truncate">Lihat Sertifikat</h3>
                    <p id="penerbit-modal-sertifikat" class="text-sm text-gray-600 mt-0.5 truncate hidden"></p>
                </div>
                <button type="button"
                        onclick="tutupModalSertifikat()"
                        class="w-10 h-10 inline-flex items-center justify-center rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition shrink-0"
                        aria-label="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Area Isi --}}
            <div class="flex-1 min-h-0 overflow-y-auto bg-gray-50 p-4 sm:p-6 flex flex-col items-center">
                <div id="pembuat-sertifikat" class="w-full my-auto flex flex-col items-center justify-center">
                    <div class="flex items-center justify-center gap-2.5 py-12 text-sm text-gray-600">
                        <svg class="w-5 h-5 animate-spin text-[#7B1038]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0112 21h7v-4a17.982 17.982 0 011.211-2.769L3.711 15.291z"></path>
                        </svg>
                        <span>Memuat sertifikat...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus Sertifikat --}}
    <div id="modal-hapus-sertifikat"
         class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-150 opacity-0"
         role="dialog" aria-modal="true" aria-labelledby="judul-modal-hapus">
        <div id="panel-modal-hapus"
             class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-150 ease-out">
            <div class="p-6">
                <h3 id="judul-modal-hapus" class="text-lg font-bold text-[#1F2937]">Hapus Sertifikat?</h3>
                <p class="text-sm text-gray-600 mt-1.5 leading-relaxed">
                    Sertifikat <span id="judul-hapus-sertifikat" class="font-semibold text-[#1F2937]"></span> akan dihapus secara permanen dari portofolio Anda.
                </p>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button"
                            onclick="tutupModalHapus()"
                            class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            id="btn-konfirmasi-hapus"
                            class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-[#7B1038] hover:bg-[#5E0C2B] text-white shadow-xs transition cursor-pointer">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        if (window.pdfjsLib) {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }

        function bukaKartuSertifikat(cardEl, event) {
            if (event && event.target && event.target.closest('[data-no-preview]')) {
                return;
            }
            tampilkanSertifikat(cardEl.dataset.url || '', cardEl.dataset.judul || '', cardEl.dataset.penerbit || '');
        }

        function tampilkanSertifikat(url, judul, penerbit) {
            const modal = document.getElementById('modal-preview-sertifikat');
            const modalPanel = document.getElementById('panel-modal-sertifikat');
            const judulEl = document.getElementById('judul-modal-sertifikat');
            const penerbitEl = document.getElementById('penerbit-modal-sertifikat');
            const pembuat = document.getElementById('pembuat-sertifikat');

            judulEl.textContent = judul || 'Lihat Sertifikat';
            if (penerbit && penerbit.trim() !== '') {
                penerbitEl.textContent = 'Penerbit: ' + penerbit.trim();
                penerbitEl.classList.remove('hidden');
            } else {
                penerbitEl.textContent = '';
                penerbitEl.classList.add('hidden');
            }

            pembuat.innerHTML = `
                <div class="flex items-center justify-center gap-2.5 py-12 text-sm text-gray-600">
                    <svg class="w-5 h-5 animate-spin text-[#7B1038]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0112 21h7v-4a17.982 17.982 0 011.211-2.769L3.711 15.291z"></path>
                    </svg>
                    <span>Memuat sertifikat...</span>
                </div>
            `;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                modalPanel.classList.remove('scale-95', 'opacity-0');
                modalPanel.classList.add('scale-100', 'opacity-100');
            });

            const cleanUrl = url.split('?')[0].split('#')[0].toLowerCase();
            const isImage = /\.(jpe?g|png|webp|gif|bmp)$/i.test(cleanUrl);

            if (isImage) {
                const img = document.createElement('img');
                img.src = url;
                img.alt = judul || 'Sertifikat';
                img.className = 'max-w-full max-h-[65vh] object-contain mx-auto bg-white border border-gray-200 rounded-lg shadow-sm';
                img.onload = function () {
                    pembuat.innerHTML = '';
                    pembuat.appendChild(img);
                };
                img.onerror = function () {
                    tampilkanErrorSertifikat();
                };
            } else {
                if (!window.pdfjsLib) {
                    tampilkanErrorSertifikat();
                    return;
                }

                const loadingTask = pdfjsLib.getDocument(url);
                loadingTask.promise.then(async function (pdf) {
                    pembuat.innerHTML = '';
                    const container = document.createElement('div');
                    container.className = 'flex flex-col items-center gap-4 w-full';

                    const dpr = Math.min(window.devicePixelRatio || 1, 2);

                    for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                        const page = await pdf.getPage(pageNum);
                        const unscaledViewport = page.getViewport({ scale: 1 });
                        const availableWidth = pembuat.clientWidth > 0 ? (pembuat.clientWidth - 16) : 750;
                        const scale = Math.min(availableWidth / unscaledViewport.width, 1.5);
                        const viewport = page.getViewport({ scale: scale * dpr });

                        const canvas = document.createElement('canvas');
                        canvas.width = viewport.width;
                        canvas.height = viewport.height;
                        const displayWidth = Math.round(viewport.width / dpr);
                        canvas.style.width = '100%';
                        canvas.style.maxWidth = displayWidth + 'px';
                        canvas.style.height = 'auto';
                        canvas.className = 'bg-white border border-gray-200 rounded-lg shadow-sm';
                        canvas.setAttribute('oncontextmenu', 'return false;');
                        canvas.addEventListener('contextmenu', function (e) {
                            e.preventDefault();
                        });

                        const renderContext = {
                            canvasContext: canvas.getContext('2d'),
                            viewport: viewport
                        };

                        await page.render(renderContext).promise;
                        container.appendChild(canvas);
                    }

                    pembuat.appendChild(container);
                }).catch(function (err) {
                    console.error('PDF.js render error:', err);
                    tampilkanErrorSertifikat();
                });
            }
        }

        function tampilkanErrorSertifikat() {
            const pembuat = document.getElementById('pembuat-sertifikat');
            pembuat.innerHTML = '<div class="py-16 text-center text-base text-gray-600">Berkas sertifikat tidak dapat ditampilkan</div>';
        }

        function tutupModalSertifikat() {
            const modal = document.getElementById('modal-preview-sertifikat');
            const modalPanel = document.getElementById('panel-modal-sertifikat');
            if (!modal || modal.classList.contains('hidden')) return;

            modal.classList.add('opacity-0');
            modalPanel.classList.remove('scale-100', 'opacity-100');
            modalPanel.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
                const pembuat = document.getElementById('pembuat-sertifikat');
                if (pembuat) pembuat.innerHTML = '';
            }, 150);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const modalPreview = document.getElementById('modal-preview-sertifikat');
                const modalHapus = document.getElementById('modal-hapus-sertifikat');
                if (modalPreview && !modalPreview.classList.contains('hidden')) {
                    tutupModalSertifikat();
                }
                if (modalHapus && !modalHapus.classList.contains('hidden')) {
                    tutupModalHapus();
                }
            }
        });

        document.getElementById('modal-preview-sertifikat').addEventListener('click', function (e) {
            if (e.target === this) {
                tutupModalSertifikat();
            }
        });

        let formHapusAktif = null;

        function bukaModalHapus(btn) {
            const modal = document.getElementById('modal-hapus-sertifikat');
            const panel = document.getElementById('panel-modal-hapus');
            const judulSpan = document.getElementById('judul-hapus-sertifikat');
            formHapusAktif = btn.closest('form');

            if (judulSpan) {
                const judul = (btn.dataset.judul || '').trim();
                judulSpan.textContent = judul ? `"${judul}"` : 'ini';
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                panel.classList.remove('scale-95', 'opacity-0');
                panel.classList.add('scale-100', 'opacity-100');
            });
        }

        function tutupModalHapus() {
            const modal = document.getElementById('modal-hapus-sertifikat');
            const panel = document.getElementById('panel-modal-hapus');
            if (!modal || modal.classList.contains('hidden')) return;

            modal.classList.add('opacity-0');
            panel.classList.remove('scale-100', 'opacity-100');
            panel.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
                formHapusAktif = null;
            }, 150);
        }

        document.getElementById('btn-konfirmasi-hapus').addEventListener('click', function () {
            if (formHapusAktif) {
                formHapusAktif.submit();
            }
        });

        document.getElementById('modal-hapus-sertifikat').addEventListener('click', function (e) {
            if (e.target === this) {
                tutupModalHapus();
            }
        });
    </script>
    @endpush

</div>
@endsection
