@extends('layouts.app')

@section('title', 'Kelola Soal Quiz')

@section('content')
<div class="space-y-6">

    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#7B1038]/10 text-[#7B1038]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Kelola Soal Quiz</h1>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-slate-500">
                <span>Materi:</span>
                <strong class="text-slate-800">{{ $quiz->materi->judul_materi }}</strong>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $quiz->tipe_test == 'pretest' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                    {{ ucfirst($quiz->tipe_test) }}
                </span>
                @if($quiz->materi->kelas)
                    <span class="text-slate-400">&bull;</span>
                    <span class="text-slate-600 font-medium">Kelas {{ str_replace(' ', '.', $quiz->materi->kelas) }}</span>
                @endif
            </div>
        </div>

        <a href="{{ route('admin.quiz.index') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition duration-150 self-start sm:self-auto">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Quiz
        </a>
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

    @if($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 p-4 text-xs sm:text-sm shadow-2xs">
            <p class="font-bold flex items-center gap-1.5 mb-1.5">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Terjadi kesalahan validasi:
            </p>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SECTION 1: IMPORT SPREADSHEET / CSV --}}
    @if($soals->isEmpty())
        <div class="bg-emerald-50/70 border border-emerald-200 rounded-2xl p-5 sm:p-6 shadow-2xs">
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-600 text-white font-bold text-xs shadow-2xs">
                    📊
                </span>
                <h3 class="text-sm font-bold text-emerald-950 flex items-center gap-2">
                    Import Soal dari Spreadsheet / CSV
                    <span class="text-[10px] bg-emerald-200/80 text-emerald-800 font-bold px-2 py-0.5 rounded-full border border-emerald-300">Hanya sekali</span>
                </h3>
            </div>
            <p class="text-xs text-emerald-800 mb-4 leading-relaxed">
                Unggah file CSV dari Excel atau Google Sheets untuk menambahkan banyak butir soal secara instan.
                <strong>Fitur ini hanya tersedia saat kuis masih belum memiliki soal.</strong>
                Setelah melakukan import, Anda tetap dapat menambah soal baru secara manual.
            </p>

            <form action="{{ route('admin.soal.import', $quiz) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                @csrf
                <input type="file"
                       name="csv_file"
                       accept=".csv"
                       required
                       class="text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 file:transition file:cursor-pointer border border-emerald-300 rounded-xl bg-white p-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">

                <button type="submit"
                        class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-xs transition duration-150 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload &amp; Import CSV
                </button>

                <a href="{{ route('admin.soal.template') }}"
                   class="inline-flex items-center gap-1.5 border border-emerald-600 text-emerald-700 bg-white hover:bg-emerald-50 text-xs font-bold px-3.5 py-2.5 rounded-xl transition duration-150 shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh Template CSV
                </a>
            </form>
        </div>
    @else
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex items-start gap-3 shadow-2xs">
            <span class="text-xl">🔒</span>
            <div>
                <p class="text-xs font-bold text-slate-800">Import CSV Ditutup</p>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    Kuis ini sudah memiliki <strong>{{ $soals->count() }} butir soal</strong>. Import massal via CSV hanya berlaku satu kali saat kuis masih kosong. Gunakan formulir manual di bawah untuk menambah soal baru.
                </p>
            </div>
        </div>
    @endif

    {{-- SECTION 2: FORM TAMBAH SOAL SECARA MANUAL --}}
    <div class="bg-slate-50/70 rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-200/70 pb-3">
            <div class="w-7 h-7 rounded-lg bg-[#7B1038] text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                +
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Tambah Soal Secara Manual</h3>
                <p class="text-[11px] text-slate-500">Tuliskan teks pertanyaan, 4 pilihan jawaban, dan pilih 1 kunci jawaban benar</p>
            </div>
        </div>

        <script>
            const existingPertanyaans = @json($soals->pluck('pertanyaan')->map(fn($p) => strtolower(trim($p)))->values());
        </script>

        <form action="{{ route('admin.soal.store', $quiz) }}" method="POST" id="form-soal" class="space-y-4">
            @csrf

            <div id="soal-container" class="space-y-4"></div>

            {{-- Pesan Peringatan Duplikat --}}
            <div id="duplikat-warning" class="hidden bg-amber-50 border border-amber-300 text-amber-900 px-4 py-3 rounded-xl text-xs shadow-2xs"></div>

            <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-slate-200/70">
                <button type="button"
                        onclick="tambahSoal()"
                        class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-xs transition duration-150 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Form Soal
                </button>

                <button type="submit"
                        id="btn-simpan"
                        class="inline-flex items-center gap-1.5 bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-xs hover:shadow transition duration-150 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Simpan Semua Soal Manual
                </button>
            </div>
        </form>
    </div>

    {{-- SECTION 3: DAFTAR SOAL YANG SUDAH ADA --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs space-y-4">

        {{-- Toolbar Atas: Judul, Toggle Acak, & Search Input --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <h3 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#7B1038]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Daftar Soal yang Sudah Ada ({{ $soals->count() }})
                </h3>

                {{-- Tombol Toggle Shuffle/Acak Soal --}}
                <form action="{{ route('admin.quiz.toggle-shuffle', $quiz) }}" method="POST" class="inline m-0 p-0">
                    @csrf
                    @method('PATCH')
                    @if($quiz->is_shuffle)
                        <button type="submit"
                                title="Klik untuk menonaktifkan acak soal & jawaban bagi siswa"
                                class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-2xs transition duration-150 cursor-pointer">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-200 animate-ping"></span>
                            <span>Acak Soal: Aktif</span>
                        </button>
                    @else
                        <button type="submit"
                                title="Klik untuk mengaktifkan acak soal & jawaban bagi siswa"
                                class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-xs font-bold px-3 py-1.5 rounded-full shadow-2xs transition duration-150 cursor-pointer">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            <span>Acak Soal: Nonaktif</span>
                        </button>
                    @endif
                </form>
            </div>

            {{-- Input Pencarian Realtime --}}
            <div class="relative w-full sm:w-64">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       id="search-soal"
                       placeholder="Cari teks pertanyaan / opsi..."
                       class="w-full pl-9 pr-3.5 py-1.5 text-xs rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
            </div>
        </div>

        @if($quiz->is_shuffle)
            <div class="text-xs text-emerald-800 bg-emerald-50 border border-emerald-200 px-3.5 py-2 rounded-xl font-medium flex items-center gap-1.5">
                <span>✨</span>
                <span>Fitur acak aktif: Setiap siswa akan menerima urutan butir soal dan susunan pilihan ganda yang teracak unik saat mengerjakan.</span>
            </div>
        @endif

        {{-- Tabel Daftar Soal --}}
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-xs" id="table-soal">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase tracking-wider font-bold text-[11px]">
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">Pertanyaan</th>
                        <th class="px-4 py-3">Pilihan Jawaban (A, B, C, D)</th>
                        <th class="px-4 py-3 text-center w-20">Kunci</th>
                        <th class="px-4 py-3 text-right w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($soals as $index => $soal)
                        <tr class="hover:bg-slate-50/70 transition duration-75">
                            <td class="px-4 py-3.5 text-center font-bold text-slate-500">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-slate-900 max-w-xs leading-relaxed">
                                {{ $soal->pertanyaan }}
                            </td>
                            <td class="px-4 py-3.5 text-xs space-y-1">
                                <div class="flex items-start gap-1.5 {{ strtolower($soal->jawaban_benar) === 'a' ? 'text-emerald-700 font-bold bg-emerald-50/60 px-2 py-0.5 rounded-md border border-emerald-200' : 'text-slate-600' }}">
                                    <span class="w-4 font-bold shrink-0">A.</span>
                                    <span>{{ $soal->pilihan_a }}</span>
                                </div>
                                <div class="flex items-start gap-1.5 {{ strtolower($soal->jawaban_benar) === 'b' ? 'text-emerald-700 font-bold bg-emerald-50/60 px-2 py-0.5 rounded-md border border-emerald-200' : 'text-slate-600' }}">
                                    <span class="w-4 font-bold shrink-0">B.</span>
                                    <span>{{ $soal->pilihan_b }}</span>
                                </div>
                                <div class="flex items-start gap-1.5 {{ strtolower($soal->jawaban_benar) === 'c' ? 'text-emerald-700 font-bold bg-emerald-50/60 px-2 py-0.5 rounded-md border border-emerald-200' : 'text-slate-600' }}">
                                    <span class="w-4 font-bold shrink-0">C.</span>
                                    <span>{{ $soal->pilihan_c }}</span>
                                </div>
                                <div class="flex items-start gap-1.5 {{ strtolower($soal->jawaban_benar) === 'd' ? 'text-emerald-700 font-bold bg-emerald-50/60 px-2 py-0.5 rounded-md border border-emerald-200' : 'text-slate-600' }}">
                                    <span class="w-4 font-bold shrink-0">D.</span>
                                    <span>{{ $soal->pilihan_d }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg uppercase font-black text-xs bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    {{ strtoupper($soal->jawaban_benar) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.soal.edit', $soal) }}"
                                       title="Edit Soal"
                                       class="p-1.5 rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition duration-150">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.soal.destroy', $soal) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus butir soal ini?')"
                                          class="inline m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Hapus Soal"
                                                class="p-1.5 rounded-lg text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition duration-150 cursor-pointer">
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
                            <td colspan="5" class="px-4 py-10 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="font-semibold text-xs text-slate-500">Belum ada soal untuk kuis ini.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Silakan buat secara manual pada form di atas atau unggah melalui format CSV.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Tabel --}}
        <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100">
            <span>Poin per soal dihitung otomatis: Total 100 poin dibagi jumlah butir soal yang ada.</span>
            <span id="match-count" class="font-medium text-slate-500"></span>
        </div>
    </div>
</div>

<script>
let idx = 0;
let currentFormPertanyaans = [];

function getNormalizedValue(value) {
    return value.trim().toLowerCase();
}

function checkDuplicates() {
    const textareas = document.querySelectorAll('#soal-container textarea[data-pertanyaan]');
    const dupWarn = document.getElementById('duplikat-warning');
    const seenInForm = [];
    const duplicateLabels = [];

    textareas.forEach(function (ta) {
        const val = getNormalizedValue(ta.value);
        if (!val) return;

        const formNo = ta.dataset.formNo;

        // Cek duplikat dengan soal yang sudah ada di database
        if (existingPertanyaans.includes(val)) {
            ta.classList.add('border-rose-400', 'bg-rose-50');
            ta.classList.remove('border-slate-300');
            duplicateLabels.push('Form Soal #' + formNo + ': teks pertanyaan sama dengan soal yang sudah terdaftar');
        // Cek duplikat antar form baru
        } else if (seenInForm.includes(val)) {
            ta.classList.add('border-amber-400', 'bg-amber-50');
            ta.classList.remove('border-slate-300', 'border-rose-400');
            duplicateLabels.push('Form Soal #' + formNo + ': sama dengan form soal lain di atasnya');
        } else {
            ta.classList.remove('border-rose-400', 'bg-rose-50', 'border-amber-400', 'bg-amber-50');
            ta.classList.add('border-slate-300');
            seenInForm.push(val);
        }
    });

    if (duplicateLabels.length > 0) {
        dupWarn.innerHTML = '⚠️ <strong>Pertanyaan duplikat terdeteksi:</strong><ul class="list-disc list-inside mt-1 space-y-0.5">' +
            duplicateLabels.map(d => '<li>' + d + '</li>').join('') + '</ul>';
        dupWarn.classList.remove('hidden');
    } else {
        dupWarn.classList.add('hidden');
    }
}

function tambahSoal() {
    const c = document.getElementById('soal-container');
    const i = idx++;
    const formNo = i + 1;
    const div = document.createElement('div');
    div.className = 'border border-slate-300 rounded-2xl p-4 sm:p-5 bg-white space-y-3.5 relative shadow-2xs';
    div.id = 'soal-form-' + i;
    div.innerHTML = `
        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-800 uppercase tracking-wide">
                <span class="w-5 h-5 rounded-md bg-[#7B1038]/10 text-[#7B1038] flex items-center justify-center font-bold text-[10px]">#${formNo}</span>
                Form Soal #${formNo}
            </span>
            <button type="button" onclick="hapusForm(${i})" class="text-xs text-rose-600 hover:text-rose-800 font-bold border border-rose-200 bg-rose-50 hover:bg-rose-100 rounded-lg px-2.5 py-1 transition cursor-pointer">✕ Hapus Form</button>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Teks Pertanyaan <span class="text-rose-500">*</span></label>
            <textarea name="soals[${i}][pertanyaan]" rows="2" placeholder="Tuliskan butir soal atau pertanyaan di sini..." required
                data-pertanyaan="true" data-form-no="${formNo}"
                oninput="checkDuplicates()"
                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] focus:outline-none transition"></textarea>
        </div>
        <div id="dup-hint-${i}" class="hidden text-xs text-rose-600 font-medium"></div>
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-700">Pilihan Ganda &amp; Kunci Jawaban <span class="text-rose-500">*</span></label>
                <span class="text-[11px] text-slate-500">Pilih opsi radio (A/B/C/D) untuk jawaban benar</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Opsi A -->
                <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-2 has-[:checked]:ring-emerald-500/20 has-[:checked]:bg-emerald-50/20 transition-all shadow-2xs">
                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none" title="Tandai Opsi A sebagai Kunci Jawaban Benar">
                        <input type="radio" name="soals[${i}][jawaban_benar]" value="a" checked required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs font-bold text-slate-700">A</span>
                    </label>
                    <input type="text" name="soals[${i}][pilihan_a]" placeholder="Pilihan jawaban A..." required class="flex-1 rounded-r-xl border-0 px-3 py-2 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                </div>

                <!-- Opsi B -->
                <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-2 has-[:checked]:ring-emerald-500/20 has-[:checked]:bg-emerald-50/20 transition-all shadow-2xs">
                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none" title="Tandai Opsi B sebagai Kunci Jawaban Benar">
                        <input type="radio" name="soals[${i}][jawaban_benar]" value="b" required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs font-bold text-slate-700">B</span>
                    </label>
                    <input type="text" name="soals[${i}][pilihan_b]" placeholder="Pilihan jawaban B..." required class="flex-1 rounded-r-xl border-0 px-3 py-2 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                </div>

                <!-- Opsi C -->
                <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-2 has-[:checked]:ring-emerald-500/20 has-[:checked]:bg-emerald-50/20 transition-all shadow-2xs">
                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none" title="Tandai Opsi C sebagai Kunci Jawaban Benar">
                        <input type="radio" name="soals[${i}][jawaban_benar]" value="c" required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs font-bold text-slate-700">C</span>
                    </label>
                    <input type="text" name="soals[${i}][pilihan_c]" placeholder="Pilihan jawaban C..." required class="flex-1 rounded-r-xl border-0 px-3 py-2 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                </div>

                <!-- Opsi D -->
                <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-2 has-[:checked]:ring-emerald-500/20 has-[:checked]:bg-emerald-50/20 transition-all shadow-2xs">
                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none" title="Tandai Opsi D sebagai Kunci Jawaban Benar">
                        <input type="radio" name="soals[${i}][jawaban_benar]" value="d" required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs font-bold text-slate-700">D</span>
                    </label>
                    <input type="text" name="soals[${i}][pilihan_d]" placeholder="Pilihan jawaban D..." required class="flex-1 rounded-r-xl border-0 px-3 py-2 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                </div>
            </div>
        </div>
        <div class="flex items-center gap-1.5 text-[11px] text-slate-500 pt-1">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Lingkaran radio yang aktif menandai opsi tersebut sebagai <strong>kunci jawaban benar</strong>.</span>
        </div>`;
    c.appendChild(div);
}

function hapusForm(i) {
    const el = document.getElementById('soal-form-' + i);
    if (el) {
        el.remove();
        checkDuplicates();
    }
}

window.addEventListener('DOMContentLoaded', function() {
    tambahSoal();

    // Script pencarian soal
    const searchInput = document.getElementById('search-soal');
    const tableBody = document.querySelector('#table-soal tbody');
    if (searchInput && tableBody) {
        const rows = tableBody.querySelectorAll('tr:not(#empty-row)');
        const matchCount = document.getElementById('match-count');

        searchInput.addEventListener('input', function(e) {
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
});
</script>
@endsection
