@extends('layouts.app')

@section('title', 'Leaderboard Posttest')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h1 class="text-xl font-semibold">Leaderboard Posttest</h1>
            <p class="text-sm text-gray-500">Data otomatis diperbarui setiap 3 detik.</p>
        </div>
        <select id="quizFilter" class="rounded-md border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua Posttest</option>
            @foreach($quizzes as $quiz)
                <option value="{{ $quiz->id_quiz }}" @selected((string)$selectedQuiz === (string)$quiz->id_quiz)>
                    {{ $quiz->materi?->judul_materi }} — Posttest
                </option>
            @endforeach
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead><tr class="border-b bg-gray-50">
                <th class="px-3 py-2">#</th><th class="px-3 py-2">Siswa</th><th class="px-3 py-2">NISN</th>
                <th class="px-3 py-2">Materi</th><th class="px-3 py-2">Poin</th><th class="px-3 py-2">Terakhir</th>
            </tr></thead>
            <tbody id="leaderboardBody">
                <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">Memuat...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
const endpoint = @json(route('admin.leaderboard.data'));
const body = document.getElementById('leaderboardBody');
const filter = document.getElementById('quizFilter');

async function loadLeaderboard() {
    const url = new URL(endpoint, window.location.origin);
    if (filter.value) url.searchParams.set('quiz_id', filter.value);

    try {
        const response = await fetch(url, {headers: {'Accept': 'application/json'}});
        const data = await response.json();

        if (!data.length) {
            body.innerHTML = '<tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">Belum ada siswa yang mengerjakan posttest.</td></tr>';
            return;
        }

        body.innerHTML = data.map(item => `
            <tr class="border-b">
                <td class="px-3 py-2 font-semibold">${item.rank}</td>
                <td class="px-3 py-2">${escapeHtml(item.nama)}</td>
                <td class="px-3 py-2">${escapeHtml(String(item.nisn))}</td>
                <td class="px-3 py-2">${escapeHtml(item.quiz)}</td>
                <td class="px-3 py-2 font-semibold">${item.poin}</td>
                <td class="px-3 py-2">${item.updated_at ?? '-'}</td>
            </tr>`).join('');
    } catch (error) {
        body.innerHTML = '<tr><td colspan="6" class="px-3 py-6 text-center text-red-500">Gagal memuat data.</td></tr>';
    }
}

function escapeHtml(value) {
    return value.replace(/[&<>"']/g, char => ({
        '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;'
    }[char]));
}

filter.addEventListener('change', loadLeaderboard);
loadLeaderboard();
setInterval(loadLeaderboard, 3000);
</script>
@endsection
