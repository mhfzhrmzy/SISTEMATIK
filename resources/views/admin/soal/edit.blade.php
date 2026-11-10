@extends('layouts.app')
@section('title', 'Edit Soal')
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-xl mx-auto">
    <div class="flex items-center justify-between border-b pb-3 mb-4">
        <h2 class="text-lg font-bold text-gray-800">Edit Soal</h2>
        <a href="{{ route('admin.soal.index', $soal->id_quiz) }}" class="text-sm text-blue-600 hover:underline">&larr; Batal</a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
            <p class="font-semibold mb-1">Terjadi kesalahan validasi:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.soal.update', $soal) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Pertanyaan</label>
            <textarea name="pertanyaan" rows="3" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ $soal->pertanyaan }}</textarea>
        </div>
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-semibold text-gray-700">Pilihan Jawaban &amp; Kunci Jawaban</label>
                <span class="text-[11px] text-gray-500">Klik buletan di samping opsi (A/B/C/D) untuk jawaban benar</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-2.5">
                <!-- Opsi A -->
                <div class="flex items-center bg-white rounded-md border border-gray-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 has-[:checked]:bg-emerald-50/20 transition-all shadow-sm">
                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-gray-50 border-r border-gray-200 rounded-l-md hover:bg-emerald-50 transition-colors select-none" title="Pilih Opsi A sebagai jawaban benar">
                        <input type="radio" name="jawaban_benar" value="a" {{ strtolower($soal->jawaban_benar) === 'a' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs font-bold text-gray-700">A</span>
                    </label>
                    <input type="text" name="pilihan_a" value="{{ $soal->pilihan_a }}" required placeholder="Pilihan A" class="flex-1 rounded-r-md border-0 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:ring-0 focus:outline-none">
                </div>

                <!-- Opsi B -->
                <div class="flex items-center bg-white rounded-md border border-gray-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 has-[:checked]:bg-emerald-50/20 transition-all shadow-sm">
                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-gray-50 border-r border-gray-200 rounded-l-md hover:bg-emerald-50 transition-colors select-none" title="Pilih Opsi B sebagai jawaban benar">
                        <input type="radio" name="jawaban_benar" value="b" {{ strtolower($soal->jawaban_benar) === 'b' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs font-bold text-gray-700">B</span>
                    </label>
                    <input type="text" name="pilihan_b" value="{{ $soal->pilihan_b }}" required placeholder="Pilihan B" class="flex-1 rounded-r-md border-0 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:ring-0 focus:outline-none">
                </div>

                <!-- Opsi C -->
                <div class="flex items-center bg-white rounded-md border border-gray-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 has-[:checked]:bg-emerald-50/20 transition-all shadow-sm">
                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-gray-50 border-r border-gray-200 rounded-l-md hover:bg-emerald-50 transition-colors select-none" title="Pilih Opsi C sebagai jawaban benar">
                        <input type="radio" name="jawaban_benar" value="c" {{ strtolower($soal->jawaban_benar) === 'c' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs font-bold text-gray-700">C</span>
                    </label>
                    <input type="text" name="pilihan_c" value="{{ $soal->pilihan_c }}" required placeholder="Pilihan C" class="flex-1 rounded-r-md border-0 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:ring-0 focus:outline-none">
                </div>

                <!-- Opsi D -->
                <div class="flex items-center bg-white rounded-md border border-gray-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 has-[:checked]:bg-emerald-50/20 transition-all shadow-sm">
                    <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-gray-50 border-r border-gray-200 rounded-l-md hover:bg-emerald-50 transition-colors select-none" title="Pilih Opsi D sebagai jawaban benar">
                        <input type="radio" name="jawaban_benar" value="d" {{ strtolower($soal->jawaban_benar) === 'd' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs font-bold text-gray-700">D</span>
                    </label>
                    <input type="text" name="pilihan_d" value="{{ $soal->pilihan_d }}" required placeholder="Pilihan D" class="flex-1 rounded-r-md border-0 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:ring-0 focus:outline-none">
                </div>
            </div>
            <p class="text-xs text-gray-500 mb-4 flex items-center gap-1.5">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Buletan yang dicentang menandakan opsi tersebut <strong>jawaban benar</strong> (lainnya otomatis salah).</span>
            </p>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-md transition duration-150">Update Soal</button>
    </form>
</div>
@endsection