@extends('layouts.app')

@section('title', 'Edit Soal Quiz')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mb-1">Edit Butir Soal</h1>
            <p class="text-xs sm:text-sm text-slate-500">Perbarui teks pertanyaan, pilihan ganda, atau ubah kunci jawaban.</p>
        </div>

        <a href="{{ route('admin.soal.index', $soal->id_quiz) }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Notifikasi Error --}}
    @if($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 p-4 text-xs sm:text-sm shadow-2xs">
            <p class="font-bold mb-1">Terjadi kesalahan validasi:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Edit Soal --}}
    <div class="bg-slate-50/70 rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-2xs">
        <form action="{{ route('admin.soal.update', $soal) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Teks Pertanyaan --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Teks Pertanyaan <span class="text-rose-500">*</span>
                </label>
                <textarea name="pertanyaan"
                          rows="3"
                          required
                          class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs sm:text-sm focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] focus:outline-none transition">{{ $soal->pertanyaan }}</textarea>
            </div>

            {{-- Pilihan Jawaban & Kunci --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700">Pilihan Ganda &amp; Kunci Jawaban <span class="text-rose-500">*</span></label>
                    <span class="text-[11px] text-slate-500">Pilih opsi radio (A/B/C/D) untuk jawaban benar</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Opsi A -->
                    <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-2 has-[:checked]:ring-emerald-500/20 has-[:checked]:bg-emerald-50/20 transition-all shadow-2xs">
                        <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none" title="Pilih Opsi A sebagai jawaban benar">
                            <input type="radio" name="jawaban_benar" value="a" {{ strtolower($soal->jawaban_benar) === 'a' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span class="text-xs font-bold text-slate-700">A</span>
                        </label>
                        <input type="text" name="pilihan_a" value="{{ $soal->pilihan_a }}" required placeholder="Pilihan A" class="flex-1 rounded-r-xl border-0 px-3 py-2 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                    </div>

                    <!-- Opsi B -->
                    <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-2 has-[:checked]:ring-emerald-500/20 has-[:checked]:bg-emerald-50/20 transition-all shadow-2xs">
                        <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none" title="Pilih Opsi B sebagai jawaban benar">
                            <input type="radio" name="jawaban_benar" value="b" {{ strtolower($soal->jawaban_benar) === 'b' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span class="text-xs font-bold text-slate-700">B</span>
                        </label>
                        <input type="text" name="pilihan_b" value="{{ $soal->pilihan_b }}" required placeholder="Pilihan B" class="flex-1 rounded-r-xl border-0 px-3 py-2 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                    </div>

                    <!-- Opsi C -->
                    <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-2 has-[:checked]:ring-emerald-500/20 has-[:checked]:bg-emerald-50/20 transition-all shadow-2xs">
                        <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none" title="Pilih Opsi C sebagai jawaban benar">
                            <input type="radio" name="jawaban_benar" value="c" {{ strtolower($soal->jawaban_benar) === 'c' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span class="text-xs font-bold text-slate-700">C</span>
                        </label>
                        <input type="text" name="pilihan_c" value="{{ $soal->pilihan_c }}" required placeholder="Pilihan C" class="flex-1 rounded-r-xl border-0 px-3 py-2 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                    </div>

                    <!-- Opsi D -->
                    <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-2 has-[:checked]:ring-emerald-500/20 has-[:checked]:bg-emerald-50/20 transition-all shadow-2xs">
                        <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none" title="Pilih Opsi D sebagai jawaban benar">
                            <input type="radio" name="jawaban_benar" value="d" {{ strtolower($soal->jawaban_benar) === 'd' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span class="text-xs font-bold text-slate-700">D</span>
                        </label>
                        <input type="text" name="pilihan_d" value="{{ $soal->pilihan_d }}" required placeholder="Pilihan D" class="flex-1 rounded-r-xl border-0 px-3 py-2 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 pt-1">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Opsi dengan buletan radio aktif menandai <strong>kunci jawaban yang benar</strong>.</span>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="flex items-center gap-3 pt-3 border-t border-slate-200">
                <button type="submit"
                        class="flex items-center gap-2 bg-[#7B1038] hover:bg-[#5E0C2B] text-white font-bold py-2.5 px-5 rounded-xl shadow-xs hover:shadow transition duration-150 cursor-pointer text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan Soal
                </button>
                <a href="{{ route('admin.soal.index', $soal->id_quiz) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-200/80 hover:bg-slate-300 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
