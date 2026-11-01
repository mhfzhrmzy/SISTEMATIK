@extends('layouts.app')
@section('title', 'Kerjakan Quiz — ' . ($quiz->materi->judul_materi ?? 'Quiz'))
@section('content')
<div class="max-w-2xl mx-auto py-2">

    {{-- Header Quiz --}}
    <div class="rounded-2xl border border-gray-200 shadow-sm bg-white p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-5">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $quiz->tipe_test === 'pretest' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800' }}">
                    {{ ucfirst($quiz->tipe_test) }}
                </span>
                @if($quiz->tipe_test === 'posttest' && $quiz->jadwalLengkap())
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 ml-1">Sedang berlangsung</span>
                @endif
                <h1 class="text-xl font-bold text-gray-800 mt-2">{{ $quiz->materi->judul_materi ?? 'Quiz' }}</h1>
                @if($quiz->tipe_test === 'posttest' && $quiz->jadwalLengkap())
                    <p class="text-xs text-gray-500 mt-1">
                        📅 {{ $quiz->waktuMulai()->format('H:i') }} – {{ $quiz->waktuSelesai()->format('H:i') }} WIB
                    </p>
                @endif
            </div>
            <div class="text-right shrink-0">
                <div class="text-xs text-gray-400 mb-0.5">Total Bobot Nilai</div>
                <div class="text-2xl font-black text-blue-600">100 <span class="text-sm font-semibold text-gray-400">Poin</span></div>
                @if($quiz->soal->count() > 0)
                    <div class="text-[11px] text-gray-400 font-medium">±{{ round(100 / $quiz->soal->count(), 2) }} poin / soal</div>
                @endif
            </div>
        </div>

        {{-- Progress & Timer --}}
        <div class="mt-5 flex items-center justify-between gap-4">
            <div class="text-sm font-semibold text-gray-600">
                Soal <span id="current-question-num" class="text-blue-600 font-bold">1</span>
                <span class="text-gray-400">/ {{ $quiz->soal->count() }}</span>
            </div>

            <div class="flex items-center gap-3">
                {{-- Timer absolut posttest --}}
                @if(isset($sisaDetik) && $sisaDetik !== null)
                    <div id="absolute-timer-box"
                         class="flex items-center gap-2 bg-indigo-50 border border-indigo-200 text-indigo-700 px-4 py-2 rounded-full font-mono text-sm font-bold shadow-sm"
                         data-sisa-detik="{{ $sisaDetik }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span id="absolute-countdown">{{ gmdate('H:i:s', $sisaDetik) }}</span>
                        <span class="text-xs font-semibold opacity-70">sisa</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="w-full bg-gray-100 h-2.5 rounded-full mt-3 overflow-hidden">
            <div id="quiz-progress-bar"
                 class="bg-gradient-to-r from-blue-500 to-indigo-600 h-full rounded-full transition-all duration-500 ease-out"
                 style="width: 0%;"></div>
        </div>
    </div>

    {{-- Overlay waktu habis --}}
    <div id="waktu-habis-overlay" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center max-w-sm mx-4">
            <div class="text-5xl mb-3">⏳</div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Waktu habis</h3>
            <p class="text-sm text-gray-500">Jawaban sedang dikirim...</p>
        </div>
    </div>

    {{-- Form / Soal --}}
    @if ($quiz->soal->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-10 text-center">
            <div class="text-4xl mb-3">📭</div>
            <p class="text-gray-500 font-medium">Belum ada soal pada quiz ini.</p>
            <a href="{{ route('portal.quiz.index', $quiz->materi) }}"
               class="inline-block mt-4 text-sm text-blue-600 hover:underline">← Kembali ke daftar quiz</a>
        </div>
    @else
        <form action="{{ route('portal.quiz.submit', $quiz) }}" method="POST" id="quiz-form">
            @csrf

            @foreach ($quiz->soal as $index => $soal)
                <div class="soal-slide bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-4 {{ $index > 0 ? 'hidden' : '' }}"
                     data-index="{{ $index }}">

                    {{-- Question number + text --}}
                    <div class="mb-5">
                        <span class="text-xs font-bold text-blue-500 uppercase tracking-widest">Pertanyaan {{ $index + 1 }}</span>
                        <div class="flex items-start gap-2 mt-2">
                            <p class="text-base font-semibold text-gray-800 leading-relaxed flex-1" id="question-text-{{ $index }}">
                                {{ $soal->pertanyaan }}
                            </p>
                            <button type="button"
                                    onclick="playTTS(this, '{{ $soal->pertanyaan }}', {{ $index }})"
                                    class="tts-btn shrink-0 w-8 h-8 rounded-full border border-blue-200 hover:bg-blue-50 text-blue-500 hover:text-blue-700 flex items-center justify-center transition"
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
                            <label class="answer-label flex items-start gap-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer
                                          hover:border-blue-400 hover:bg-blue-50 transition-all duration-150 group"
                                   id="label-{{ $index }}-{{ $origKey }}">
                                <input type="radio"
                                       name="jawaban[{{ $soal->id_soal }}]"
                                       value="{{ $origKey }}"
                                       class="hidden answer-radio"
                                       id="radio-{{ $index }}-{{ $origKey }}">
                                <span class="flex-shrink-0 w-7 h-7 rounded-full border-2 border-gray-300 flex items-center justify-center
                                             text-xs font-bold text-gray-500 group-hover:border-blue-500 group-hover:text-blue-600
                                             transition-all duration-150 choice-circle"
                                      id="circle-{{ $index }}-{{ $origKey }}">
                                    {{ $displayLetter }}
                                </span>
                                <span class="text-sm text-gray-700 group-hover:text-gray-900 pt-0.5 leading-relaxed">
                                    {{ $choiceText }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    {{-- Navigation buttons --}}
                    <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                        @if ($index > 0)
                            <button type="button" onclick="prevSoal()"
                                    class="nav-btn flex items-center gap-1.5 border border-gray-300 hover:bg-gray-100 text-gray-600 text-sm font-semibold px-4 py-2 rounded-lg transition">
                                ← Sebelumnya
                            </button>
                        @else
                            <div></div>
                        @endif

                        @if ($index < $quiz->soal->count() - 1)
                            <button type="button" onclick="nextSoal()"
                                    class="nav-btn flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg shadow transition">
                                Selanjutnya →
                            </button>
                        @else
                            <button type="submit"
                                    class="nav-btn flex items-center gap-1.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white text-sm font-bold px-6 py-2.5 rounded-lg shadow-md transition">
                                🚀 Kumpulkan Jawaban
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </form>
    @endif
</div>

<script>
let currentIdx = 0;
const slides = document.querySelectorAll('.soal-slide');
const totalSoal = slides.length;
let absoluteTimerInterval = null;
let absoluteSecondsLeft = null;
let quizLocked = false;

const ttsCache = new Map();
let currentAudio = null;
let currentTtsBtn = null;

function playTTS(btn, text, index) {
    if (currentAudio && currentTtsBtn && currentTtsBtn !== btn) {
        currentAudio.pause();
        currentAudio = null;
        if (currentTtsBtn) {
            currentTtsBtn.classList.remove('bg-blue-100', 'border-blue-400');
            currentTtsBtn.disabled = false;
            currentTtsBtn.innerHTML = _ttsIconPlay();
        }
    }

    if (currentAudio && currentTtsBtn === btn) {
        currentAudio.pause();
        currentAudio = null;
        currentTtsBtn = null;
        btn.disabled = false;
        btn.classList.remove('bg-blue-100', 'border-blue-400');
        btn.innerHTML = _ttsIconPlay();
        return;
    }

    if (ttsCache.has(index)) {
        const audio = new Audio(ttsCache.get(index));
        currentAudio = audio;
        currentTtsBtn = btn;
        btn.disabled = true;
        btn.classList.add('bg-blue-100', 'border-blue-400');
        btn.innerHTML = _ttsIconPause();
        audio.play();
        audio.addEventListener('ended', () => {
            if (currentAudio === audio) {
                currentAudio = null;
                if (currentTtsBtn === btn) {
                    btn.disabled = false;
                    btn.classList.remove('bg-blue-100', 'border-blue-400');
                    btn.innerHTML = _ttsIconPlay();
                }
                currentTtsBtn = null;
            }
        });
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
        const audio = new Audio(url);
        currentAudio = audio;
        currentTtsBtn = btn;
        btn.innerHTML = _ttsIconPause();
        btn.classList.add('bg-blue-100', 'border-blue-400');
        audio.play();
        audio.addEventListener('ended', () => {
            if (currentAudio === audio) {
                currentAudio = null;
                if (currentTtsBtn === btn) {
                    btn.disabled = false;
                    btn.classList.remove('bg-blue-100', 'border-blue-400');
                    btn.innerHTML = _ttsIconPlay();
                }
                currentTtsBtn = null;
            }
        });
    })
    .catch(err => {
        console.error('[TTS]', err);
        alert('Gagal memuat audio: ' + err.message);
        btn.disabled = false;
        btn.classList.remove('bg-blue-100', 'border-blue-400');
        btn.innerHTML = _ttsIconPlay();
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
            lbl.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
            lbl.classList.add('border-gray-200');
            circle.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
            circle.classList.add('border-gray-300', 'text-gray-500');
        });
        // Style selected
        const circle = label.querySelector('.choice-circle');
        label.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
        label.classList.remove('border-gray-200');
        circle.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
        circle.classList.remove('border-gray-300', 'text-gray-500');
    });
});

function lockQuizAndSubmit() {
    if (quizLocked) return;
    quizLocked = true;

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

    box.classList.remove('bg-indigo-50', 'border-indigo-200', 'text-indigo-700',
                         'bg-red-50', 'border-red-200', 'text-red-700',
                         'bg-amber-50', 'border-amber-200', 'text-amber-700');
    if (s <= 60) {
        box.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
    } else if (s <= 300) {
        box.classList.add('bg-amber-50', 'border-amber-200', 'text-amber-700');
    } else {
        box.classList.add('bg-indigo-50', 'border-indigo-200', 'text-indigo-700');
    }
}

function showSlide(index) {
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
@endsection
