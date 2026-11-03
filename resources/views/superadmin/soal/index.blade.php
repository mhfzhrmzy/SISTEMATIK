@extends('layouts.app')

@section('title', 'Kelola Soal Quiz — Superadmin Portal')

@section('content')
<div class="flex min-h-screen bg-slate-100 font-sans text-slate-800">
    <!-- LEFT SIDEBAR -->
    @include('superadmin.partials.sidebar', ['menu' => 'quiz'])

    <!-- MAIN WORKSPACE -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- TOP HEADER BAR -->
        @include('superadmin.partials.header')

        <!-- PAGE CONTENT -->
        <main class="p-6 lg:p-8 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Kelola Soal Quiz</h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Materi: <strong>{{ $quiz->materi->judul_materi }}</strong> ({{ ucfirst($quiz->tipe_test) }}) &bull; 
                            Guru: <strong>{{ $quiz->adminGuru->nama_lengkap ?? $quiz->materi->adminGuru->nama_lengkap ?? '-' }}</strong>
                        </p>
                    </div>
                    <a href="{{ route('superadmin.quiz.index') }}" class="text-xs font-semibold text-[#6D1837] hover:underline flex items-center gap-1">
                        &larr; Kembali ke Daftar Quiz
                    </a>
                </div>

                @if(session('success'))
                    <div class="rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 text-xs font-semibold flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="rounded-2xl bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 text-xs font-semibold flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="rounded-2xl bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 text-xs mb-4">
                        <p class="font-bold mb-1">Terjadi kesalahan validasi:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Section Import Spreadsheet / CSV -->
                @if($soals->isEmpty())
                    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 mb-8">
                        <h3 class="text-sm font-bold text-emerald-900 mb-2 flex items-center gap-2">
                            <span>📊 Import Soal dari Spreadsheet / CSV</span>
                            <span class="text-[10px] bg-emerald-200 text-emerald-800 font-bold px-2 py-0.5 rounded-full">Hanya sekali</span>
                        </h3>
                        <p class="text-xs text-emerald-700 mb-4">
                            Upload file CSV untuk menambahkan banyak soal sekaligus secara instan.
                            <strong>Fitur ini hanya tersedia saat quiz belum memiliki soal.</strong>
                        </p>

                        <form action="{{ route('superadmin.soal.import', $quiz) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                            @csrf
                            <input type="file" name="csv_file" accept=".csv" required class="text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition duration-150 cursor-pointer shadow-sm">
                                Upload &amp; Import CSV
                            </button>
                            <a href="{{ route('superadmin.soal.template') }}" class="inline-flex items-center gap-1 border border-emerald-600 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold px-3 py-2 rounded-xl transition duration-150">
                                ⬇️ Unduh Template CSV
                            </a>
                        </form>
                    </div>
                @else
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 mb-8 flex items-start gap-3">
                        <span class="text-lg">🔒</span>
                        <div>
                            <p class="text-xs font-bold text-slate-700">Import CSV Tidak Tersedia</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Quiz ini sudah memiliki <strong>{{ $soals->count() }} soal</strong>. Import CSV hanya bisa dilakukan saat quiz masih kosong.
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Form Manual Tambah Soal -->
                <h3 class="text-base font-bold text-slate-900 mb-3">Tambah Soal Secara Manual</h3>

                <script>
                    const existingPertanyaans = @json($soals->pluck('pertanyaan')->map(fn($p) => strtolower(trim($p)))->values());
                </script>

                <form action="{{ route('superadmin.soal.store', $quiz) }}" method="POST" id="form-soal" class="space-y-4 mb-8">
                    @csrf
                    <div id="soal-container" class="space-y-4"></div>

                    <div id="duplikat-warning" class="hidden bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-xl text-xs"></div>

                    <div class="flex gap-3">
                        <button type="button" onclick="tambahSoal()" class="bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl cursor-pointer">+ Tambah Form Soal</button>
                        <button type="submit" id="btn-simpan" class="bg-[#6D1837] hover:bg-[#521128] text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-sm cursor-pointer">Simpan Semua Soal Manual</button>
                    </div>
                </form>

                <!-- Daftar Soal Header & Tombol Acak/Shuffle -->
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pt-4 border-t border-slate-100">
                    <div class="flex flex-wrap items-center gap-3">
                        <h3 class="text-base font-bold text-slate-900">Daftar Soal yang Sudah Ada ({{ $soals->count() }})</h3>

                        <form action="{{ route('superadmin.quiz.toggle-shuffle', $quiz) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            @if($quiz->is_shuffle)
                                <button type="submit" 
                                        title="Klik untuk menonaktifkan acak soal & jawaban bagi siswa"
                                        class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm transition duration-150 cursor-pointer">
                                    <span class="w-2 h-2 rounded-full bg-emerald-200 animate-ping"></span>
                                    🔀 Soal &amp; Jawaban Diacak (Aktif)
                                </button>
                            @else
                                <button type="submit" 
                                        title="Klik untuk mengaktifkan acak soal & jawaban bagi siswa"
                                        class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm transition duration-150 cursor-pointer">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    🔀 Acak Soal: Nonaktif (Klik untuk Acak)
                                </button>
                            @endif
                        </form>
                    </div>

                    @if($quiz->is_shuffle)
                        <span class="text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-xl font-medium">
                            ✨ Setiap siswa akan menerima urutan soal &amp; pilihan jawaban yang diacak secara otomatis.
                        </span>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border rounded-xl overflow-hidden">
                        <thead>
                            <tr class="border-b bg-slate-50 text-slate-700 font-bold uppercase">
                                <th class="px-3 py-2.5">No</th>
                                <th class="px-3 py-2.5">Pertanyaan</th>
                                <th class="px-3 py-2.5">Pilihan Jawaban</th>
                                <th class="px-3 py-2.5">Kunci</th>
                                <th class="px-3 py-2.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($soals as $index => $soal)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-3 py-3 font-semibold text-slate-500">{{ $index + 1 }}</td>
                                    <td class="px-3 py-3 font-medium text-slate-800 max-w-sm">{{ $soal->pertanyaan }}</td>
                                    <td class="px-3 py-3 text-[11px] space-y-1">
                                        <div><strong class="{{ $soal->jawaban_benar == 'a' ? 'text-green-600 font-bold' : 'text-slate-500' }}">A:</strong> {{ $soal->pilihan_a }}</div>
                                        <div><strong class="{{ $soal->jawaban_benar == 'b' ? 'text-green-600 font-bold' : 'text-slate-500' }}">B:</strong> {{ $soal->pilihan_b }}</div>
                                        <div><strong class="{{ $soal->jawaban_benar == 'c' ? 'text-green-600 font-bold' : 'text-slate-500' }}">C:</strong> {{ $soal->pilihan_c }}</div>
                                        <div><strong class="{{ $soal->jawaban_benar == 'd' ? 'text-green-600 font-bold' : 'text-slate-500' }}">D:</strong> {{ $soal->pilihan_d }}</div>
                                    </td>
                                    <td class="px-3 py-3 uppercase font-bold text-blue-600">{{ $soal->jawaban_benar }}</td>
                                    <td class="px-3 py-3 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('superadmin.soal.edit', $soal) }}" class="text-amber-600 hover:text-amber-700 font-semibold text-xs hover:underline">Edit</a>
                                            <form action="{{ route('superadmin.soal.destroy', $soal) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700 font-semibold text-xs hover:underline cursor-pointer">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-6 text-center text-slate-400 font-medium">Belum ada soal untuk quiz ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
let idx = 0;

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

        if (existingPertanyaans.includes(val)) {
            ta.classList.add('border-red-400', 'bg-red-50');
            ta.classList.remove('border-slate-300');
            duplicateLabels.push('Form Soal #' + formNo + ': sama dengan soal yang sudah ada di database');
        } else if (seenInForm.includes(val)) {
            ta.classList.add('border-amber-400', 'bg-amber-50');
            ta.classList.remove('border-slate-300', 'border-red-400');
            duplicateLabels.push('Form Soal #' + formNo + ': sama dengan form soal lain di atas');
        } else {
            ta.classList.remove('border-red-400', 'bg-red-50', 'border-amber-400', 'bg-amber-50');
            ta.classList.add('border-slate-300');
            seenInForm.push(val);
        }
    });

    if (duplicateLabels.length > 0) {
        dupWarn.innerHTML = '⚠️ <strong>Pertanyaan duplikat terdeteksi:</strong><ul class="list-disc list-inside mt-1">' +
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
    div.className = 'border border-slate-300 rounded-2xl p-4 bg-slate-50/70 space-y-3 relative';
    div.id = 'soal-form-' + i;
    div.innerHTML = `
        <div class="flex justify-between items-center mb-1">
            <span class="font-bold text-xs text-slate-700 uppercase">Form Soal #${formNo}</span>
            <button type="button" onclick="hapusForm(${i})" class="text-xs text-red-500 hover:text-red-700 font-semibold border border-red-300 rounded-lg px-2 py-0.5 hover:bg-red-50 cursor-pointer">✕ Hapus Form</button>
        </div>
        <textarea name="soals[${i}][pertanyaan]" rows="2" placeholder="Tuliskan teks pertanyaan di sini..." required
            data-pertanyaan="true" data-form-no="${formNo}"
            oninput="checkDuplicates()"
            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#6D1837] focus:outline-none bg-white"></textarea>
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-700">Pilihan Jawaban &amp; Kunci</label>
                <span class="text-[11px] text-slate-500">Klik buletan (A/B/C/D) untuk jawaban benar</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                <!-- Opsi A -->
                <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 shadow-sm">
                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none">
                        <input type="radio" name="soals[${i}][jawaban_benar]" value="a" checked required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs font-bold text-slate-700">A</span>
                    </label>
                    <input type="text" name="soals[${i}][pilihan_a]" placeholder="Pilihan jawaban A..." required class="flex-1 rounded-r-xl border-0 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                </div>

                <!-- Opsi B -->
                <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 shadow-sm">
                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none">
                        <input type="radio" name="soals[${i}][jawaban_benar]" value="b" required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs font-bold text-slate-700">B</span>
                    </label>
                    <input type="text" name="soals[${i}][pilihan_b]" placeholder="Pilihan jawaban B..." required class="flex-1 rounded-r-xl border-0 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                </div>

                <!-- Opsi C -->
                <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 shadow-sm">
                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none">
                        <input type="radio" name="soals[${i}][jawaban_benar]" value="c" required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs font-bold text-slate-700">C</span>
                    </label>
                    <input type="text" name="soals[${i}][pilihan_c]" placeholder="Pilihan jawaban C..." required class="flex-1 rounded-r-xl border-0 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                </div>

                <!-- Opsi D -->
                <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 shadow-sm">
                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none">
                        <input type="radio" name="soals[${i}][jawaban_benar]" value="d" required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs font-bold text-slate-700">D</span>
                    </label>
                    <input type="text" name="soals[${i}][pilihan_d]" placeholder="Pilihan jawaban D..." required class="flex-1 rounded-r-xl border-0 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                </div>
            </div>
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

window.addEventListener('DOMContentLoaded', tambahSoal);
</script>
@endsection

