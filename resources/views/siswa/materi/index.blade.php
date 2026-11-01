@extends('layouts.app')

@section('title', 'Bahan Ajar Aktif — Portal Siswa')

@section('content')
<div class="space-y-4 sm:space-y-5">

    {{-- 1. Informasi Profil Siswa & Poin (Simetris & Sejajar) --}}
    @php
        $siswaUser = $siswa ?? auth('siswa')->user();
    @endphp
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-5 pb-3.5 border-b border-gray-200/70">
        {{-- Data Siswa --}}
        <div class="flex flex-col justify-center">
            <h1 class="text-xl sm:text-2xl font-bold text-[#1F2937] tracking-tight leading-tight">
                {{ $siswaUser->nama_lengkap }}
            </h1>

            <p class="text-xs sm:text-sm font-semibold text-[#7B1038] leading-snug mt-0.5">
                Kelas {{ $siswaUser->kelas }} • {{ $siswaUser->jurusan }}
            </p>

            <p class="text-[11px] sm:text-xs text-[#6B7280] font-mono leading-none mt-1">
                NISN: {{ $siswaUser->nisn }}
            </p>
        </div>

        {{-- Badge Poin Langsung (x Poin) --}}
        <div class="bg-white rounded-xl px-4 py-2 shadow-xs border border-gray-100 flex items-center justify-center shrink-0 self-start sm:self-center">
            <span class="text-sm sm:text-base font-bold text-[#7B1038] tracking-tight leading-none">
                {{ number_format($siswaUser->poin ?? 0, 0, ',', '.') }} Poin
            </span>
        </div>
    </div>

    {{-- 2. Header Bagian & Filter Tab --}}
    <div class="space-y-2">
        <h2 class="text-lg sm:text-xl font-bold text-[#1F2937] tracking-tight leading-tight">
            Bahan Ajar Aktif
        </h2>

        {{-- Segmented Tab Filter --}}
        <div class="inline-flex p-1 rounded-full bg-gray-100 gap-1 text-xs sm:text-sm font-medium flex-wrap">
            <button type="button"
                    onclick="filterMateri('semua', this)"
                    class="filter-tab-btn active px-3.5 py-1.5 rounded-full transition-all duration-150 bg-[#7B1038] text-white font-semibold shadow-xs">
                Semua Materi ({{ $countSemua }})
            </button>
            <button type="button"
                    onclick="filterMateri('belum-dimulai', this)"
                    class="filter-tab-btn px-3.5 py-1.5 rounded-full transition-all duration-150 text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10">
                Belum Dimulai ({{ $countBelumDimulai }})
            </button>
            <button type="button"
                    onclick="filterMateri('sedang-berjalan', this)"
                    class="filter-tab-btn px-3.5 py-1.5 rounded-full transition-all duration-150 text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10">
                Sedang Berjalan ({{ $countSedangBerjalan }})
            </button>
            <button type="button"
                    onclick="filterMateri('selesai', this)"
                    class="filter-tab-btn px-3.5 py-1.5 rounded-full transition-all duration-150 text-[#5E0C2B] hover:text-[#7B1038] hover:bg-[#7B1038]/10">
                Selesai ({{ $countSelesai }})
            </button>
        </div>
    </div>

    {{-- 3. Daftar Kartu Materi (Grid 2 Kolom Responsif Sesuai Referensi) --}}
    @if ($materis->isEmpty())
        <div class="bg-white rounded-2xl p-10 sm:p-14 text-center shadow-xs">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-[#7B1038]/10 flex items-center justify-center text-[#7B1038]">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-[#1F2937] mb-1">Belum Ada Materi</h3>
            <p class="text-sm text-[#6B7280]">Guru belum menambahkan materi untuk kelas dan jurusan Anda.</p>
        </div>
    @else
        <div id="materiGrid" class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 gap-6">
            @foreach ($materis as $materi)
                <div class="materi-card bg-white rounded-2xl p-6 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between"
                     data-status="{{ $materi->statusKey }}">

                    <div>
                        {{-- Baris Atas: Kode Modul & Status Badge --}}
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-[#5E0C2B] tracking-wide">
                                {{ $materi->kode_modul }}
                            </span>

                            @if ($materi->statusLabel)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $materi->statusBadgeClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $materi->statusDotClass }}"></span>
                                    {{ $materi->statusLabel }}
                                </span>
                            @endif
                        </div>

                        {{-- Judul Materi --}}
                        <h2 class="text-lg sm:text-xl font-bold text-[#1F2937] mb-2 leading-snug">
                            {{ $materi->judul_materi }}
                        </h2>

                        {{-- Deskripsi Materi --}}
                        <p class="text-xs sm:text-sm text-[#6B7280] leading-relaxed line-clamp-2 mb-5">
                            {{ $materi->isi_materi ?: 'Membahas pengenalan konsep dasar, penerapan langsung di bidang keahlian, latihan mandiri, dan evaluasi pemahaman standar industri.' }}
                        </p>

                        {{-- Kartu Guru Pengampu --}}
                        <div class="bg-[#F8F9FA] rounded-xl p-3 flex items-center gap-3 mb-5">
                            <div class="w-9 h-9 rounded-full bg-[#7B1038] text-white flex items-center justify-center shrink-0 shadow-xs text-sm">
                                🎓
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider block">
                                    GURU PENGAMPU
                                </span>
                                <span class="text-xs sm:text-sm font-semibold text-[#1F2937] truncate block" title="{{ $materi->adminGuru->nama_lengkap ?? 'Guru Pengampu' }}">
                                    {{ $materi->adminGuru->nama_lengkap ?? 'Guru Pengampu' }}
                                </span>
                            </div>
                        </div>

                        {{-- Progress Kemajuan Belajar --}}
                        <div class="mb-5">
                            <div class="flex items-center justify-between text-xs font-semibold text-[#1F2937] mb-2">
                                <span>Kemajuan Belajar</span>
                                <span class="text-[#7B1038] font-bold">{{ $materi->progress }}% Selesai</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#7B1038] rounded-full transition-all duration-300" style="width: {{ $materi->progress }}%;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Tiga Tombol Aksi di Bawah (Pre-Test | Bahan Ajar | Post-Test) --}}
                    <div class="grid grid-cols-3 gap-2.5 pt-2">

                        {{-- Kolom 1: PRE-TEST --}}
                        @if ($materi->pretestDone && $materi->hasPretest)
                            {{-- Pre-Test Selesai -> MAROON UTAMA --}}
                            <a href="{{ route('portal.quiz.index', $materi) }}"
                               class="bg-[#7B1038] hover:bg-[#5E0C2B] text-white rounded-xl p-2.5 sm:p-3 text-center transition flex flex-col items-center justify-center shadow-xs group"
                               title="Lihat riwayat Pre-Test">
                                <span class="text-white font-bold text-xs flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    {{ $materi->pretestAttempt->total_poin ?? '100' }}/100
                                </span>
                                <span class="font-bold text-xs text-white mt-0.5">Pre-Test</span>
                                <span class="text-[10px] text-white/80">Sudah Selesai</span>
                            </a>
                        @elseif ($materi->hasPretest)
                            {{-- Pre-Test Belum Dikerjakan -> MAROON SAMAR --}}
                            <a href="{{ route('portal.quiz.kerjakan', $materi->pretest) }}"
                               class="bg-[#7B1038]/10 hover:bg-[#7B1038]/20 text-[#7B1038] border border-[#7B1038]/25 rounded-xl p-2.5 sm:p-3 text-center transition flex flex-col items-center justify-center group">
                                <svg class="w-3.5 h-3.5 mb-0.5 text-[#7B1038]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="font-bold text-xs text-[#7B1038] mt-0.5">Pre-Test</span>
                                <span class="text-[10px] text-[#7B1038]/80 font-medium">Mulai Sekarang</span>
                            </a>
                        @else
                            {{-- Tidak ada Pre-Test (Bebas Akses) -> MAROON SAMAR --}}
                            <div class="bg-[#7B1038]/5 border border-[#7B1038]/15 rounded-xl p-2.5 sm:p-3 text-center flex flex-col items-center justify-center text-[#7B1038]/70">
                                <span class="font-bold text-xs flex items-center justify-center gap-1 text-[#7B1038]/70">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="font-bold text-xs text-[#7B1038]/90 mt-0.5">Pre-Test</span>
                                <span class="text-[10px] text-[#7B1038]/60">Bebas Akses</span>
                            </div>
                        @endif

                        {{-- Kolom 2: BAHAN AJAR / LANJUT MATERI --}}
                        @if ($materi->pretestDone)
                            @if ($materi->isMateriSelesai)
                                {{-- Bahan Ajar Selesai Dibaca -> MAROON UTAMA --}}
                                <a href="{{ route('portal.materi.show', $materi) }}"
                                   class="bg-[#7B1038] hover:bg-[#5E0C2B] text-white rounded-xl p-2.5 sm:p-3 text-center transition flex flex-col items-center justify-center shadow-xs group"
                                   title="Buka kembali modul materi yang telah dipelajari">
                                    <span class="text-white font-bold text-xs flex items-center justify-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span class="font-bold text-xs text-white mt-0.5">Bahan Ajar</span>
                                    <span class="text-[10px] text-white/80">Selesai</span>
                                </a>
                            @else
                                {{-- Bahan Ajar Belum Selesai (Mulai Baca) -> MAROON UTAMA --}}
                                <a href="{{ route('portal.materi.show', $materi) }}"
                                   class="bg-[#7B1038] hover:bg-[#5E0C2B] text-white rounded-xl p-2.5 sm:p-3 text-center transition flex flex-col items-center justify-center shadow-xs group">
                                    <svg class="w-3.5 h-3.5 mb-0.5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    <span class="font-bold text-xs text-white mt-0.5">Bahan Ajar</span>
                                    <span class="text-[10px] text-white/80">Mulai Baca</span>
                                </a>
                            @endif
                        @else
                            {{-- Terkunci (Pre-Test Dulu) -> ABU-ABU --}}
                            <div class="bg-gray-100 rounded-xl p-2.5 sm:p-3 text-center flex flex-col items-center justify-center cursor-not-allowed text-gray-400 border border-gray-200"
                                 title="Kerjakan Pre-Test terlebih dahulu untuk membuka bahan ajar">
                                <svg class="w-3.5 h-3.5 mb-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span class="font-bold text-xs text-gray-500 mt-0.5">Bahan Ajar</span>
                                <span class="text-[10px] text-gray-400">Pre-Test Dulu</span>
                            </div>
                        @endif

                        {{-- Kolom 3: POST-TEST --}}
                        @if (! $materi->pretestDone)
                            {{-- Terkunci karena Pre-Test belum selesai -> ABU-ABU --}}
                            <div class="bg-gray-100 rounded-xl p-2.5 sm:p-3 text-center flex flex-col items-center justify-center cursor-not-allowed text-gray-400 border border-gray-200"
                                 title="Pre-Test harus diselesaikan sebelum dapat mengikuti Post-Test">
                                <svg class="w-3.5 h-3.5 mb-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span class="font-bold text-xs text-gray-500 mt-0.5">Post-Test</span>
                                <span class="text-[10px] text-gray-400">Terkunci</span>
                            </div>
                        @elseif ($materi->posttestDone)
                            {{-- Post-Test Selesai -> MAROON UTAMA --}}
                            <a href="{{ route('portal.quiz.index', $materi) }}"
                               class="bg-[#7B1038] hover:bg-[#5E0C2B] text-white rounded-xl p-2.5 sm:p-3 text-center transition flex flex-col items-center justify-center shadow-xs group"
                               title="Lihat riwayat Post-Test">
                                <span class="text-white font-bold text-xs flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    {{ $materi->posttestAttempt->total_poin ?? '100' }}/100
                                </span>
                                <span class="font-bold text-xs text-white mt-0.5">Post-Test</span>
                                <span class="text-[10px] text-white/80">Sudah Selesai</span>
                            </a>
                        @elseif ($materi->hasPosttest)
                            {{-- Post-Test Belum Dikerjakan (Siap Diuji) -> MAROON SAMAR --}}
                            <a href="{{ route('portal.quiz.index', $materi) }}"
                               class="bg-[#7B1038]/10 hover:bg-[#7B1038]/20 text-[#7B1038] border border-[#7B1038]/25 rounded-xl p-2.5 sm:p-3 text-center transition flex flex-col items-center justify-center group">
                                <svg class="w-3.5 h-3.5 mb-0.5 text-[#7B1038]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="font-bold text-xs text-[#7B1038] mt-0.5">Post-Test</span>
                                <span class="text-[10px] text-[#7B1038]/80 font-medium">Siap Diuji</span>
                            </a>
                        @else
                            {{-- Belum ada Post-Test -> ABU-ABU --}}
                            <div class="bg-gray-100 rounded-xl p-2.5 sm:p-3 text-center flex flex-col items-center justify-center text-gray-400 border border-gray-200">
                                <svg class="w-3.5 h-3.5 mb-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="font-bold text-xs text-gray-500 mt-0.5">Post-Test</span>
                                <span class="text-[10px] text-gray-400">Belum Tersedia</span>
                            </div>
                        @endif

                    </div>

                </div>
            @endforeach
        </div>

        {{-- Pesan Ketika Filter Tidak Memiliki Data --}}
        <div id="emptyFilterMsg" class="hidden bg-white rounded-2xl p-10 text-center shadow-xs">
            <div class="text-3xl mb-2">📂</div>
            <h3 class="text-base font-bold text-[#1F2937]">Tidak Ada Materi</h3>
            <p class="text-xs text-[#6B7280] mt-1">Tidak ditemukan materi dengan kategori status ini.</p>
        </div>
    @endif

</div>

@push('scripts')
<script>
    function filterMateri(status, buttonEl) {
        // Update styling tombol tab aktif (Active: Maroon #7B1038 & White, Inactive: text-[#5E0C2B] hover:bg-[#7B1038]/10)
        const activeClasses = ['bg-[#7B1038]', 'text-white', 'font-semibold', 'shadow-xs'];
        const inactiveClasses = ['text-[#5E0C2B]', 'hover:text-[#7B1038]', 'hover:bg-[#7B1038]/10'];

        document.querySelectorAll('.filter-tab-btn').forEach(btn => {
            btn.classList.remove('active', ...activeClasses);
            btn.classList.add(...inactiveClasses);
        });

        buttonEl.classList.remove(...inactiveClasses);
        buttonEl.classList.add('active', ...activeClasses);

        // Tampilkan/sembunyikan kartu materi
        const cards = document.querySelectorAll('.materi-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardStatus = card.getAttribute('data-status');
            if (status === 'semua' || cardStatus === status) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        // Tampilkan pesan kosong jika tidak ada yang cocok
        const emptyMsg = document.getElementById('emptyFilterMsg');
        if (emptyMsg) {
            if (visibleCount === 0) {
                emptyMsg.classList.remove('hidden');
            } else {
                emptyMsg.classList.add('hidden');
            }
        }
    }
</script>
@endpush
@endsection
