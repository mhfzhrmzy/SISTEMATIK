@extends('layouts.app')

@section('title', 'Kelola Quiz — Superadmin Portal')

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
            <!-- Header Row -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Kelola Quiz</h1>
                    <p class="text-xs text-slate-500 mt-1">Super Admin dapat membuat dan mengatur kuis Pre-Test &amp; Post-Test untuk materi pembelajaran.</p>
                </div>
            </div>

            <!-- Flash Session Alerts -->
            @if(session('success'))
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 text-sm font-semibold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 text-sm font-semibold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-2xl bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 text-sm font-semibold space-y-1 shadow-sm">
                    <p class="font-bold flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Terjadi kesalahan validasi:
                    </p>
                    <ul class="list-disc list-inside text-xs space-y-0.5 pl-6 font-normal">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Buat Quiz -->
            <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <span>➕ Buat Quiz Baru</span>
                </h3>
                <form action="{{ route('superadmin.quiz.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4" id="form-create-quiz">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Materi <span class="text-red-500">*</span></label>
                        <select name="id_materi" id="select-materi" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:ring-2 focus:ring-[#6D1837] focus:outline-none">
                            <option value="">-- Pilih Materi --</option>
                            @foreach ($materis as $m)
                                <option value="{{ $m->id_materi }}" data-nip="{{ $m->nip }}" {{ old('id_materi') == $m->id_materi ? 'selected' : '' }}>
                                    {{ $m->judul_materi }} (Kelas {{ $m->kelas }} - {{ $m->adminGuru->nama_lengkap ?? 'Guru' }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_materi')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Guru Pengampu</label>
                        <select name="nip" id="select-guru" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:ring-2 focus:ring-[#6D1837] focus:outline-none">
                            <option value="">-- Otomatis Mengikuti Materi --</option>
                            @foreach ($admins as $guru)
                                <option value="{{ $guru->nip }}" {{ old('nip') == $guru->nip ? 'selected' : '' }}>
                                    {{ $guru->nama_lengkap }} ({{ $guru->nip }})
                                </option>
                            @endforeach
                        </select>
                        @error('nip')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Test <span class="text-red-500">*</span></label>
                        <select name="tipe_test" id="select-tipe" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:ring-2 focus:ring-[#6D1837] focus:outline-none">
                            <option value="pretest" id="opt-pretest">Pre-Test</option>
                            <option value="posttest" id="opt-posttest">Post-Test</option>
                        </select>
                        @error('tipe_test')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <p id="tipe-info" class="text-xs text-amber-600 mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Timer Total (Menit) <span class="text-red-500">*</span></label>
                        <input type="number" name="timer" id="input-timer" value="{{ old('timer', 30) }}" min="1" max="120" required placeholder="Contoh: 30" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:ring-2 focus:ring-[#6D1837] focus:outline-none">
                        <p id="timer-help" class="text-xs text-slate-400 mt-1">Maks. 120 menit</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal" id="input-tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" readonly class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold bg-slate-100 text-slate-500 cursor-not-allowed">
                        <p id="tanggal-help" class="text-xs text-slate-400 mt-1">Otomatis, tidak dapat diubah</p>
                        @error('tanggal')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Blok khusus posttest: Jam Mulai + Estimasi Ditutup --}}
                    <div id="blok-posttest" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-3 hidden">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Jam Mulai Post-Test</label>
                            @php
                                $oldJamParts = explode(':', old('jam_mulai', '08:00'));
                                $selectedJam = str_pad($oldJamParts[0] ?? '08', 2, '0', STR_PAD_LEFT);
                                $selectedMenit = str_pad($oldJamParts[1] ?? '00', 2, '0', STR_PAD_LEFT);
                            @endphp
                            <div class="flex items-center border border-slate-300 rounded-xl px-3 py-2 bg-white focus-within:ring-2 focus-within:ring-[#6D1837]">
                                <div class="flex items-center gap-1">
                                    <select id="select-jam" class="bg-transparent text-sm font-semibold text-slate-700 focus:outline-none cursor-pointer">
                                        @for ($h = 0; $h < 24; $h++)
                                            @php $valH = sprintf('%02d', $h); @endphp
                                            <option value="{{ $valH }}" {{ $selectedJam === $valH ? 'selected' : '' }}>{{ $valH }}</option>
                                        @endfor
                                    </select>
                                    <span class="text-sm font-bold text-slate-400">:</span>
                                    <select id="select-menit" class="bg-transparent text-sm font-semibold text-slate-700 focus:outline-none cursor-pointer">
                                        @for ($m = 0; $m < 60; $m++)
                                            @php $valM = sprintf('%02d', $m); @endphp
                                            <option value="{{ $valM }}" {{ $selectedMenit === $valM ? 'selected' : '' }}>{{ $valM }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <span class="ml-auto text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-md">WIB</span>
                                <input type="hidden" name="jam_mulai" id="input-jam-mulai" value="{{ old('jam_mulai', '08:00') }}">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Format 24 jam (WIB)</p>
                            @error('jam_mulai')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Estimasi Selesai</label>
                            <input type="text" id="input-estimasi-tutup" readonly disabled class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold bg-slate-100 text-slate-500 cursor-not-allowed" placeholder="—">
                        </div>
                    </div>

                    <div class="md:col-span-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5 text-xs text-amber-800">
                        ℹ️ Setiap quiz otomatis bernilai total <strong>100 Poin</strong>. Setiap materi hanya dapat memiliki <strong>1 pre-test</strong> dan <strong>1 post-test</strong>.
                    </div>
                    <button type="submit" id="btn-submit" class="md:col-span-4 bg-[#6D1837] hover:bg-[#521128] text-white text-sm font-bold py-2.5 rounded-xl transition duration-150 shadow-sm cursor-pointer">
                        Simpan &amp; Buat Quiz
                    </button>
                </form>
            </div>

            <!-- Tabel Daftar Quiz -->
            <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Daftar Quiz Pembelajaran</h3>
                        <p class="text-xs text-slate-500">Total {{ $quizzes->count() }} Quiz Dibuat</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-700 uppercase tracking-wider font-bold">
                                <th class="px-3 py-2.5 rounded-l-lg">Materi</th>
                                <th class="px-3 py-2.5">Guru Pengampu</th>
                                <th class="px-3 py-2.5">Tipe Test</th>
                                <th class="px-3 py-2.5">Total Poin</th>
                                <th class="px-3 py-2.5">Jadwal</th>
                                <th class="px-3 py-2.5 text-center">Jumlah Soal</th>
                                <th class="px-3 py-2.5 text-right rounded-r-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($quizzes as $quiz)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-3 py-3 font-semibold text-slate-800">
                                        {{ $quiz->materi->judul_materi ?? '-' }}
                                    </td>
                                    <td class="px-3 py-3 text-slate-700">
                                        {{ $quiz->adminGuru->nama_lengkap ?? $quiz->materi->adminGuru->nama_lengkap ?? '-' }}
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ $quiz->tipe_test == 'pretest' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-green-100 text-green-800 border border-green-200' }}">
                                            {{ ucfirst($quiz->tipe_test) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 font-semibold text-blue-600">
                                        100 Poin
                                        @if($quiz->soal->count() > 0)
                                            <span class="block text-[10px] text-slate-400 font-normal">({{ round(100 / $quiz->soal->count(), 2) }} / soal)</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-slate-600 text-xs">
                                        @if($quiz->tipe_test === 'posttest' && $quiz->jadwalLengkap())
                                            {{ $quiz->waktuMulai()->format('d M Y · H:i') }} – {{ $quiz->waktuSelesai()->format('H:i') }} WIB
                                        @else
                                            {{ \Carbon\Carbon::parse($quiz->tanggal)->format('d M Y') }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-center font-bold text-slate-700">
                                        {{ $quiz->soal->count() }} Soal
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            <a href="{{ route('superadmin.soal.index', $quiz) }}" class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold px-2.5 py-1 rounded-lg border border-blue-200 text-xs transition">
                                                📝 Soal ({{ $quiz->soal->count() }})
                                            </a>
                                            @if($quiz->tipe_test === 'posttest')
                                                <button type="button" onclick="openJadwalModal({{ $quiz->id_quiz }}, '{{ $quiz->tanggal?->format('Y-m-d') }}', '{{ $quiz->jam_mulai }}', {{ $quiz->timer }})" class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold px-2.5 py-1 rounded-lg border border-indigo-200 text-xs transition cursor-pointer">
                                                    📅 Jadwal
                                                </button>
                                            @endif
                                            <form action="{{ route('superadmin.quiz.destroy', $quiz) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Hapus quiz ini beserta seluruh soalnya?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-800 hover:bg-red-50 font-semibold px-2 py-1 rounded-lg text-xs transition cursor-pointer">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-6 text-center text-slate-400 font-medium">
                                        Belum ada quiz yang dibuat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

{{-- Modal Edit Jadwal Posttest --}}
<div id="modal-jadwal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-base font-bold text-slate-900 mb-4">📅 Ubah Jadwal Post-Test</h3>
        <form id="form-jadwal" method="POST">
            @csrf
            @method('PATCH')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="modal-tanggal" min="{{ date('Y-m-d') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jam Mulai</label>
                    <div class="flex items-center border border-slate-300 rounded-xl px-3 py-2 bg-white focus-within:ring-2 focus-within:ring-[#6D1837]">
                        <div class="flex items-center gap-1">
                            <select id="modal-select-jam" class="bg-transparent text-sm font-semibold text-slate-700 focus:outline-none cursor-pointer">
                                @for ($h = 0; $h < 24; $h++)
                                    @php $valH = sprintf('%02d', $h); @endphp
                                    <option value="{{ $valH }}">{{ $valH }}</option>
                                @endfor
                            </select>
                            <span class="text-sm font-bold text-slate-400">:</span>
                            <select id="modal-select-menit" class="bg-transparent text-sm font-semibold text-slate-700 focus:outline-none cursor-pointer">
                                @for ($m = 0; $m < 60; $m++)
                                    @php $valM = sprintf('%02d', $m); @endphp
                                    <option value="{{ $valM }}">{{ $valM }}</option>
                                @endfor
                            </select>
                        </div>
                        <span class="ml-auto text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-md">WIB</span>
                        <input type="hidden" name="jam_mulai" id="modal-jam-mulai">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Timer Total (Menit)</label>
                    <input type="number" name="timer" id="modal-timer" min="1" max="120" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Estimasi Ditutup</label>
                    <input type="text" id="modal-estimasi-tutup" readonly disabled class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold bg-slate-100 text-slate-500 cursor-not-allowed" placeholder="—">
                </div>
            </div>
            <div class="flex gap-2 mt-5">
                <button type="submit" class="flex-1 bg-[#6D1837] hover:bg-[#521128] text-white text-xs font-bold py-2.5 rounded-xl transition cursor-pointer">Simpan Jadwal</button>
                <button type="button" onclick="closeJadwalModal()" class="flex-1 border border-slate-300 text-slate-600 hover:bg-slate-50 text-xs font-bold py-2.5 rounded-xl transition cursor-pointer">Batal</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const existing = @json($existingCombinations);
    const selectMateri = document.getElementById('select-materi');
    const selectGuru = document.getElementById('select-guru');
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
            inputTanggal.classList.add('border-slate-300', 'focus:ring-2', 'focus:ring-[#6D1837]');
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
            inputTanggal.classList.remove('border-slate-300', 'focus:ring-2', 'focus:ring-[#6D1837]');
            inputTanggal.removeAttribute('name');
            inputTanggal.removeAttribute('min');
            tanggalHelp.textContent = 'Otomatis, tidak dapat diubah';
            timerHelp.textContent = 'Maks. 120 menit';
        }
    }

    function updateTipeOptions() {
        const materiId = selectMateri.value;
        const selectedOpt = selectMateri.options[selectMateri.selectedIndex];
        if (selectedOpt && selectedOpt.dataset.nip && !selectGuru.value) {
            selectGuru.value = selectedOpt.dataset.nip;
        }

        const usedTypes = existing[materiId] || [];
        const pretestUsed = usedTypes.includes('pretest');
        const posttestUsed = usedTypes.includes('posttest');

        optPretest.disabled = pretestUsed;
        optPosttest.disabled = posttestUsed;
        optPretest.textContent = 'Pre-Test' + (pretestUsed ? ' (sudah ada)' : '');
        optPosttest.textContent = 'Post-Test' + (posttestUsed ? ' (sudah ada)' : '');

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
        formJadwal.action = '/superadmin/quiz/' + quizId + '/jadwal';
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
</script>
@endpush
@endsection

