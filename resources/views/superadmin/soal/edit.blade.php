@extends('layouts.app')

@section('title', 'Edit Soal Quiz — Superadmin Portal')

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
            <div class="bg-white rounded-2xl border border-slate-200/70 p-6 lg:p-8 max-w-2xl mx-auto shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Edit Soal Quiz</h2>
                        <p class="text-xs text-slate-500">Materi: {{ $soal->quiz->materi->judul_materi ?? '-' }}</p>
                    </div>
                    <a href="{{ route('superadmin.soal.index', $soal->id_quiz) }}" class="text-xs text-[#6D1837] hover:underline font-semibold">&larr; Batal</a>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-xs">
                        <p class="font-bold mb-1">Terjadi kesalahan validasi:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('superadmin.soal.update', $soal) }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pertanyaan</label>
                        <textarea name="pertanyaan" rows="3" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-[#6D1837] focus:outline-none">{{ $soal->pertanyaan }}</textarea>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700">Pilihan Jawaban &amp; Kunci Jawaban</label>
                            <span class="text-[11px] text-slate-500">Klik buletan (A/B/C/D) untuk jawaban benar</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-2.5">
                            <!-- Opsi A -->
                            <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 shadow-sm">
                                <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none">
                                    <input type="radio" name="jawaban_benar" value="a" {{ strtolower($soal->jawaban_benar) === 'a' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                    <span class="text-xs font-bold text-slate-700">A</span>
                                </label>
                                <input type="text" name="pilihan_a" value="{{ $soal->pilihan_a }}" required placeholder="Pilihan A" class="flex-1 rounded-r-xl border-0 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                            </div>

                            <!-- Opsi B -->
                            <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 shadow-sm">
                                <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none">
                                    <input type="radio" name="jawaban_benar" value="b" {{ strtolower($soal->jawaban_benar) === 'b' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                    <span class="text-xs font-bold text-slate-700">B</span>
                                </label>
                                <input type="text" name="pilihan_b" value="{{ $soal->pilihan_b }}" required placeholder="Pilihan B" class="flex-1 rounded-r-xl border-0 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                            </div>

                            <!-- Opsi C -->
                            <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 shadow-sm">
                                <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none">
                                    <input type="radio" name="jawaban_benar" value="c" {{ strtolower($soal->jawaban_benar) === 'c' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                    <span class="text-xs font-bold text-slate-700">C</span>
                                </label>
                                <input type="text" name="pilihan_c" value="{{ $soal->pilihan_c }}" required placeholder="Pilihan C" class="flex-1 rounded-r-xl border-0 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                            </div>

                            <!-- Opsi D -->
                            <div class="flex items-center bg-white rounded-xl border border-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500 shadow-sm">
                                <label class="flex items-center gap-1.5 cursor-pointer px-3 py-2 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-emerald-50 transition-colors select-none">
                                    <input type="radio" name="jawaban_benar" value="d" {{ strtolower($soal->jawaban_benar) === 'd' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                    <span class="text-xs font-bold text-slate-700">D</span>
                                </label>
                                <input type="text" name="pilihan_d" value="{{ $soal->pilihan_d }}" required placeholder="Pilihan D" class="flex-1 rounded-r-xl border-0 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#6D1837] hover:bg-[#521128] text-white text-xs font-bold py-2.5 rounded-xl transition shadow-sm cursor-pointer">Update Soal</button>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection

