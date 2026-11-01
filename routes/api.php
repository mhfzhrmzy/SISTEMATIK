<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaderboardController as LeaderboardApiController;
use App\Http\Controllers\Api\MateriController as MateriApiController;
use App\Http\Controllers\Api\QuizController as QuizApiController;
use App\Http\Controllers\Api\SertifikatController as SertifikatApiController;
use App\Http\Controllers\Api\TtsController as ApiTtsController; // Diimpor dengan alias ApiTtsController
use Illuminate\Support\Facades\Route;

// === PUBLIC ROUTES ===
Route::post('/login/siswa', [AuthController::class, 'loginSiswa']);
Route::post('/login/guru', [AuthController::class, 'loginGuru']);

// TTS Public 
Route::post('/tts', [ApiTtsController::class, 'synthesize']);


// === SISWA (token auth) ===
Route::middleware('auth:sanctum')->prefix('siswa')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profil', [AuthController::class, 'profilSiswa']);
    Route::post('/profil/foto', [AuthController::class, 'updateFotoSiswa']);
    Route::put('/profil/password', [AuthController::class, 'updatePasswordSiswa']);

    // Materi
    Route::get('/materi', [MateriApiController::class, 'index']);
    Route::get('/materi/{materi}', [MateriApiController::class, 'show']);
    Route::get('/materi/{materi}/file', [MateriApiController::class, 'serveFile']);

    // Quiz
    Route::get('/materi/{materi}/quiz', [QuizApiController::class, 'index']);
    Route::get('/quiz/{quiz}', [QuizApiController::class, 'kerjakan']);
    Route::post('/quiz/{quiz}', [QuizApiController::class, 'submit']);

    // Leaderboard
    Route::get('/leaderboard', [LeaderboardApiController::class, 'index']);

    // Sertifikat
    Route::get('/sertifikat', [SertifikatApiController::class, 'index']);
    Route::get('/sertifikat/{sertifikat}/download', [SertifikatApiController::class, 'download']);
    
});
