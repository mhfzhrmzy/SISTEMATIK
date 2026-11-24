@extends('layouts.app')

@section('title', 'Bahan Ajar Aktif — Portal Siswa')

@section('content')
<div class="space-y-4 sm:space-y-5">

    {{-- Header Bagian --}}
    <div class="pt-0.5 sm:pt-1">
        <h1 class="text-xl sm:text-2xl font-bold text-[var(--text-gray-900)] text-gradient-gray tracking-normal leading-tight">
            Bahan Ajar Aktif
        </h1>
    </div>

    {{-- 3. Daftar Kartu Materi (Grid 2 Kolom Responsif Sesuai Referensi) --}}
    @if ($materis->isEmpty())
        <x-card class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-gray-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-[var(--text-gray-900)] mb-1">Belum Ada Materi</h3>
            <p class="text-sm text-gray-600">Guru belum menambahkan materi untuk kelas dan jurusan Anda.</p>
        </x-card>
    @else
        <div id="materiGrid" class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 gap-6">
            @foreach ($materis as $materi)
                <x-card class="materi-card hover:shadow-md transition-all duration-200 flex flex-col justify-between">

                    <div>
                        {{-- Baris Atas: Kode Modul --}}
                        <div class="mb-3">
                            <span class="inline-block px-3 py-1 rounded-lg text-sm font-bold bg-gray-100 text-gray-600 tracking-wide">
                                {{ $materi->kode_modul }}
                            </span>
                        </div>

                        {{-- Judul Materi --}}
                        <h2 class="text-lg sm:text-xl font-bold text-[var(--text-gray-900)] mb-2 leading-snug">
                            {{ $materi->judul_materi }}
                        </h2>

                        {{-- Deskripsi Materi --}}
                        <p class="text-sm text-gray-600 leading-relaxed line-clamp-2 mb-5">
                            {{ $materi->isi_materi ?: 'Membahas pengenalan konsep dasar, penerapan langsung di bidang keahlian, latihan mandiri, dan evaluasi pemahaman standar industri.' }}
                        </p>

                        {{-- Kartu Guru Pengampu --}}
                        <div class="bg-[#F8F9FA] rounded-xl p-3 flex items-center gap-3 mb-5">
                            <div class="w-9 h-9 rounded-full bg-[#7B1038] text-white flex items-center justify-center shrink-0 shadow-xs">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-sm font-bold text-gray-600 uppercase tracking-wider block">
                                    GURU PENGAMPU
                                </span>
                                <span class="text-sm font-semibold text-gray-700 truncate block" title="{{ $materi->guru?->nama_lengkap ?? 'Guru Pengampu' }}">
                                    {{ $materi->guru?->nama_lengkap ?? 'Guru Pengampu' }}
                                </span>
                            </div>
                        </div>

                        {{-- Progress Kemajuan Belajar --}}
                        <div class="mb-5">
                            <div class="flex items-center justify-between text-sm font-semibold mb-2">
                                <span class="text-gray-600">Kemajuan Belajar</span>
                                <span class="text-gray-700 font-bold">{{ $materi->progress }}% Selesai</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#7B1038] rounded-full transition-all duration-300" style="width: {{ $materi->progress }}%;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Tiga Tombol Aksi di Bawah (Pre-test | Bahan Ajar | Post-test) --}}
                    <div class="grid grid-cols-3 gap-2.5 pt-2">

                        {{-- Kolom 1: PRE-TEST --}}
                        @if ($materi->pretestDone && $materi->hasPretest)
                            {{-- Pre-Test Selesai -> MAROON UTAMA --}}
                            <a href="{{ route('portal.quiz.index', $materi) }}"
                               class="bg-[#7B1038] hover:bg-[#5E0C2B] text-white rounded-2xl p-3 text-center transition flex flex-col items-center justify-center shadow-xs group"
                               title="Lihat riwayat Pre-Test">
                                <span class="font-bold text-sm text-white">Pre-test</span>
                                <span class="text-sm text-white/80 mt-0.5">Selesai</span>
                            </a>
                        @elseif ($materi->hasPretest)
                            {{-- Pre-Test Belum Dikerjakan -> BORDER MAROON ELEGAN --}}
                            <a href="{{ route('portal.quiz.kerjakan', $materi->pretest) }}"
                               class="bg-white hover:bg-[#7B1038] text-[var(--text-gray-700)] hover:text-white border-2 border-[#7B1038] rounded-xl p-3 text-center transition flex flex-col items-center justify-center group shadow-2xs">
                                <span class="font-bold text-sm text-[var(--text-gray-700)] group-hover:text-white transition">Pre-test</span>
                                <span class="text-sm text-gray-600 group-hover:text-white/90 font-medium transition mt-0.5">Mulai</span>
                            </a>
                        @else
                            {{-- Tidak ada Pre-Test (Bebas Akses) -> NETRAL --}}
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-center flex flex-col items-center justify-center text-gray-600">
                                <span class="font-bold text-sm text-[var(--text-gray-700)]">Pre-test</span>
                                <span class="text-sm text-gray-600 mt-0.5">Bebas Akses</span>
                            </div>
                        @endif

                        {{-- Kolom 2: BAHAN AJAR / LANJUT MATERI --}}
                        @if ($materi->pretestDone)
                            @if ($materi->isMateriSelesai)
                                {{-- Bahan Ajar Selesai Dibaca -> MAROON UTAMA --}}
                                <a href="{{ route('portal.materi.show', $materi) }}"
                                   class="bg-[#7B1038] hover:bg-[#5E0C2B] text-white rounded-2xl p-3 text-center transition flex flex-col items-center justify-center shadow-xs group"
                                   title="Buka kembali modul materi yang telah dipelajari">
                                    <span class="font-bold text-sm text-white">Bahan Ajar</span>
                                    <span class="text-sm text-white/80 mt-0.5">Selesai</span>
                                </a>
                            @else
                                {{-- Bahan Ajar Belum Selesai (Mulai Baca) -> MAROON UTAMA --}}
                                <a href="{{ route('portal.materi.show', $materi) }}"
                                   class="bg-[#7B1038] hover:bg-[#5E0C2B] text-white rounded-2xl p-3 text-center transition flex flex-col items-center justify-center shadow-xs group">
                                    <span class="font-bold text-sm text-white">Bahan Ajar</span>
                                    <span class="text-sm text-white/80 mt-0.5">Baca</span>
                                </a>
                            @endif
                        @else
                            {{-- Terkunci (Pre-Test Dulu) -> ABU-ABU --}}
                            <div class="bg-gray-100 rounded-xl p-3 text-center flex flex-col items-center justify-center cursor-not-allowed text-gray-600 border border-gray-200"
                                 title="Kerjakan Pre-Test terlebih dahulu untuk membuka bahan ajar">
                                <span class="font-bold text-sm text-gray-600">Bahan Ajar</span>
                                <span class="text-sm text-gray-600 mt-0.5">Terkunci</span>
                            </div>
                        @endif

                        {{-- Kolom 3: POST-TEST --}}
                        @if (! $materi->pretestDone)
                            {{-- Terkunci karena Pre-Test belum selesai -> ABU-ABU --}}
                            <div class="bg-gray-100 rounded-xl p-3 text-center flex flex-col items-center justify-center cursor-not-allowed text-gray-600 border border-gray-200"
                                 title="Pre-Test harus diselesaikan sebelum dapat mengikuti Post-Test">
                                <span class="font-bold text-sm text-gray-600">Post-test</span>
                                <span class="text-sm text-gray-600 mt-0.5">Terkunci</span>
                            </div>
                        @elseif ($materi->posttestDone)
                            {{-- Post-Test Selesai -> MAROON UTAMA --}}
                            <a href="{{ route('portal.quiz.index', $materi) }}"
                               class="bg-[#7B1038] hover:bg-[#5E0C2B] text-white rounded-2xl p-3 text-center transition flex flex-col items-center justify-center shadow-xs group"
                               title="Lihat riwayat Post-Test">
                                <span class="font-bold text-sm text-white">Post-test</span>
                                <span class="text-sm text-white/80 mt-0.5">Selesai</span>
                            </a>
                        @elseif ($materi->hasPosttest)
                            {{-- Post-Test Belum Dikerjakan (Siap Diuji) -> BORDER MAROON ELEGAN --}}
                            <a href="{{ route('portal.quiz.index', $materi) }}"
                               class="bg-white hover:bg-[#7B1038] text-[var(--text-gray-700)] hover:text-white border-2 border-[#7B1038] rounded-xl p-3 text-center transition flex flex-col items-center justify-center group shadow-2xs">
                                <span class="font-bold text-sm text-[var(--text-gray-700)] group-hover:text-white transition">Post-test</span>
                                <span class="text-sm text-gray-600 group-hover:text-white/90 font-medium transition mt-0.5">Mulai</span>
                            </a>
                        @else
                            {{-- Belum ada Post-Test -> ABU-ABU --}}
                            <div class="bg-gray-100 rounded-xl p-3 text-center flex flex-col items-center justify-center text-gray-600 border border-gray-200">
                                <span class="font-bold text-sm text-gray-600">Post-test</span>
                                <span class="text-sm text-gray-600 mt-0.5">Belum Ada</span>
                            </div>
                        @endif

                    </div>

                </x-card>
            @endforeach
        </div>
    @endif

</div>
@endsection
