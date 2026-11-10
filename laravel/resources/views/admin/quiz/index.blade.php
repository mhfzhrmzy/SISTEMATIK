@extends('layouts.app')
@section('title', 'Kelola Quiz')
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between border-b pb-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Kelola Quiz</h2>
            <p class="text-sm text-gray-500">Buat dan atur kuis Pre-Test &amp; Post-Test untuk materi pembelajaran</p>
        </div>
    </div>

    <!-- Form Buat Quiz -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 mb-8">
        <h3 class="text-md font-semibold text-blue-900 mb-3">➕ Buat Quiz Baru</h3>
        <form action="{{ route('admin.quiz.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Materi</label>
                <select name="id_materi" id="select-materi" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Materi --</option>
                    @foreach ($materis as $m)
                        <option value="{{ $m->id_materi }}" {{ old('id_materi') == $m->id_materi ? 'selected' : '' }}>{{ $m->judul_materi }}</option>
                    @endforeach
                </select>
                @error('id_materi')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tipe Test</label>
                <select name="tipe_test" id="select-tipe" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="pretest" id="opt-pretest">Pre-Test</option>
                    <option value="posttest" id="opt-posttest">Post-Test</option>
                </select>
                @error('tipe_test')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
                <p id="tipe-info" class="text-xs text-amber-600 mt-1 hidden"></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Timer Total (Menit)</label>
                <input type="number" name="timer" value="{{ old('timer', 30) }}" min="1" max="120" required placeholder="Contoh: 30" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Maks. 120 menit</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Dibuat</label>
                <input type="date" value="{{ date('Y-m-d') }}" readonly class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm font-semibold bg-gray-100 text-gray-500 cursor-not-allowed">
                <p class="text-xs text-gray-400 mt-1">Otomatis, tidak dapat diubah</p>
            </div>
            <div class="md:col-span-4 bg-amber-50 border border-amber-200 rounded-md px-4 py-2 text-xs text-amber-800">
                ℹ️ Setiap quiz otomatis bernilai total <strong>100 Poin</strong> (poin per soal dihitung dari 100 dibagi total soal). Setiap materi hanya dapat memiliki <strong>1 pre-test</strong> dan <strong>1 post-test</strong>.
            </div>
            <button type="submit" id="btn-submit" class="md:col-span-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-md transition duration-150">
                Simpan &amp; Buat Quiz
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Quiz -->
    <h3 class="text-md font-semibold text-gray-800 mb-3">Daftar Quiz Pembelajaran</h3>
    <table class="w-full text-sm text-left border rounded-lg overflow-hidden">
        <thead>
            <tr class="border-b bg-gray-100 text-gray-700">
                <th class="px-3 py-2.5">Materi</th>
                <th class="px-3 py-2.5">Tipe Test</th>
                <th class="px-3 py-2.5">Total Poin</th>
                <th class="px-3 py-2.5">Tanggal Dibuat</th>
                <th class="px-3 py-2.5">Jumlah Soal</th>
                <th class="px-3 py-2.5 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($quizzes as $quiz)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-3 py-2.5 font-medium">{{ $quiz->materi->judul_materi ?? '-' }}</td>
                    <td class="px-3 py-2.5">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $quiz->tipe_test == 'pretest' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($quiz->tipe_test) }}
                        </span>
                    </td>
                    <td class="px-3 py-2.5">
                        <span class="font-semibold text-blue-600">100 Poin</span>
                        @if($quiz->soal->count() > 0)
                            <span class="block text-[11px] text-gray-400 font-normal">({{ round(100 / $quiz->soal->count(), 2) }} / soal)</span>
                        @else
                            <span class="block text-[11px] text-gray-400 font-normal">(0 soal)</span>
                        @endif
                    </td>
                    <td class="px-3 py-2.5 text-gray-600">{{ \Carbon\Carbon::parse($quiz->tanggal)->format('d M Y') }}</td>
                    <td class="px-3 py-2.5 font-bold">{{ $quiz->soal->count() }} Soal</td>
                    <td class="px-3 py-2.5 text-right">
                        <a href="{{ route('admin.soal.index', $quiz) }}" class="bg-blue-50 text-blue-600 hover:bg-blue-100 font-semibold px-3 py-1 rounded border border-blue-300 text-xs mr-2">
                            📝 Kelola Soal ({{ $quiz->soal->count() }})
                        </a>
                        <form action="{{ route('admin.quiz.destroy', $quiz) }}" method="POST" class="inline" onsubmit="return confirm('Hapus quiz ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline font-semibold text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">Belum ada quiz yang dibuat.</td></tr>
            @endforelse
        </tbody>
    </table>
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
    }

    selectMateri.addEventListener('change', updateTipeOptions);
    updateTipeOptions();
</script>
@endpush
@endsection