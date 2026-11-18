@extends('layouts.app')

@section('title', 'Kelola Data Quiz')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Kelola Data Quiz
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Manajemen quiz pre-test dan post-test untuk bahan ajar siswa.
            </p>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form Tambah Quiz Baru -->
    <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80">
        <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="w-2 h-5 bg-[#7B1038] rounded-full inline-block"></span>
            Tambah Quiz Baru
        </h2>

        <form action="{{ route('quiz.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">ID Quiz</label>
                    <input type="text" name="id_quiz" placeholder="Contoh: QZ001" required value="{{ old('id_quiz') }}"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#7B1038] focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Guru (NIP)</label>
                    <select name="nip" required
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#7B1038] focus:border-transparent transition">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->nip }}" {{ old('nip') == $guru->nip ? 'selected' : '' }}>
                                {{ $guru->nama_lengkap }} ({{ $guru->nip }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Materi</label>
                    <select name="id_materi" required
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#7B1038] focus:border-transparent transition">
                        <option value="">-- Pilih Materi --</option>
                        @foreach($materis as $materi)
                            <option value="{{ $materi->id_materi }}" {{ old('id_materi') == $materi->id_materi ? 'selected' : '' }}>
                                {{ $materi->judul_materi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tipe Test</label>
                    <select name="tipe_test" required
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#7B1038] focus:border-transparent transition">
                        <option value="pre_test" {{ old('tipe_test') == 'pre_test' ? 'selected' : '' }}>Pre Test</option>
                        <option value="post_test" {{ old('tipe_test') == 'post_test' ? 'selected' : '' }}>Post Test</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Poin</label>
                    <input type="number" name="poin" min="0" required value="{{ old('poin', 100) }}"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#7B1038] focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Timer (Menit)</label>
                    <input type="number" name="timer" min="1" required value="{{ old('timer', 30) }}"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#7B1038] focus:border-transparent transition">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" required value="{{ old('tanggal') }}"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#7B1038] focus:border-transparent transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kunci Jawaban</label>
                <textarea name="kunci_jawaban" rows="2" placeholder="Masukkan kunci jawaban..." required
                          class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#7B1038] focus:border-transparent transition">{{ old('kunci_jawaban') }}</textarea>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="bg-[#7B1038] hover:bg-[#5E0C2B] text-white font-semibold px-5 py-2.5 rounded-xl text-sm shadow-xs transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Simpan Quiz
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Quiz -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base sm:text-lg font-bold text-slate-900">
                Daftar Quiz
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">ID Quiz</th>
                        <th class="px-5 py-3.5">Guru / NIP</th>
                        <th class="px-5 py-3.5">Materi</th>
                        <th class="px-5 py-3.5">Tipe</th>
                        <th class="px-5 py-3.5 text-center">Poin</th>
                        <th class="px-5 py-3.5 text-center">Timer</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($quizzes as $q)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-4 font-bold text-slate-900">{{ $q->id_quiz }}</td>
                            <td class="px-5 py-4">{{ $q->guru->nama_lengkap ?? $q->nip }}</td>
                            <td class="px-5 py-4 font-medium">{{ $q->materi->judul_materi ?? $q->id_materi }}</td>
                            <td class="px-5 py-4">
                                @if($q->tipe_test == 'pre_test')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        PRE TEST
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        POST TEST
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center font-semibold">{{ $q->poin }}</td>
                            <td class="px-5 py-4 text-center">{{ $q->timer }} Menit</td>
                            <td class="px-5 py-4 text-slate-600">{{ $q->tanggal }}</td>
                            <td class="px-5 py-4 text-center">
                                <form action="{{ route('quiz.destroy', $q->id_quiz) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus quiz ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-slate-400">
                                Belum ada data quiz.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($quizzes, 'links'))
            <div class="p-4 border-t border-slate-100">
                {{ $quizzes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
