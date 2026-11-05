@extends('layouts.app')

@section('title', 'Kelola Quiz')

@section('content')
<div class="space-y-6">

    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#7B1038]/10 text-[#7B1038]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Kelola Quiz Pembelajaran</h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-500">Buat dan atur jadwal kuis Pre-Test &amp; Post-Test untuk menguji capaian belajar siswa pada setiap materi.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-[#7B1038]/10 text-[#7B1038] border border-[#7B1038]/20 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-[#7B1038]"></span>
                {{ $quizzes->count() }} Quiz Dibuat
            </span>
        </div>
    </div>

    {{-- Notifikasi Sukses / Error --}}
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-xs sm:text-sm flex items-center gap-2 shadow-2xs">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-xs sm:text-sm flex items-center gap-2 shadow-2xs">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- FORM BUAT QUIZ BARU --}}
    <div class="bg-slate-50/70 rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs">
        <div class="flex items-center gap-2 border-b border-slate-200/70 pb-3 mb-4">
            <div class="w-7 h-7 rounded-lg bg-[#7B1038] text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                +
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900">Buat Quiz Baru</h2>
                <p class="text-[11px] text-slate-500">Pilih materi dan tentukan jenis kuis (Pre-Test atau Post-Test)</p>
            </div>
        </div>

        <form action="{{ route('admin.quiz.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4" id="form-create-quiz">
            @csrf

            {{-- 1. Pilih Materi --}}
            <div class="md:col-span-1">
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Pilih Materi <span class="text-rose-500">*</span>
                </label>
                <select name="id_materi"
                        id="select-materi"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
                    <option value="">-- Pilih Materi --</option>
                    @foreach ($materis as $m)
                        <option value="{{ $m->id_materi }}" {{ old('id_materi') == $m->id_materi ? 'selected' : '' }}>
                            {{ $m->judul_materi }} (Kelas {{ str_replace(' ', '.', $m->kelas) }})
                        </option>
                    @endforeach
                </select>
                @error('id_materi')
                    <p class="text-rose-600 text-[11px] mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- 2. Tipe Test --}}
            <div class="md:col-span-1">
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Tipe Test <span class="text-rose-500">*</span>
                </label>
                <select name="tipe_test"
                        id="select-tipe"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
                    <option value="pretest" id="opt-pretest">Pre-Test</option>
                    <option value="posttest" id="opt-posttest">Post-Test</option>
                </select>
                @error('tipe_test')
                    <p class="text-rose-600 text-[11px] mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="tipe-info" class="text-[11px] text-amber-700 font-medium mt-1 hidden"></p>
            </div>

            {{-- 3. Timer Total --}}
            <div class="md:col-span-1">
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Timer Pengerjaan <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input type="number"
                           name="timer"
                           id="input-timer"
                           value="{{ old('timer', 30) }}"
                           min="1"
                           max="120"
                           required
                           placeholder="30"
                           class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 pr-14 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-slate-400 font-semibold pointer-events-none">Menit</span>
                </div>
                <p id="timer-help" class="text-slate-400 text-[10px] mt-1">Maksimal 120 menit</p>
                @error('timer')
                    <p class="text-rose-600 text-[11px] mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- 4. Tanggal Pelaksanaan --}}
            <div class="md:col-span-1">
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Tanggal Pelaksanaan <span class="text-rose-500">*</span>
                </label>
                <input type="date"
                       name="tanggal"
                       id="input-tanggal"
                       value="{{ old('tanggal', date('Y-m-d')) }}"
                       min="{{ date('Y-m-d') }}"
                       readonly
                       class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold bg-slate-100 text-slate-500 cursor-not-allowed">
                <p id="tanggal-help" class="text-slate-400 text-[10px] mt-1">Otomatis, tidak dapat diubah</p>
                @error('tanggal')
                    <p class="text-rose-600 text-[11px] mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Blok Khusus Post-Test: Jam Mulai & Estimasi Ditutup --}}
            <div id="blok-posttest" class="md:col-span-4 grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs hidden">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Jam Mulai Post-Test <span class="text-rose-500">*</span>
                    </label>
                    @php
                        $oldJamParts = explode(':', old('jam_mulai', '08:00'));
                        $selectedJam = str_pad($oldJamParts[0] ?? '08', 2, '0', STR_PAD_LEFT);
                        $selectedMenit = str_pad($oldJamParts[1] ?? '00', 2, '0', STR_PAD_LEFT);
                    @endphp
                    <div class="flex items-center border border-slate-300 rounded-xl px-3.5 py-2 bg-white focus-within:ring-2 focus-within:ring-[#7B1038]/20 focus-within:border-[#7B1038] transition">
                        <div class="flex items-center gap-1">
                            <select id="select-jam" class="bg-transparent text-xs font-bold text-slate-800 focus:outline-none cursor-pointer">
                                @for ($h = 0; $h < 24; $h++)
                                    @php $valH = sprintf('%02d', $h); @endphp
                                    <option value="{{ $valH }}" {{ $selectedJam === $valH ? 'selected' : '' }}>{{ $valH }}</option>
                                @endfor
                            </select>
                            <span class="text-xs font-bold text-slate-400">:</span>
                            <select id="select-menit" class="bg-transparent text-xs font-bold text-slate-800 focus:outline-none cursor-pointer">
                                @for ($m = 0; $m < 60; $m++)
                                    @php $valM = sprintf('%02d', $m); @endphp
                                    <option value="{{ $valM }}" {{ $selectedMenit === $valM ? 'selected' : '' }}>{{ $valM }}</option>
                                @endfor
                            </select>
                        </div>
                        <span class="ml-auto text-[10px] font-bold text-[#7B1038] bg-[#7B1038]/10 px-2 py-0.5 rounded-md">WIB</span>
                        <input type="hidden" name="jam_mulai" id="input-jam-mulai" value="{{ old('jam_mulai', '08:00') }}">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Pilih jam dan menit dimulainya pengerjaan (24 jam)</p>
                    @error('jam_mulai')
                        <p class="text-rose-600 text-[11px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Estimasi Post-Test Ditutup
                    </label>
                    <input type="text"
                           id="input-estimasi-tutup"
                           readonly
                           disabled
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold bg-slate-100 text-slate-600 cursor-not-allowed"
                           placeholder="—">
                    <p class="text-[10px] text-slate-400 mt-1">Otomatis dihitung dari (Jam Mulai + Durasi Timer)</p>
                </div>
            </div>

            {{-- Banner Info Ketentuan Poin & Jumlah Quiz --}}
            <div class="md:col-span-4 bg-amber-50/80 border border-amber-200 rounded-xl p-3 text-xs text-amber-900 flex items-start gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="space-y-0.5">
                    <p>Setiap quiz otomatis bernilai total <strong>100 Poin</strong> (bobot nilai per soal dihitung otomatis dari 100 dibagi total jumlah butir soal).</p>
                    <p class="text-[11px] text-amber-800">Setiap materi pembelajaran hanya dapat memiliki <strong>1 Pre-Test</strong> dan <strong>1 Post-Test</strong>.</p>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="md:col-span-4 pt-1">
                <button type="submit"
                        id="btn-submit"
                        class="w-full flex items-center justify-center gap-2 bg-[#7B1038] hover:bg-[#5E0C2B] text-white font-bold py-2.5 px-4 rounded-xl shadow-xs hover:shadow transition duration-150 cursor-pointer text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Simpan &amp; Buat Quiz
                </button>
            </div>
        </form>
    </div>

    {{-- TABEL DAFTAR QUIZ PEMBELAJARAN --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs space-y-4">

        {{-- Toolbar Atas: Judul & Input Pencarian Real-time --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#7B1038]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Daftar Quiz Pembelajaran
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar semua kuis materi yang tersedia beserta jadwal dan jumlah butir soal.</p>
            </div>

            {{-- Input Pencarian Realtime --}}
            <div class="relative w-full sm:w-64">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       id="search-quiz"
                       placeholder="Cari materi atau kuis..."
                       class="w-full pl-9 pr-3.5 py-1.5 text-xs rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
            </div>
        </div>

        {{-- Wrapper Tabel Responsif --}}
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-xs" id="table-quiz">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase tracking-wider font-bold text-[11px]">
                        <th class="px-4 py-3">Materi</th>
                        <th class="px-4 py-3">Tipe Test</th>
                        <th class="px-4 py-3">Total Poin</th>
                        <th class="px-4 py-3">Jadwal &amp; Waktu</th>
                        <th class="px-4 py-3 text-center">Jumlah Soal</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($quizzes as $quiz)
                        <tr class="hover:bg-slate-50/70 transition duration-75">

                            {{-- Judul Materi & Kelas --}}
                            <td class="px-4 py-3.5 max-w-[240px]">
                                <div class="font-bold text-slate-900 line-clamp-1" title="{{ $quiz->materi->judul_materi ?? '-' }}">
                                    {{ $quiz->materi->judul_materi ?? '-' }}
                                </div>
                                @if($quiz->materi && $quiz->materi->kelas)
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Kelas {{ str_replace(' ', '.', $quiz->materi->kelas) }} &bull; {{ $quiz->materi->jurusan ?? '-' }}
                                    </div>
                                @endif
                            </td>

                            {{-- Tipe Test (Pre-Test / Post-Test) --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($quiz->tipe_test == 'pretest')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pre-Test
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Post-Test
                                    </span>
                                @endif
                            </td>

                            {{-- Total Poin & Bobot per Soal --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-bold text-[#7B1038]">100 Poin</span>
                                @if($quiz->soal->count() > 0)
                                    <span class="block text-[10px] text-slate-400 font-medium mt-0.5">
                                        ({{ round(100 / $quiz->soal->count(), 1) }} poin / soal)
                                    </span>
                                @else
                                    <span class="block text-[10px] text-slate-400 font-medium mt-0.5">(0 butir soal)</span>
                                @endif
                            </td>

                            {{-- Jadwal & Durasi --}}
                            <td class="px-4 py-3.5 whitespace-nowrap text-slate-600 font-medium">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-1.5 text-slate-700">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        @if($quiz->tipe_test === 'posttest' && $quiz->jadwalLengkap())
                                            <span>{{ $quiz->waktuMulai()->translatedFormat('d M Y') }}</span>
                                        @else
                                            <span>{{ \Carbon\Carbon::parse($quiz->tanggal)->translatedFormat('d M Y') }}</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-400 pl-5">
                                        @if($quiz->tipe_test === 'posttest' && $quiz->jadwalLengkap())
                                            {{ $quiz->waktuMulai()->format('H:i') }} – {{ $quiz->waktuSelesai()->format('H:i') }} WIB ({{ $quiz->timer }} mnt)
                                        @else
                                            Durasi: {{ $quiz->timer }} Menit
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Jumlah Soal --}}
                            <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-block px-2.5 py-1 rounded-lg {{ $quiz->soal->count() > 0 ? 'bg-slate-100 text-slate-700 font-bold' : 'bg-rose-50 text-rose-600 font-bold border border-rose-200' }} text-[11px]">
                                    {{ $quiz->soal->count() }} Soal
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                <div class="inline-flex items-center justify-end gap-1.5">

                                    {{-- Tombol Kelola Soal --}}
                                    <a href="{{ route('admin.soal.index', $quiz) }}"
                                       title="Kelola Butir Soal"
                                       class="inline-flex items-center gap-1.5 bg-[#7B1038]/10 hover:bg-[#7B1038] text-[#7B1038] hover:text-white font-bold px-3 py-1.5 rounded-xl border border-[#7B1038]/20 text-xs transition duration-150 shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Kelola Soal ({{ $quiz->soal->count() }})</span>
                                    </a>

                                    {{-- Tombol Ubah Jadwal (Khusus Post-test) --}}
                                    @if($quiz->tipe_test === 'posttest')
                                        <button type="button"
                                                onclick="openJadwalModal({{ $quiz->id_quiz }}, '{{ $quiz->tanggal?->format('Y-m-d') }}', '{{ $quiz->jam_mulai }}', {{ $quiz->timer }})"
                                                title="Ubah Jadwal Post-Test"
                                                class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-2.5 py-1.5 rounded-xl border border-indigo-200 text-xs transition duration-150 shadow-2xs cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Jadwal</span>
                                        </button>
                                    @endif

                                    {{-- Tombol Hapus Quiz --}}
                                    <form action="{{ route('admin.quiz.destroy', $quiz) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kuis ini? Semua soal dan riwayat skor pengerjaan siswa akan ikut terhapus.')"
                                          class="inline m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Hapus Quiz"
                                                class="p-1.5 rounded-xl text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition duration-150 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    <p class="font-semibold text-xs text-slate-500">Belum ada quiz yang dibuat.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Gunakan formulir di atas untuk menambahkan Pre-Test atau Post-Test.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Tabel --}}
        <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100">
            <span>Catatan: Soal dapat ditambahkan setelah kuis berhasil dibuat melalui tombol "Kelola Soal".</span>
            <span id="match-count" class="font-medium text-slate-500"></span>
        </div>
    </div>
</div>

{{-- MODAL UBAH JADWAL POST-TEST --}}
<div id="modal-jadwal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-2xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-900 text-base flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
                Ubah Jadwal Post-Test
            </h3>
            <button type="button" onclick="closeJadwalModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2 cursor-pointer">&times;</button>
        </div>

        @if($errors->has('jadwal'))
            <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs px-3.5 py-2.5 rounded-xl">
                {{ $errors->first('jadwal') }}
            </div>
        @endif

        <form id="form-jadwal" method="POST" class="space-y-3.5">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pelaksanaan</label>
                <input type="date"
                       name="tanggal"
                       id="modal-tanggal"
                       min="{{ date('Y-m-d') }}"
                       required
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
                <p class="text-[10px] text-slate-400 mt-1">Hanya hari ini atau tanggal masa depan yang diperbolehkan</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Jam Mulai</label>
                <div class="flex items-center border border-slate-300 rounded-xl px-3.5 py-2 bg-white focus-within:ring-2 focus-within:ring-[#7B1038]/20 focus-within:border-[#7B1038] transition">
                    <div class="flex items-center gap-1">
                        <select id="modal-select-jam" class="bg-transparent text-xs font-bold text-slate-800 focus:outline-none cursor-pointer">
                            @for ($h = 0; $h < 24; $h++)
                                @php $valH = sprintf('%02d', $h); @endphp
                                <option value="{{ $valH }}">{{ $valH }}</option>
                            @endfor
                        </select>
                        <span class="text-xs font-bold text-slate-400">:</span>
                        <select id="modal-select-menit" class="bg-transparent text-xs font-bold text-slate-800 focus:outline-none cursor-pointer">
                            @for ($m = 0; $m < 60; $m++)
                                @php $valM = sprintf('%02d', $m); @endphp
                                <option value="{{ $valM }}">{{ $valM }}</option>
                            @endfor
                        </select>
                    </div>
                    <span class="ml-auto text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md">WIB</span>
                    <input type="hidden" name="jam_mulai" id="modal-jam-mulai">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Format 24 jam (WIB)</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Timer Pengerjaan (Menit)</label>
                <input type="number"
                       name="timer"
                       id="modal-timer"
                       min="1"
                       max="120"
                       required
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Estimasi Post-Test Ditutup</label>
                <input type="text"
                       id="modal-estimasi-tutup"
                       readonly
                       disabled
                       class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold bg-slate-100 text-slate-600 cursor-not-allowed"
                       placeholder="—">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button"
                        onclick="closeJadwalModal()"
                        class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-bold text-white bg-[#7B1038] hover:bg-[#5E0C2B] rounded-xl shadow-xs hover:shadow transition cursor-pointer">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const existing = @json($existingCombinations);
    const selectMateri = document.getElementById('select-materi');
    const selectTipe = document.getElementById('select-tipe');
    const optPretest = document.getElementById('opt-pretest');
    const optPosttest = document.getElementById('opt-posttest');
    const tipeInfo = document.getElementById('tipe-info');
    const btnSubmit = document.getElementById('btn-submit');

    const inputTanggal = document.getElementById('input-tanggal');
    const inputJamMulai = document.getElementById('input-jam-mulai');
    const selectJam = document.getElementById('select-jam');
    const selectMenit = document.getElementById('select-menit');
    const inputTimer = document.getElementById('input-timer');
    const inputEstimasiTutup = document.getElementById('input-estimasi-tutup');
    const blokPosttest = document.getElementById('blok-posttest');
    const timerHelp = document.getElementById('timer-help');
    const tanggalHelp = document.getElementById('tanggal-help');

    const bulanIndo = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    function syncJamMulai() {
        if (selectJam && selectMenit && inputJamMulai) {
            inputJamMulai.value = selectJam.value + ':' + selectMenit.value;
            updateEstimasi();
        }
    }

    if (selectJam && selectMenit) {
        selectJam.addEventListener('change', syncJamMulai);
        selectMenit.addEventListener('change', syncJamMulai);
    }

    function formatEstimasi(tanggal, jamMulai, timer) {
        if (!tanggal || !jamMulai || !timer || timer < 1) return '—';
        const dt = new Date(tanggal + 'T' + jamMulai + ':00');
        if (isNaN(dt.getTime())) return '—';
        dt.setMinutes(dt.getMinutes() + parseInt(timer));
        const d = dt.getDate();
        const m = bulanIndo[dt.getMonth()];
        const y = dt.getFullYear();
        const hh = String(dt.getHours()).padStart(2,'0');
        const mm = String(dt.getMinutes()).padStart(2,'0');
        return d + ' ' + m + ' ' + y + ' · ' + hh + ':' + mm + ' WIB';
    }

    function updateEstimasi() {
        inputEstimasiTutup.value = formatEstimasi(inputTanggal.value, inputJamMulai.value, inputTimer.value);
    }

    function togglePosttestFields() {
        const isPosttest = selectTipe.value === 'posttest';
        blokPosttest.classList.toggle('hidden', !isPosttest);

        const todayStr = new Date().toLocaleDateString('en-CA');

        if (isPosttest) {
            inputTanggal.removeAttribute('readonly');
            inputTanggal.classList.remove('bg-slate-100', 'text-slate-500', 'cursor-not-allowed', 'border-slate-200');
            inputTanggal.classList.add('border-slate-300', 'bg-white', 'focus:ring-2', 'focus:ring-[#7B1038]/20', 'focus:border-[#7B1038]');
            inputTanggal.setAttribute('name', 'tanggal');
            inputTanggal.min = todayStr;
            if (!inputTanggal.value || inputTanggal.value < todayStr) {
                inputTanggal.value = todayStr;
            }
            tanggalHelp.textContent = 'Pilih tanggal post-test dilaksanakan';
            timerHelp.textContent = 'Maks. 120 menit';
            syncJamMulai();
        } else {
            inputTanggal.setAttribute('readonly', '');
            inputTanggal.value = todayStr;
            inputTanggal.classList.add('bg-slate-100', 'text-slate-500', 'cursor-not-allowed', 'border-slate-200');
            inputTanggal.classList.remove('border-slate-300', 'bg-white', 'focus:ring-2', 'focus:ring-[#7B1038]/20', 'focus:border-[#7B1038]');
            inputTanggal.removeAttribute('name');
            inputTanggal.removeAttribute('min');
            tanggalHelp.textContent = 'Otomatis, tidak dapat diubah';
            timerHelp.textContent = 'Maks. 120 menit';
        }
    }

    function updateTipeOptions() {
        const materiId = selectMateri.value;
        const usedTypes = existing[materiId] || [];

        const pretestUsed = usedTypes.includes('pretest');
        const posttestUsed = usedTypes.includes('posttest');

        optPretest.disabled = pretestUsed;
        optPosttest.disabled = posttestUsed;
        optPretest.textContent = 'Pre-Test' + (pretestUsed ? ' (sudah ada)' : '');
        optPosttest.textContent = 'Post-Test' + (posttestUsed ? ' (sudah ada)' : '');

        // Auto-select first available option
        if (pretestUsed && !posttestUsed) {
            selectTipe.value = 'posttest';
        } else if (!pretestUsed) {
            selectTipe.value = 'pretest';
        }

        const allUsed = pretestUsed && posttestUsed;
        if (allUsed && materiId) {
            tipeInfo.textContent = '⚠️ Materi ini sudah memiliki pre-test dan post-test.';
            tipeInfo.classList.remove('hidden');
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            tipeInfo.classList.add('hidden');
            btnSubmit.disabled = false;
            btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        togglePosttestFields();
    }

    selectMateri.addEventListener('change', updateTipeOptions);
    selectTipe.addEventListener('change', togglePosttestFields);
    inputTanggal.addEventListener('change', function() {
        const todayStr = new Date().toLocaleDateString('en-CA');
        if (selectTipe.value === 'posttest' && this.value && this.value < todayStr) {
            alert('Tanggal post-test tidak boleh tanggal yang sudah lewat.');
            this.value = todayStr;
        }
        updateEstimasi();
    });
    inputTimer.addEventListener('change', updateEstimasi);
    inputTimer.addEventListener('input', updateEstimasi);

    updateTipeOptions();

    // === Modal Jadwal ===
    const modalJadwal = document.getElementById('modal-jadwal');
    const formJadwal = document.getElementById('form-jadwal');
    const modalTanggal = document.getElementById('modal-tanggal');
    const modalJamMulai = document.getElementById('modal-jam-mulai');
    const modalSelectJam = document.getElementById('modal-select-jam');
    const modalSelectMenit = document.getElementById('modal-select-menit');
    const modalTimer = document.getElementById('modal-timer');
    const modalEstimasi = document.getElementById('modal-estimasi-tutup');

    function syncModalJamMulai() {
        if (modalSelectJam && modalSelectMenit && modalJamMulai) {
            modalJamMulai.value = modalSelectJam.value + ':' + modalSelectMenit.value;
            updateModalEstimasi();
        }
    }

    if (modalSelectJam && modalSelectMenit) {
        modalSelectJam.addEventListener('change', syncModalJamMulai);
        modalSelectMenit.addEventListener('change', syncModalJamMulai);
    }

    function updateModalEstimasi() {
        modalEstimasi.value = formatEstimasi(modalTanggal.value, modalJamMulai.value, modalTimer.value);
    }

    function openJadwalModal(quizId, tanggal, jamMulai, timer) {
        const todayStr = new Date().toLocaleDateString('en-CA');
        formJadwal.action = '/admin/quiz/' + quizId + '/jadwal';
        modalTanggal.min = todayStr;
        modalTanggal.value = (tanggal && tanggal >= todayStr) ? tanggal : todayStr;

        const cleanJam = (jamMulai || '08:00').substring(0, 5);
        const parts = cleanJam.split(':');
        if (modalSelectJam) modalSelectJam.value = (parts[0] || '08').padStart(2, '0');
        if (modalSelectMenit) modalSelectMenit.value = (parts[1] || '00').padStart(2, '0');
        syncModalJamMulai();

        modalTimer.value = timer || 30;
        updateModalEstimasi();
        modalJadwal.classList.remove('hidden');
        modalJadwal.classList.add('flex');
    }

    function closeJadwalModal() {
        modalJadwal.classList.add('hidden');
        modalJadwal.classList.remove('flex');
    }

    modalTanggal.addEventListener('change', function() {
        const todayStr = new Date().toLocaleDateString('en-CA');
        if (this.value && this.value < todayStr) {
            alert('Tanggal post-test tidak boleh tanggal yang sudah lewat.');
            this.value = todayStr;
        }
        updateModalEstimasi();
    });
    modalTimer.addEventListener('change', updateModalEstimasi);
    modalTimer.addEventListener('input', updateModalEstimasi);

    // === Filter Pencarian Realtime Quiz ===
    const searchQuiz = document.getElementById('search-quiz');
    const tableBody = document.querySelector('#table-quiz tbody');
    if (searchQuiz && tableBody) {
        const rows = tableBody.querySelectorAll('tr:not(#empty-row)');
        const matchCount = document.getElementById('match-count');

        searchQuiz.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            let visible = 0;

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if (query === '' || text.includes(query)) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (matchCount) {
                matchCount.textContent = query !== '' ? `Ditemukan: ${visible} dari ${rows.length}` : '';
            }
        });
    }
</script>
@endpush
@endsection
