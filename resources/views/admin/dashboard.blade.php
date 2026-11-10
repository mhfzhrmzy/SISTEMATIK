@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold">Dashboard Admin</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola materi, quiz, siswa, dan hasil posttest.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="{{ route('materi.index') }}" class="rounded-lg border p-5 hover:bg-gray-50">
            <p class="text-sm text-gray-500">Materi</p>
            <p class="text-2xl font-semibold mt-1">{{ $totalMateri }}</p>
        </a>
        <a href="{{ route('admin.quiz.index') }}" class="rounded-lg border p-5 hover:bg-gray-50">
            <p class="text-sm text-gray-500">Quiz</p>
            <p class="text-2xl font-semibold mt-1">{{ $totalQuiz }}</p>
        </a>
        <a href="{{ route('admin.siswa.index') }}" class="rounded-lg border p-5 hover:bg-gray-50">
            <p class="text-sm text-gray-500">Siswa Terdaftar</p>
            <p class="text-2xl font-semibold mt-1">{{ $totalSiswa }}</p>
        </a>
        <a href="{{ route('admin.leaderboard.index') }}" class="rounded-lg border p-5 hover:bg-gray-50">
            <p class="text-sm text-gray-500">Hasil Posttest</p>
            <p class="text-2xl font-semibold mt-1">{{ $totalHasilPosttest }}</p>
        </a>
    </div>
</div>
@endsection
