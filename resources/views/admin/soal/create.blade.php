@extends('layouts.app')

@section('title', 'Tambah Soal')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-3xl mx-auto">
    <h2 class="text-lg font-semibold mb-1">Tambah Soal ({{ ucfirst($tipe) }})</h2>
    <p class="text-sm text-gray-500 mb-6">
        Klik <strong>"+ Tambah Soal"</strong> untuk menambah form soal baru (bisa banyak sekaligus),
        lalu klik <strong>Simpan Semua Soal</strong> di bagian bawah.
    </p>

    <form action="{{ route('admin.soal.store') }}" method="POST" id="form-soal" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Materi</label>
            <select name="id_materi" required
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="">-- Pilih Materi --</option>
                @foreach ($materis as $m)
                    <option value="{{ $m->id_materi }}">{{ $m->judul_materi }}</option>
                @endforeach
            </select>
            @error('id_materi')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Tipe Soal</label>
            <select name="tipe" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="pretest" {{ $tipe == 'pretest' ? 'selected' : '' }}>Pretest</option>
                <option value="posttest" {{ $tipe == 'posttest' ? 'selected' : '' }}>Posttest</option>
            </select>
        </div>

        <hr class="my-4">

        <div id="soal-container" class="space-y-4"></div>

        <button type="button" onclick="tambahSoal()"
                class="bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium px-4 py-2 rounded-md">
            + Tambah Soal
        </button>

        <hr class="my-4">

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-md">
                Simpan Semua Soal
            </button>
            <a href="{{ route('admin.soal.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium px-5 py-2 rounded-md">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
    let soalIndex = 0;

    function tambahSoal() {
        const container = document.getElementById('soal-container');
        const idx = soalIndex;

        const div = document.createElement('div');
        div.className = 'relative border border-gray-200 rounded-md p-4 bg-gray-50';
        div.id = 'soal-' + idx;
        div.innerHTML = `
            <button type="button" onclick="hapusSoal(${idx})"
                    class="absolute top-3 right-3 text-red-600 text-xs hover:underline">Hapus</button>
            <h4 class="font-medium text-sm mb-3">Soal #${idx + 1}</h4>

            <div class="mb-3">
                <label class="block text-xs font-medium mb-1">Pertanyaan</label>
                <textarea name="soals[${idx}][pertanyaan]" rows="2" required
                          class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></textarea>
            </div>

            <div class="mb-3">
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold text-gray-700">Pilihan Jawaban &amp; Kunci Jawaban</label>
                    <span class="text-[11px] text-gray-500">Klik buletan di samping opsi untuk jawaban benar</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                    <div class="flex items-center bg-white rounded-md border border-gray-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 has-[:checked]:bg-emerald-50/20 transition-all shadow-sm">
                        <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-gray-50 border-r border-gray-200 rounded-l-md hover:bg-emerald-50 transition-colors select-none" title="Pilih Opsi A sebagai jawaban benar">
                            <input type="radio" name="soals[${idx}][jawaban_benar]" value="a" checked required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span class="text-xs font-bold text-gray-700">A</span>
                        </label>
                        <input type="text" name="soals[${idx}][pilihan_a]" required placeholder="Pilihan A" class="flex-1 rounded-r-md border-0 px-3 py-2 text-sm text-gray-800 focus:ring-0 focus:outline-none">
                    </div>
                    <div class="flex items-center bg-white rounded-md border border-gray-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 has-[:checked]:bg-emerald-50/20 transition-all shadow-sm">
                        <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-gray-50 border-r border-gray-200 rounded-l-md hover:bg-emerald-50 transition-colors select-none" title="Pilih Opsi B sebagai jawaban benar">
                            <input type="radio" name="soals[${idx}][jawaban_benar]" value="b" required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span class="text-xs font-bold text-gray-700">B</span>
                        </label>
                        <input type="text" name="soals[${idx}][pilihan_b]" required placeholder="Pilihan B" class="flex-1 rounded-r-md border-0 px-3 py-2 text-sm text-gray-800 focus:ring-0 focus:outline-none">
                    </div>
                    <div class="flex items-center bg-white rounded-md border border-gray-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 has-[:checked]:bg-emerald-50/20 transition-all shadow-sm">
                        <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-gray-50 border-r border-gray-200 rounded-l-md hover:bg-emerald-50 transition-colors select-none" title="Pilih Opsi C sebagai jawaban benar">
                            <input type="radio" name="soals[${idx}][jawaban_benar]" value="c" required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span class="text-xs font-bold text-gray-700">C</span>
                        </label>
                        <input type="text" name="soals[${idx}][pilihan_c]" required placeholder="Pilihan C" class="flex-1 rounded-r-md border-0 px-3 py-2 text-sm text-gray-800 focus:ring-0 focus:outline-none">
                    </div>
                    <div class="flex items-center bg-white rounded-md border border-gray-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 has-[:checked]:bg-emerald-50/20 transition-all shadow-sm">
                        <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-gray-50 border-r border-gray-200 rounded-l-md hover:bg-emerald-50 transition-colors select-none" title="Pilih Opsi D sebagai jawaban benar">
                            <input type="radio" name="soals[${idx}][jawaban_benar]" value="d" required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span class="text-xs font-bold text-gray-700">D</span>
                        </label>
                        <input type="text" name="soals[${idx}][pilihan_d]" required placeholder="Pilihan D" class="flex-1 rounded-r-md border-0 px-3 py-2 text-sm text-gray-800 focus:ring-0 focus:outline-none">
                    </div>
                </div>
                <p class="text-[11px] text-gray-500 mt-2 flex items-center gap-1.5">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Buletan yang dicentang menandakan opsi tersebut <strong>jawaban benar</strong> (lainnya otomatis salah).</span>
                </p>
            </div>
        `;
        container.appendChild(div);
        soalIndex++;
        renumberSoal();
    }

    function hapusSoal(idx) {
        const el = document.getElementById('soal-' + idx);
        if (el) el.remove();
        renumberSoal();
    }

    function renumberSoal() {
        document.querySelectorAll('#soal-container > div').forEach((card, i) => {
            card.querySelector('h4').innerText = 'Soal #' + (i + 1);
        });
    }

    window.addEventListener('DOMContentLoaded', tambahSoal);
</script>
@endsection