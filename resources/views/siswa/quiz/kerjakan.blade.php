@extends('layouts.app')
@section('title', 'Kerjakan Quiz — ' . ($quiz->materi->judul_materi ?? 'Quiz'))
@section('content')
<div class="max-w-3xl mx-auto py-2 space-y-6">

    {{-- Header Quiz (Clean & Minimalis) --}}
    <div class="bg-white rounded-2xl shadow-xs p-6 sm:p-7">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-5">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-[#7B1038]/10 text-[#7B1038]">
                        {{ ucfirst($quiz->tipe_test) }}
                    </span>
                    @if($quiz->tipe_test === 'posttest' && $quiz->jadwalLengkap())
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#E8F8F0] text-[#1F9254]">
                            • Sedang Berlangsung
                        </span>
                    @endif
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#1F2937] mt-1">{{ $quiz->materi->judul_materi ?? 'Quiz' }}</h1>
                @if($quiz->tipe_test === 'posttest' && $quiz->jadwalLengkap())
                    <p class="text-xs text-[#6B7280] mt-1 flex items-center gap-1.5">
                        <span>📅 Waktu Pengerjaan:</span>
                        <strong>{{ $quiz->waktuMulai()->format('H:i') }} – {{ $quiz->waktuSelesai()->format('H:i') }} WIB</strong>
                    </p>
                @endif
            </div>

            <div class="text-left sm:text-right shrink-0">
                <div class="text-[11px] text-[#6B7280] font-semibold uppercase tracking-wider mb-0.5">Total Bobot Nilai</div>
                <div class="text-2xl font-black text-[#7B1038]">100 <span class="text-xs font-semibold text-[#6B7280]">Poin</span></div>
                @if($quiz->soal->count() > 0)
                    <div class="text-[11px] text-[#6B7280]">±{{ round(100 / $quiz->soal->count(), 2) }} poin / soal</div>
                @endif
            </div>
        </div>

        {{-- Progress Counter & Timer --}}
        <div class="mt-5 flex items-center justify-between gap-4 flex-wrap">
            <div class="text-xs sm:text-sm font-semibold text-[#1F2937]">
                Pertanyaan <span id="current-question-num" class="text-[#7B1038] font-bold text-base">1</span>
                <span class="text-[#6B7280]">/ {{ $quiz->soal->count() }}</span>
            </div>

            <div class="flex items-center gap-3">
                {{-- Timer absolut posttest --}}
                @if(isset($sisaDetik) && $sisaDetik !== null)
                    <div id="absolute-timer-box"
                         class="flex items-center gap-2 bg-[#7B1038]/10 text-[#7B1038] px-4 py-2 rounded-xl font-mono text-sm font-bold shadow-xs transition-colors"
                         data-sisa-detik="{{ $sisaDetik }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span id="absolute-countdown">{{ gmdate('H:i:s', $sisaDetik) }}</span>
                        <span class="text-[10px] font-semibold uppercase opacity-75">sisa</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="w-full bg-gray-100 h-2 rounded-full mt-3 overflow-hidden">
            <div id="quiz-progress-bar"
                 class="bg-[#7B1038] h-full rounded-full transition-all duration-300 ease-out"
                 style="width: 0%;"></div>
        </div>
    </div>

    {{-- Overlay waktu habis --}}
    <div id="waktu-habis-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center max-w-sm mx-4">
            <div class="text-4xl mb-3">⏳</div>
            <h3 class="text-lg font-bold text-[#1F2937] mb-1">Waktu Habis</h3>
            <p class="text-xs sm:text-sm text-[#6B7280]">Waktu pengerjaan telah berakhir. Jawaban Anda sedang dikirim otomatis...</p>
        </div>
    </div>

    {{-- Form / Soal --}}
    @if ($quiz->soal->isEmpty())
        <div class="bg-white rounded-2xl shadow-xs p-10 text-center">
            <div class="text-4xl mb-3">📭</div>
            <p class="text-sm font-semibold text-[#1F2937]">Belum ada soal pada quiz ini.</p>
            <a href="{{ route('portal.quiz.index', $quiz->materi) }}"
               class="inline-block mt-4 text-xs font-semibold text-[#7B1038] hover:underline">
                ← Kembali ke daftar quiz
            </a>
        </div>
    @else
        <form action="{{ route('portal.quiz.submit', $quiz) }}" method="POST" id="quiz-form">
            @csrf

            @foreach ($quiz->soal as $index => $soal)
                <div class="soal-slide bg-white rounded-2xl shadow-xs p-6 sm:p-7 {{ $index > 0 ? 'hidden' : '' }}"
                     data-index="{{ $index }}">

                    {{-- Question number + text + tombol TTS --}}
                    <div class="mb-5">
                        <span class="text-xs font-bold text-[#7B1038] uppercase tracking-wider">
                            Pertanyaan {{ $index + 1 }} dari {{ $quiz->soal->count() }}
                        </span>
                        <div class="flex items-start gap-2 mt-2">
                            <p class="text-base sm:text-lg font-semibold text-[#1F2937] leading-relaxed flex-1" id="question-text-{{ $index }}">
                                {{ $soal->pertanyaan }}
                            </p>
                            <button type="button"
                                    onclick="playTTS(this, @js($soal->pertanyaan), {{ $index }})"
                                    class="tts-btn shrink-0 w-8 h-8 rounded-full border border-[#7B1038]/30 hover:bg-[#7B1038]/10 text-[#7B1038] flex items-center justify-center transition"
                                    id="tts-btn-{{ $index }}"
                                    title="Dengarkan soal">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072M12 6v12M8.464 8.464C6.26 10.669 6.26 14.332 8.464 16.536"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 12a7 7 0 01-7 7m0-14a7 7 0 017 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Answer choices --}}
                    <div class="space-y-3 mb-6" id="choices-{{ $index }}">
                        @php
                            $optionLabels = ['A', 'B', 'C', 'D'];
                            $optIdx = 0;
                            $choices = $soal->display_choices ?? [
                                'a' => $soal->pilihan_a,
                                'b' => $soal->pilihan_b,
                                'c' => $soal->pilihan_c,
                                'd' => $soal->pilihan_d,
                            ];
                        @endphp
                        @foreach ($choices as $origKey => $choiceText)
                            @php
                                $displayLetter = $optionLabels[$optIdx] ?? strtoupper($origKey);
                                $optIdx++;
                            @endphp
                            <label class="answer-label flex items-start gap-3.5 p-4 rounded-xl cursor-pointer bg-[#F8F9FA] hover:bg-gray-100 transition-all duration-150 group"
                                   id="label-{{ $index }}-{{ $origKey }}">
                                <input type="radio"
                                       name="jawaban[{{ $soal->id_soal }}]"
                                       value="{{ $origKey }}"
                                       class="hidden answer-radio"
                                       id="radio-{{ $index }}-{{ $origKey }}">
                                <span class="choice-circle shrink-0 w-8 h-8 rounded-xl bg-white text-[#6B7280] flex items-center justify-center text-xs font-bold shadow-xs group-hover:text-[#7B1038] transition-all"
                                      id="circle-{{ $index }}-{{ $origKey }}">
                                    {{ $displayLetter }}
                                </span>
                                <span class="text-sm font-medium text-[#1F2937] pt-1 leading-relaxed flex-1">
                                    {{ $choiceText }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    {{-- Navigation buttons --}}
                    <div class="flex justify-between items-center pt-4 border-t border-gray-100 gap-3">
                        @if ($index > 0)
                            <button type="button" onclick="prevSoal()"
                                    class="nav-btn flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-[#5E0C2B] text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                                ← Sebelumnya
                            </button>
                        @else
                            <div></div>
                        @endif

                        @if ($index < $quiz->soal->count() - 1)
                            <button type="button" onclick="nextSoal()"
                                    class="nav-btn flex items-center gap-1.5 bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs sm:text-sm font-semibold px-5 py-2.5 rounded-xl shadow-xs transition">
                                Selanjutnya →
                            </button>
                        @else
                            <button type="submit"
                                    class="nav-btn flex items-center gap-1.5 bg-[#1F9254] hover:bg-[#167442] text-white text-xs sm:text-sm font-bold px-6 py-2.5 rounded-xl shadow-xs transition">
                                🚀 Kumpulkan Jawaban
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </form>
    @endif
</div>

@push('scripts')
<script>
let currentIdx = 0;
const slides = document.querySelectorAll('.soal-slide');
const totalSoal = slides.length;
let absoluteTimerInterval = null;
let absoluteSecondsLeft = null;
let quizLocked = false;

// ===== TTS =====
const ttsCache = new Map();
let currentAudio = null;
let currentTtsBtn = null;
const TTS_ACTIVE_CLASSES = ['bg-[#7B1038]/10', 'border-[#7B1038]'];

function resetTtsBtn(btn) {
    if (!btn) return;
    btn.disabled = false;
    btn.classList.remove(...TTS_ACTIVE_CLASSES);
    btn.innerHTML = _ttsIconPlay();
}

function startAudio(btn, url) {
    const audio = new Audio(url);
    currentAudio = audio;
    currentTtsBtn = btn;
    btn.disabled = false;
    btn.classList.add(...TTS_ACTIVE_CLASSES);
    btn.innerHTML = _ttsIconPause();
    audio.play();
    audio.addEventListener('ended', () => {
        if (currentAudio === audio) {
            currentAudio = null;
            currentTtsBtn = null;
        }
        resetTtsBtn(btn);
    });
}

function playTTS(btn, text, index) {
    // Klik tombol yang sedang diputar = stop
    if (currentAudio && currentTtsBtn === btn) {
        currentAudio.pause();
        currentAudio = null;
        currentTtsBtn = null;
        resetTtsBtn(btn);
        return;
    }

    // Klik tombol lain = hentikan audio sebelumnya
    if (currentAudio && currentTtsBtn && currentTtsBtn !== btn) {
        currentAudio.pause();
        resetTtsBtn(currentTtsBtn);
        currentAudio = null;
        currentTtsBtn = null;
    }

    // Pakai cache kalau sudah pernah dimuat
    if (ttsCache.has(index)) {
        startAudio(btn, ttsCache.get(index));
        return;
    }

    btn.disabled = true;
    btn.innerHTML = _ttsIconLoading();

    fetch('{{ url("api/tts") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({text: text})
    })
    .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.blob();
    })
    .then(blob => {
        if (blob.size === 0) throw new Error('Audio kosong');
        const url = URL.createObjectURL(blob);
        ttsCache.set(index, url);
        startAudio(btn, url);
    })
    .catch(err => {
        console.error('[TTS]', err);
        alert('Gagal memuat audio: ' + err.message);
        resetTtsBtn(btn);
        currentTtsBtn = null;
    });
}

