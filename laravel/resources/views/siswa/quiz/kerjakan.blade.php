@extends('layouts.app')
@section('title', 'Kerjakan Quiz — ' . ($quiz->materi->judul_materi ?? 'Quiz'))

@push('styles')
<style>
    /* Default / Unselected State */
    .answer-label {
        background-color: #F8F9FA;
        border: 1.5px solid #E5E7EB;
        transition: all 0.15s ease-in-out;
    }
    .answer-label:hover:not(.is-selected) {
        background-color: #F1F3F5;
        border-color: #CBD5E1;
    }
    .answer-label:hover:not(.is-selected) .choice-circle {
        background-color: #FFFFFF;
        border-color: #94A3B8;
        color: #1E293B !important;
    }

    /* Selected State (Clean White + Neutral Slate/Gray Border, Tanpa Stroke Maroon) */
    .answer-label.is-selected {
        background-color: #FFFFFF !important;
        border: 1.5px solid #94A3B8 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    }
    .answer-label.is-selected .choice-circle {
        background-color: #7B1038 !important;
        border-color: #7B1038 !important;
        color: #FFFFFF !important;
    }

    /* Saat kursor hover ke jawaban yang sedang dipilih */
    .answer-label.is-selected:hover {
        background-color: #FFFFFF !important;
        border-color: #64748B !important;
    }
    .answer-label.is-selected:hover .choice-circle {
        background-color: #7B1038 !important;
        border-color: #7B1038 !important;
        color: #FFFFFF !important;
    }
    .answer-label.is-selected .choice-text {
        color: #111827 !important;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="w-full h-full flex-1 flex flex-col min-h-0 gap-3 sm:gap-3.5">

    {{-- Header Quiz (Compact & Clean) --}}
    <div class="bg-white rounded-2xl shadow-xs px-5 py-3 sm:px-6 sm:py-3.5 shrink-0">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#7B1038] text-white shadow-2xs">
                        {{ ucfirst($quiz->tipe_test) }}
                    </span>
                    @if($quiz->tipe_test === 'posttest' && $quiz->jadwalLengkap())
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#E8F8F0] text-[#1F9254]">
                            Sedang Berlangsung
                        </span>
                    @endif
                </div>
                <h1 class="text-base sm:text-lg lg:text-xl font-bold text-[#1F2937] leading-tight truncate">
                    {{ $quiz->materi->judul_materi ?? 'Quiz' }}
                </h1>
                @if($quiz->tipe_test === 'posttest' && $quiz->jadwalLengkap())
                    <p class="text-xs text-[#6B7280] flex items-center gap-1.5 pt-0.5">
                        <span class="font-medium">Waktu:</span>
                        <strong class="text-[#1F2937]">{{ $quiz->waktuMulai()->format('H:i') }} – {{ $quiz->waktuSelesai()->format('H:i') }} WIB</strong>
                    </p>
                @endif
            </div>

            {{-- Pojok Kanan: Timer & Counter Pertanyaan --}}
            <div class="flex items-end flex-col gap-1 shrink-0">
                @if(isset($sisaDetik) && $sisaDetik !== null)
                    <div id="absolute-timer-box"
                         class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 text-slate-700 px-3 py-1 rounded-xl font-mono text-xs sm:text-sm font-bold shadow-2xs transition-colors"
                         data-sisa-detik="{{ $sisaDetik }}">
                        <svg class="w-3.5 h-3.5 shrink-0 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span id="absolute-countdown">{{ gmdate('H:i:s', $sisaDetik) }}</span>
                        <span class="text-[10px] font-semibold uppercase text-slate-400">sisa</span>
                    </div>
                @endif
                <div class="text-xs sm:text-sm font-normal text-[#6B7280]">
                    Pertanyaan <span id="current-question-num">1</span> dari {{ $quiz->soal->count() }}
                </div>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="w-full bg-gray-100 h-1.5 rounded-full mt-2.5 sm:mt-3 overflow-hidden">
            <div id="quiz-progress-bar"
                 class="bg-[#7B1038] h-full rounded-full transition-all duration-300 ease-out"
                 style="width: 0%;"></div>
        </div>
    </div>

    {{-- Overlay waktu habis --}}
    <div id="waktu-habis-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center max-w-sm mx-4">
            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-[#1F2937] mb-1">Waktu Habis</h3>
            <p class="text-xs sm:text-sm text-[#6B7280]">Waktu pengerjaan telah berakhir. Jawaban Anda sedang dikirim otomatis...</p>
        </div>
    </div>

    {{-- Form / Soal --}}
    @if ($quiz->soal->isEmpty())
        <div class="bg-white rounded-2xl shadow-xs p-10 text-center">
            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-[#1F2937]">Belum ada soal pada quiz ini.</p>
            <a href="{{ route('portal.quiz.index', $quiz->materi) }}"
               class="inline-flex items-center mt-4 text-xs font-semibold text-slate-700 hover:text-slate-900 hover:underline">
                <span>Kembali ke daftar quiz</span>
            </a>
        </div>
    @else
        <form action="{{ route('portal.quiz.submit', $quiz) }}" method="POST" id="quiz-form" class="flex-1 flex flex-col min-h-0">
            @csrf

            @foreach ($quiz->soal as $index => $soal)
                <div class="soal-slide bg-white rounded-2xl shadow-xs p-5 sm:p-6 flex-1 flex flex-col justify-between min-h-0 {{ $index > 0 ? 'hidden' : '' }}"
                     data-index="{{ $index }}">

                    {{-- Question text + tombol TTS --}}
                    <div class="shrink-0 mb-3.5 sm:mb-4">
                        <div class="flex items-start gap-3">
                            <p class="text-sm sm:text-base lg:text-lg font-semibold text-[#1F2937] leading-relaxed flex-1" id="question-text-{{ $index }}">
                                {{ $soal->pertanyaan }}
                            </p>
                            <button type="button"
                                    onclick="playTTS(this, @js($soal->pertanyaan), {{ $index }})"
                                    class="tts-btn shrink-0 w-8 h-8 rounded-full border border-slate-200 hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition mt-0.5"
                                    id="tts-btn-{{ $index }}"
                                    title="Dengarkan soal">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072M12 6v12M8.464 8.464C6.26 10.669 6.26 14.332 8.464 16.536"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 12a7 7 0 01-7 7m0-14a7 7 0 017 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Answer choices (Fit screen, scroll inside if exceptionally tall) --}}
                    <div class="space-y-2 sm:space-y-2.5 flex-1 min-h-0 overflow-y-auto pr-1 py-1" id="choices-{{ $index }}">
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
                            <label class="answer-label flex items-start gap-3 p-3 sm:p-3.5 rounded-xl cursor-pointer transition-all duration-150 {{ old('jawaban.'.$soal->id_soal) === $origKey ? 'is-selected' : '' }}"
                                   id="label-{{ $index }}-{{ $origKey }}">
                                <input type="radio"
                                       name="jawaban[{{ $soal->id_soal }}]"
                                       value="{{ $origKey }}"
                                       class="hidden answer-radio"
                                       id="radio-{{ $index }}-{{ $origKey }}"
                                       {{ old('jawaban.'.$soal->id_soal) === $origKey ? 'checked' : '' }}>
                                <span class="choice-circle shrink-0 w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white text-[#6B7280] border border-gray-200 flex items-center justify-center text-xs font-bold shadow-xs transition-all mt-0.5"
                                      id="circle-{{ $index }}-{{ $origKey }}">
                                    {{ $displayLetter }}
                                </span>
                                <span class="choice-text text-xs sm:text-sm font-medium text-[#1F2937] leading-relaxed flex-1 pt-0.5">
                                    {{ $choiceText }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    {{-- Navigation buttons (Always pinned at bottom, ZERO scroll needed!) --}}
                    <div class="shrink-0 flex justify-between items-center pt-3 border-t border-gray-100 gap-3 mt-auto">
                        @if ($index > 0)
                            <button type="button" onclick="prevSoal()"
                                    class="nav-btn flex items-center bg-gray-100 hover:bg-gray-200 text-slate-700 hover:text-slate-900 text-xs sm:text-sm font-semibold px-4 py-2 sm:py-2.5 rounded-xl transition">
                                <span>Sebelumnya</span>
                            </button>
                        @else
                            <div></div>
                        @endif

                        @if ($index < $quiz->soal->count() - 1)
                            <button type="button" onclick="nextSoal()"
                                    class="nav-btn flex items-center bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs sm:text-sm font-semibold px-5 py-2 sm:py-2.5 rounded-xl shadow-xs transition">
                                <span>Selanjutnya</span>
                            </button>
                        @else
                            <button type="submit"
                                    class="nav-btn flex items-center bg-[#1F9254] hover:bg-[#167442] text-white text-xs sm:text-sm font-bold px-6 py-2 sm:py-2.5 rounded-xl shadow-xs transition">
                                <span>Kumpulkan Jawaban</span>
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
const TTS_ACTIVE_CLASSES = ['bg-slate-700', 'text-white', 'border-slate-700'];

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
function updateAnswerSelection(radio) {
    if (quizLocked || !radio) return;
    const name = radio.name;
    document.querySelectorAll(`input[name="${name}"]`).forEach(inp => {
        inp.closest('.answer-label')?.classList.remove('is-selected');
    });
    if (radio.checked) {
        radio.closest('.answer-label')?.classList.add('is-selected');
    }
}

document.querySelectorAll('.answer-label').forEach(label => {
    label.addEventListener('click', () => {
        if (quizLocked) return;
        const radio = label.querySelector('.answer-radio');
        if (radio) {
            radio.checked = true;
            updateAnswerSelection(radio);
        }
    });
});

document.querySelectorAll('.answer-radio').forEach(radio => {
    radio.addEventListener('change', () => {
        updateAnswerSelection(radio);
    });
});

// Sinkronkan opsi yang sudah tercentang saat halaman dimuat
document.querySelectorAll('.answer-radio:checked').forEach(radio => {
    radio.closest('.answer-label')?.classList.add('is-selected');
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

    box.classList.remove('bg-slate-50', 'text-slate-700', 'border', 'border-slate-200',
                         'bg-red-50', 'text-red-700',
                         'bg-amber-50', 'text-amber-800');
    if (s <= 60) {
        box.classList.add('bg-red-50', 'text-red-700');
    } else if (s <= 300) {
        box.classList.add('bg-amber-50', 'text-amber-800');
    } else {
        box.classList.add('bg-slate-50', 'text-slate-700', 'border', 'border-slate-200');
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