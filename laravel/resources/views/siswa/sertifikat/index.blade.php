@extends('layouts.app')

@section('title', 'Sertifikat & Portofolio Siswa')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-5">
        <div>
            <span class="inline-block px-3 py-1 rounded-full text-sm font-bold uppercase tracking-wider bg-[#7B1038] text-white mb-1.5 shadow-2xs">
                PRESTASI BELAJAR
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-[#1F2937]">Daftar Sertifikat &amp; Portofolio</h1>
            <p class="text-sm text-gray-600 mt-1">Koleksi sertifikat kelulusan modul dan sertifikat mandiri Anda.</p>
        </div>
        <a href="{{ route('siswa.sertifikat.create') }}"
           class="inline-flex items-center gap-2 bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-xs transition self-start sm:self-auto">
            + Upload Sertifikat Baru
        </a>
    </div>

    {{-- Filter tanggal --}}
    <div>
        @include('siswa.sertifikat._filter')
    </div>

    {{-- Grid Kartu Sertifikat --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($sertifikat as $item)
            <x-card class="hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-sm px-3 py-0.5 rounded-full font-semibold {{ $item->tipe_sertifikat === 'materi' ? 'bg-[#7B1038] text-white shadow-2xs' : 'bg-[#E8F8F0] text-[#1F9254]' }}">
                            {{ $item->tipe_sertifikat === 'materi' ? 'Kelulusan Modul' : 'Mandiri' }}
                        </span>
                        <span class="text-sm text-gray-600">{{ $item->tanggal_terbit ?? '-' }}</span>
                    </div>
                    <h2 class="font-bold text-[#1F2937] text-base leading-snug">{{ $item->judul_sertifikat }}</h2>
                    <p class="text-sm text-gray-600 mt-1">Penerbit: {{ $item->penerbit ?? '-' }}</p>
                    @if ($item->materi)
                        <p class="text-sm text-[#7B1038] mt-1 font-semibold">Modul: {{ $item->materi->judul_materi }}</p>
                    @endif
                    @if ($item->deskripsi)
                        <p class="text-sm text-gray-600 mt-3 bg-gray-50 p-3 rounded-xl leading-relaxed">{{ $item->deskripsi }}</p>
                    @endif
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-sm">
                    <button type="button"
                            class="inline-flex items-center font-semibold text-[#7B1038] hover:underline"
                            onclick="tampilkanSertifikat('{{ asset('storage/' . $item->file_sertifikat) }}', 'Lihat Sertifikat')">
                        <span>Lihat Sertifikat</span>
                    </button>
                    @if ($item->tipe_sertifikat === 'mandiri')
                        <form action="{{ route('siswa.sertifikat.destroy', $item->id_sertifikat) }}" method="POST" onsubmit="return confirm('Hapus sertifikat ini?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold transition">Hapus</button>
                        </form>
                    @endif
                </div>
            </x-card>
        @empty
            <div class="col-span-full bg-gray-50 p-10 text-center text-sm text-gray-600 rounded-2xl">
                @if ($filterAktif)
                    Tidak ada sertifikat pada rentang tanggal ini.
                @else
                    Belum ada sertifikat yang diunggah. Klik tombol <b>+ Upload Sertifikat Baru</b> di atas untuk menambahkan berkas.
                @endif
            </div>
        @endforelse
    </div>
    {{-- Modal Preview Sertifikat --}}
    <div id="modal-preview-sertifikat"
         class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <div class="bg-white w-full max-w-4xl max-h-[90vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-5 py-3.5 border-b border-gray-100 bg-gray-50/60">
                <div class="text-sm font-bold text-[#1F2937] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#7B1038]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m-6 4h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                    </svg>
                    <span id="judul-modal-sertifikat">Detail Sertifikat</span>
                </div>
                <button type="button"
                        onclick="tutupModalSertifikat()"
                        class="text-gray-400 hover:text-gray-700 p-1.5 rounded-lg hover:bg-gray-100 transition"
                        aria-label="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-auto p-4 bg-gray-50">
                <div class="w-full flex items-center justify-center min-h-[60vh]">
                    <div id="pembuat-sertifikat">
                        <div class="text-sm text-gray-400 flex flex-col items-center gap-3 py-10">
                            <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0112 21h7v-4a17.982 17.982 0 011.211-2.769L3.711 15.291z"></path>
                            </svg>
                            <span>Memuat sertifikat…</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Sertifikat ditampilkan langsung di halaman, tanpa membuka tab baru.</span>
                <button type="button"
                        onclick="window.open(currentSertifikatUrl, '_blank')"
                        class="text-[#7B1038] font-bold hover:underline">
                    Buka di Tab Baru
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentSertifikatUrl = '';

        function tampilkanSertifikat(url, judul) {
            currentSertifikatUrl = url;
            const modal = document.getElementById('modal-preview-sertifikat');
            const judulEl = document.getElementById('judul-modal-sertifikat');
            const pembuat = document.getElementById('pembuat-sertifikat');

            judulEl.textContent = judul;

            const iframe = document.createElement('iframe');
            iframe.setAttribute('src', url);
            iframe.setAttribute('class', 'w-full h-[75vh] border-0 rounded-xl shadow-sm');

            pembuat.innerHTML = '';
            pembuat.appendChild(iframe);

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function tutupModalSertifikat() {
            const modal = document.getElementById('modal-preview-sertifikat');
            const pembuat = document.getElementById('pembuat-sertifikat');

            pembuat.innerHTML = '<div class="text-sm text-gray-400 flex flex-col items-center gap-3 py-10"><span>Pratinjau ditutup.</span></div>';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            currentSertifikatUrl = '';
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !document.getElementById('modal-preview-sertifikat').classList.contains('hidden')) {
                tutupModalSertifikat();
            }
        });

        document.getElementById('modal-preview-sertifikat').addEventListener('click', function (e) {
            if (e.target === this) tutupModalSertifikat();
        });
    </script>
    @endpush

</div>
@endsection