function _ttsIconPlay() {
    return `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072M12 6v12M8.464 8.464C6.26 10.669 6.26 14.332 8.464 16.536"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 12a7 7 0 01-7 7m0-14a7 7 0 017 7"/></svg>`;
}
function _ttsIconPause() {
    return `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>`;
}
function _ttsIconLoading() {
    return `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>`;
}

// ===== Timer =====
// Inisialisasi timer absolut dari data-sisa-detik (server-derived)
const absoluteTimerBox = document.getElementById('absolute-timer-box');
if (absoluteTimerBox) {
    absoluteSecondsLeft = parseInt(absoluteTimerBox.getAttribute('data-sisa-detik')) || 0;
}

// Style answer labels with selected state
document.querySelectorAll('.answer-label').forEach(label => {
    label.addEventListener('click', () => {
        if (quizLocked) return;
        const name = label.querySelector('input').name;
        // Reset all in this question
        document.querySelectorAll(`input[name="${name}"]`).forEach(inp => {
            const lbl = inp.closest('label');
            const circle = lbl.querySelector('.choice-circle');
            lbl.classList.remove('bg-[#7B1038]/10', 'ring-2', 'ring-[#7B1038]');
            lbl.classList.add('bg-[#F8F9FA]');
            circle.classList.remove('bg-[#7B1038]', 'text-white');
            circle.classList.add('bg-white', 'text-[#6B7280]');
        });
        // Style selected
        const circle = label.querySelector('.choice-circle');
        label.classList.remove('bg-[#F8F9FA]');
        label.classList.add('bg-[#7B1038]/10', 'ring-2', 'ring-[#7B1038]');
        circle.classList.remove('bg-white', 'text-[#6B7280]');
        circle.classList.add('bg-[#7B1038]', 'text-white');
    });
});

