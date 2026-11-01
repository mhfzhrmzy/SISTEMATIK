@extends('layouts.app')

@section('title', 'Materi Pembelajaran Aktif — Portal Siswa')

@section('content')
<div class="space-y-6">

    {{-- 1. Kartu Informasi Profil Akun Siswa (Elegan, Clean, Minimalis Maroon) --}}
    @php
        $siswaUser = $siswa ?? auth('siswa')->user();
    @endphp
    <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-5 relative overflow-hidden">
        {{-- Subtle accent bar maroon --}}
        <div class="absolute top-0 left-0 w-1.5 h-full bg-[#7B1038]"></div>

        {{-- Sisi Kiri: Foto Profil & Info Akun Siswa --}}
        <div class="flex items-center gap-4 sm:gap-5 pl-2">
            @if ($siswaUser->foto_profile)
                <img src="{{ asset('storage/' . $siswaUser->foto_profile) }}"
                     alt="{{ $siswaUser->nama_lengkap }}"
                     class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl object-cover ring-2 ring-[#7B1038]/20 shadow-xs shrink-0">
            @else
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-[#7B1038] to-[#5E0C2B] text-white flex items-center justify-center font-bold text-lg sm:text-xl shadow-xs shrink-0 tracking-wider">
                    {{ strtoupper(substr($siswaUser->nama_lengkap, 0, 2)) }}
                </div>
            @endif

            <div class="min-w-0 space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#7B1038]/10 text-[#7B1038]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#1F9254]"></span>
                        Siswa Aktif
                    </span>
                    <span class="text-xs text-[#6B7280] font-mono">
                        NISN: {{ $siswaUser->nisn }}
                    </span>
                </div>

                <h2 class="text-base sm:text-xl font-bold text-[#1F2937] truncate">
                    {{ $siswaUser->nama_lengkap }}
                </h2>

                <div class="flex items-center gap-2 text-xs text-[#6B7280] flex-wrap">
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-[#7B1038]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Kelas {{ $siswaUser->kelas }}
                    </span>
                    <span>•</span>
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-[#7B1038]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        {{ $siswaUser->jurusan }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Perolehan Poin & Tombol Profil --}}
        <div class="flex items-center justify-between md:justify-end gap-3 pt-3 md:pt-0 border-t md:border-t-0 border-gray-100 pl-2 md:pl-0">
            <div class="bg-[#7B1038]/5 rounded-xl px-4 py-2 text-left md:text-right">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-[#7B1038]">
                    Perolehan Poin
                </span>
                <span class="text-lg sm:text-xl font-black text-[#7B1038]">
                    {{ number_format($siswaUser->poin ?? 0, 0, ',', '.') }}
                    <span class="text-xs font-semibold text-[#6B7280]">Poin</span>
                </span>
            </div>

            <a href="{{ route('siswa.profil') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-[#7B1038] hover:bg-[#5E0C2B] text-white shadow-xs transition duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Lihat Profil</span>
            </a>
        </div>
    </div>

    {{-- 2. Header Bagian & Filter Tab --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-2">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#1F2937] tracking-tight">
                Materi Pembelajaran Aktif
            </h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">
                Pilih modul untuk mengakses pre-test, bahan ajar, dan evaluasi post-test.
            </p>
        </div>

        {{-- Segmented Tab Filter --}}
        <div class="inline-flex p-1 rounded-2xl bg-gray-100 self-start md:self-auto gap-1 text-xs sm:text-sm font-medium flex-wrap">
            <button type="button"
                    onclick="filterMateri('semua', this)"
                    class="filter-tab-btn active px-3.5 py-1.5 rounded-xl transition-all duration-150 bg-white text-[#1F2937] font-semibold shadow-xs">
                Semua Materi ({{ $countSemua }})
            </button>
            <button type="button"
                    onclick="filterMateri('belum-dimulai', this)"
                    class="filter-tab-btn px-3.5 py-1.5 rounded-xl transition-all duration-150 text-[#6B7280] hover:text-[#1F2937]">
                Belum Dimulai ({{ $countBelumDimulai }})
            </button>
            <button type="button"
                    onclick="filterMateri('sedang-berjalan', this)"
                    class="filter-tab-btn px-3.5 py-1.5 rounded-xl transition-all duration-150 text-[#6B7280] hover:text-[#1F2937]">
                Sedang Berjalan ({{ $countSedangBerjalan }})
            </button>
            <button type="button"
                    onclick="filterMateri('selesai', this)"
                    class="filter-tab-btn px-3.5 py-1.5 rounded-xl transition-all duration-150 text-[#6B7280] hover:text-[#1F2937]">
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
        <div id="materiGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach ($materis as $materi)
                <div class="materi-card bg-white rounded-2xl p-6 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between"
                     data-status="{{ $materi->statusKey }}">

                    <div>
                        {{-- Baris Atas: Kode Modul & Status Badge --}}
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-[#5E0C2B] tracking-wide">
                                {{ $materi->kode_modul }}
                            </span>

                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $materi->statusBadgeClass }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $materi->statusDotClass }}"></span>
                                {{ $materi->statusLabel }}
                            </span>
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
                            {{-- Pre-Test Selesai --}}
                            <a href="{{ route('portal.quiz.index', $materi) }}"
                               class="bg-slate-50 hover:bg-slate-100 rounded-xl p-2.5 sm:p-3 text-center transition flex flex-col items-center justify-center group"
                               title="Lihat riwayat Pre-Test">
                                <span class="text-[#1F9254] font-bold text-xs flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    {{ $materi->pretestAttempt->total_poin ?? '100' }}/100
                                </span>
                                <span class="font-bold text-xs text-[#1F2937] mt-1 group-hover:text-[#7B1038] transition-colors">Pre-Test</span>
                                <span class="text-[10px] text-[#6B7280]">Sudah Selesai</span>
                            </a>
                        @elseif ($materi->hasPretest)
                            {{-- Pre-Test Belum Dikerjakan (Aktif / Primary Maroon) --}}
                            <a href="{{ route('portal.quiz.kerjakan', $materi->pretest) }}"
                               class="bg-[#7B1038] hover:bg-[#5E0C2B] text-white rounded-xl p-2.5 sm:p-3 text-center transition flex flex-col items-center justify-center shadow-xs hover:shadow group">
                                <span class="text-xs">▶</span>
                                <span class="font-bold text-xs text-white mt-0.5">Pre-Test</span>
                                <span class="text-[10px] text-white/80">Mulai Sekarang</span>
                            </a>
                        @else
                            {{-- Tidak ada Pre-Test --}}
                            <div class="bg-gray-50 rounded-xl p-2.5 sm:p-3 text-center flex flex-col items-center justify-center text-gray-400">
                                <span class="text-xs">✓</span>
                                <span class="font-bold text-xs text-gray-500 mt-0.5">Pre-Test</span>
                                <span class="text-[10px] text-gray-400">Bebas Akses</span>
                            </div>
                        @endif

                        {{-- Kolom 2: BAHAN AJAR / LANJUT MATERI --}}
                        @if ($materi->pretestDone)
                            @if ($materi->isMateriSelesai)
                                <a href="{{ route('portal.materi.show', $materi) }}"
                                   class="bg-slate-50 hover:bg-slate-100 rounded-xl p-2.5 sm:p-3 text-center transition flex flex-col items-center justify-center group"
                                   title="Buka kembali modul materi yang telah dipelajari">
                                    <span class="text-[#1F9254] font-bold text-xs flex items-center justify-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Selesai
                                    </span>
                                    <span class="font-bold text-xs text-[#1F2937] mt-1 group-hover:text-[#7B1038] transition-colors">Bahan Ajar</span>
                                    <span class="text-[10px] text-[#6B7280]">Sudah Dibaca</span>
                                </a>
                            @else
                                <a href="{{ route('portal.materi.show', $materi) }}"
                                   class="bg-[#7B1038] hover:bg-[#5E0C2B] text-white rounded-xl p-2.5 sm:p-3 text-center transition flex flex-col items-center justify-center shadow-xs hover:shadow group">
                                    <span class="text-xs">📖</span>
                                    <span class="font-bold text-xs text-white mt-0.5">Bahan Ajar</span>
                                    <span class="text-[10px] text-white/80">{{ $materi->isDibuka ? 'Lanjut Baca' : 'Mulai Baca' }}</span>
                                </a>
                            @endif
                        @else
                            {{-- Terkunci (Pre-Test Dulu) --}}
                            <div class="bg-gray-100 rounded-xl p-2.5 sm:p-3 text-center flex flex-col items-center justify-center cursor-not-allowed text-gray-400"
                                 title="Kerjakan Pre-Test terlebih dahulu untuk membuka bahan ajar">
                                <span class="text-xs">🔒</span>
                                <span class="font-bold text-xs text-gray-500 mt-0.5">Bahan Ajar</span>
                                <span class="text-[10px] text-gray-400">Pre-Test Dulu</span>
                            </div>
                        @endif

                        {{-- Kolom 3: POST-TEST --}}
                        @if (! $materi->pretestDone)
                            {{-- Terkunci karena Pre-Test belum selesai --}}
                            <div class="bg-gray-100 rounded-xl p-2.5 sm:p-3 text-center flex flex-col items-center justify-center cursor-not-allowed text-gray-400"
                                 title="Pre-Test harus diselesaikan sebelum dapat mengikuti Post-Test">
                                <span class="text-xs">🔒</span>
                                <span class="font-bold text-xs text-gray-500 mt-0.5">Post-Test</span>
                                <span class="text-[10px] text-gray-400">Terkunci</span>
                            </div>
                        @elseif ($materi->posttestDone)
                            {{-- Post-Test Selesai --}}
                            <a href="{{ route('portal.quiz.index', $materi) }}"
                               class="bg-slate-50 hover:bg-slate-100 rounded-xl p-2.5 sm:p-3 text-center transition flex flex-col items-center justify-center group"
                               title="Lihat riwayat Post-Test">
                                <span class="text-[#1F9254] font-bold text-xs flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    {{ $materi->posttestAttempt->total_poin ?? '100' }}/100
                                </span>
                                <span class="font-bold text-xs text-[#1F2937] mt-1 group-hover:text-[#7B1038] transition-colors">Post-Test</span>
                                <span class="text-[10px] text-[#6B7280]">Sudah Selesai</span>
                            </a>
                        @elseif ($materi->hasPosttest)
                            {{-- Post-Test Siap Diuji (Terbuka) --}}
                            <a href="{{ route('portal.quiz.index', $materi) }}"
                               class="bg-slate-50 hover:bg-[#7B1038]/5 rounded-xl p-2.5 sm:p-3 text-center transition flex flex-col items-center justify-center border border-gray-100 group">
                                <span class="text-[10px] font-bold text-[#7B1038] uppercase tracking-wider">TERBUKA</span>
                                <span class="font-bold text-xs text-[#1F2937] mt-0.5 group-hover:text-[#7B1038] transition-colors">Post-Test</span>
                                <span class="text-[10px] text-[#6B7280]">Siap Diuji</span>
                            </a>
                        @else
                            {{-- Belum ada Post-Test --}}
                            <div class="bg-gray-50 rounded-xl p-2.5 sm:p-3 text-center flex flex-col items-center justify-center text-gray-400">
                                <span class="text-xs">⏳</span>
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
        // Update styling tombol tab aktif
        document.querySelectorAll('.filter-tab-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-[#1F2937]', 'font-semibold', 'shadow-xs');
            btn.classList.add('text-[#6B7280]');
        });

        buttonEl.classList.remove('text-[#6B7280]');
        buttonEl.classList.add('bg-white', 'text-[#1F2937]', 'font-semibold', 'shadow-xs');

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