function lockQuizAndSubmit() {
    if (quizLocked) return;
    quizLocked = true;

    // Hentikan audio TTS kalau masih jalan
    if (currentAudio) { currentAudio.pause(); currentAudio = null; }

    // Kunci semua input
    document.querySelectorAll('.answer-radio').forEach(r => r.disabled = true);
    document.querySelectorAll('.nav-btn').forEach(b => b.disabled = true);

    // Tampilkan overlay
    const overlay = document.getElementById('waktu-habis-overlay');
    if (overlay) {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    }

    clearInterval(absoluteTimerInterval);

    // Submit form
    setTimeout(() => {
        document.getElementById('quiz-form').submit();
    }, 500);
}

// Timer absolut posttest — hitung mundur dari waktu_selesai jadwal
function startAbsoluteTimer() {
    if (absoluteSecondsLeft === null) return;

    updateAbsoluteDisplay();

    absoluteTimerInterval = setInterval(() => {
        if (quizLocked) { clearInterval(absoluteTimerInterval); return; }
        absoluteSecondsLeft--;
        updateAbsoluteDisplay();

        if (absoluteSecondsLeft <= 0) {
            clearInterval(absoluteTimerInterval);
            lockQuizAndSubmit();
        }
    }, 1000);
}

function updateAbsoluteDisplay() {
    const el = document.getElementById('absolute-countdown');
    const box = document.getElementById('absolute-timer-box');
    if (!el || absoluteSecondsLeft === null) return;

    const s = Math.max(0, absoluteSecondsLeft);
    const hh = String(Math.floor(s / 3600)).padStart(2, '0');
    const mm = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
    const ss = String(s % 60).padStart(2, '0');
    el.innerText = hh + ':' + mm + ':' + ss;

    box.classList.remove('bg-[#7B1038]/10', 'text-[#7B1038]',
                         'bg-red-50', 'text-red-700',
                         'bg-amber-50', 'text-amber-800');
    if (s <= 60) {
        box.classList.add('bg-red-50', 'text-red-700');
    } else if (s <= 300) {
        box.classList.add('bg-amber-50', 'text-amber-800');
    } else {
        box.classList.add('bg-[#7B1038]/10', 'text-[#7B1038]');
    }
}

// ===== Navigasi soal =====
function showSlide(index) {
    // Hentikan audio saat pindah soal
    if (currentAudio) {
        currentAudio.pause();
        currentAudio = null;
        resetTtsBtn(currentTtsBtn);
        currentTtsBtn = null;
    }

    slides.forEach((s, idx) => {
        s.classList.toggle('hidden', idx !== index);
    });
    document.getElementById('current-question-num').innerText = index + 1;
    const pct = ((index + 1) / totalSoal) * 100;
    document.getElementById('quiz-progress-bar').style.width = pct + '%';
    currentIdx = index;
}

function nextSoal() {
    if (quizLocked) return;
    if (currentIdx < totalSoal - 1) { showSlide(currentIdx + 1); }
}

function prevSoal() {
    if (quizLocked) return;
    if (currentIdx > 0) { showSlide(currentIdx - 1); }
}

document.addEventListener('DOMContentLoaded', () => {
    if (totalSoal > 0) { showSlide(0); }
    startAbsoluteTimer();
});
</script>
@endpush
@endsection