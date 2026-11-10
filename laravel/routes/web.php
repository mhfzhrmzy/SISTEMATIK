<?php

use App\Http\Controllers\AdminProfilController;
use App\Http\Controllers\Auth\GuruLoginController;
use App\Http\Controllers\Auth\SiswaLoginController;
use App\Http\Controllers\Guru\MateriController;
use App\Http\Controllers\Guru\QuizController as GuruQuizController;
use App\Http\Controllers\Guru\SertifikatController;
use App\Http\Controllers\Guru\SoalController as GuruSoalController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\Siswa\MateriSiswaController;
use App\Http\Controllers\Siswa\ProfilSiswaController;
use App\Http\Controllers\Siswa\QuizSiswaController;
use App\Http\Controllers\Siswa\SertifikatSiswaController;
use App\Http\Controllers\Superadmin\GuruManajemenController;
use App\Http\Controllers\Superadmin\MateriManajemenController;
use App\Http\Controllers\Superadmin\QuizManajemenController;
use App\Http\Controllers\Superadmin\SiswaManajemenController;
use App\Http\Controllers\Superadmin\SoalManajemenController;
use App\Http\Controllers\Superadmin\SuperadminController;
use App\Http\Controllers\TtsController;
use Illuminate\Support\Facades\Route;

// ─── Halaman Utama ────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth('superadmin')->check()) {
        return redirect()->route('superadmin.index');
    }
    if (auth('admin')->check()) {
        return redirect()->route('materi.index');
    }
    if (auth('siswa')->check()) {
        return redirect()->route('portal.materi.index');
    }

    return redirect()->route('siswa.login');
})->name('home');

// ─── Leaderboard Publik ───────────────────────────────────────────────────────
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

// ─── Autentikasi Guru & Superadmin ───────────────────────────────────────────
Route::get('/login/guru', [GuruLoginController::class, 'create'])->name('admin.login');
Route::post('/login/guru', [GuruLoginController::class, 'store'])->name('admin.login.attempt');
Route::post('/logout/guru', [GuruLoginController::class, 'destroy'])->name('admin.logout');

// ─── Autentikasi Siswa ────────────────────────────────────────────────────────
Route::get('/login/siswa', [SiswaLoginController::class, 'create'])->name('siswa.login');
Route::post('/login/siswa', [SiswaLoginController::class, 'store'])->name('siswa.login.attempt');
Route::post('/logout/siswa', [SiswaLoginController::class, 'destroy'])->name('siswa.logout');

// ─── Area Guru ────────────────────────────────────────────────────────────────
Route::middleware('role:guru')->group(function () {

    // Manajemen Materi
    Route::controller(MateriController::class)->group(function () {
        Route::get('/materi', 'index')->name('materi.index');
        Route::post('/materi', 'store')->name('materi.store');
        Route::get('/materi/{materi}', 'show')->name('materi.show');
        Route::get('/materi/{materi}/edit', 'edit')->name('materi.edit');
        Route::put('/materi/{materi}', 'update')->name('materi.update');
        Route::delete('/materi/{materi}', 'destroy')->name('materi.destroy');
    });

    // Manajemen Sertifikat oleh Guru
    Route::controller(SertifikatController::class)->group(function () {
        Route::get('/sertifikat', 'index')->name('sertifikat.index');
        Route::get('/sertifikat/siswa', 'getSiswaByFilter')->name('sertifikat.siswa-json');
        Route::post('/sertifikat/create', 'create')->name('sertifikat.create');
        Route::post('/sertifikat', 'store')->name('sertifikat.store');
        Route::delete('/sertifikat/{sertifikat}', 'destroy')->name('sertifikat.destroy');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        // Manajemen Quiz
        Route::controller(GuruQuizController::class)->group(function () {
            Route::get('/quiz', 'index')->name('quiz.index');
            Route::post('/quiz', 'store')->name('quiz.store');
            Route::patch('/quiz/{quiz}/jadwal', 'updateJadwal')->name('quiz.jadwal');
            Route::delete('/quiz/{quiz}', 'destroy')->name('quiz.destroy');
        });

        // Manajemen Soal
        Route::controller(GuruSoalController::class)->group(function () {
            Route::get('/quiz/{quiz}/soal', 'index')->name('soal.index');
            Route::post('/quiz/{quiz}/soal', 'store')->name('soal.store');
            Route::patch('/quiz/{quiz}/toggle-shuffle', 'toggleShuffle')->name('quiz.toggle-shuffle');
            Route::post('/quiz/{quiz}/soal/import', 'importCsv')->name('soal.import');
            Route::get('/soal/template', 'downloadTemplate')->name('soal.template');
            Route::get('/soal/{soal}/edit', 'edit')->name('soal.edit');
            Route::put('/soal/{soal}', 'update')->name('soal.update');
            Route::delete('/soal/{soal}', 'destroy')->name('soal.destroy');
        });

        Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

        // Profil Guru
        Route::get('/profil', [AdminProfilController::class, 'show'])->name('profil');
        Route::post('/profil/foto', [AdminProfilController::class, 'updateFoto'])->name('profil.foto');
        Route::delete('/profil/foto', [AdminProfilController::class, 'hapusFoto'])->name('profil.foto.hapus');
        Route::post('/profil/password', [AdminProfilController::class, 'updatePassword'])->name('profil.password');
    });
});

// ─── Area Superadmin ──────────────────────────────────────────────────────────
Route::middleware('role:superadmin')->group(function () {

    Route::get('/superadmin', [SuperadminController::class, 'index'])->name('superadmin.index');

    // Manajemen Guru
    Route::controller(GuruManajemenController::class)->group(function () {
        Route::get('/admin', 'index')->name('admin.index');
        Route::post('/admin', 'store')->name('admin.store');
        Route::post('/admin/import', 'importCsv')->name('admin.import');
        Route::get('/admin/template', 'downloadTemplate')->name('admin.template');
        Route::get('/admin/{guru}/edit', 'edit')->name('admin.edit');
        Route::put('/admin/{guru}', 'update')->name('admin.update');
        Route::delete('/admin/{guru}', 'destroy')->name('admin.destroy');
    });

    // Manajemen Siswa
    Route::controller(SiswaManajemenController::class)->group(function () {
        Route::get('/siswa', 'index')->name('siswa.index');
        Route::post('/siswa', 'store')->name('siswa.store');
        Route::post('/siswa/import', 'importCsv')->name('siswa.import');
        Route::get('/siswa/template', 'downloadTemplate')->name('siswa.template');
        Route::get('/siswa/{siswa}/edit', 'edit')->name('siswa.edit');
        Route::put('/siswa/{siswa}', 'update')->name('siswa.update');
        Route::delete('/siswa/{siswa}', 'destroy')->name('siswa.destroy');
    });

    // Manajemen Bahan Ajar oleh Superadmin
    Route::controller(MateriManajemenController::class)->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/materi', 'index')->name('materi.index');
        Route::post('/materi', 'store')->name('materi.store');
        Route::get('/materi/{materi}', 'show')->name('materi.show');
        Route::get('/materi/{materi}/edit', 'edit')->name('materi.edit');
        Route::put('/materi/{materi}', 'update')->name('materi.update');
        Route::delete('/materi/{materi}', 'destroy')->name('materi.destroy');
    });

    // Manajemen Quiz & Soal oleh Superadmin
    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::controller(QuizManajemenController::class)->group(function () {
            Route::get('/quiz', 'index')->name('quiz.index');
            Route::post('/quiz', 'store')->name('quiz.store');
            Route::patch('/quiz/{quiz}/jadwal', 'updateJadwal')->name('quiz.jadwal');
            Route::delete('/quiz/{quiz}', 'destroy')->name('quiz.destroy');
        });

        Route::controller(SoalManajemenController::class)->group(function () {
            Route::get('/quiz/{quiz}/soal', 'index')->name('soal.index');
            Route::post('/quiz/{quiz}/soal', 'store')->name('soal.store');
            Route::patch('/quiz/{quiz}/toggle-shuffle', 'toggleShuffle')->name('quiz.toggle-shuffle');
            Route::post('/quiz/{quiz}/soal/import', 'importCsv')->name('soal.import');
            Route::get('/soal/template', 'downloadTemplate')->name('soal.template');
            Route::get('/soal/{soal}/edit', 'edit')->name('soal.edit');
            Route::put('/soal/{soal}', 'update')->name('soal.update');
            Route::delete('/soal/{soal}', 'destroy')->name('soal.destroy');
        });
    });
});

// ─── Area Siswa ───────────────────────────────────────────────────────────────
Route::middleware('role:siswa')->group(function () {

    // Profil Siswa
    Route::get('/siswa/profil', [ProfilSiswaController::class, 'index'])->name('siswa.profil');
    Route::post('/siswa/profil/foto', [ProfilSiswaController::class, 'updateFoto'])->name('siswa.profil.foto');
    Route::delete('/siswa/profil/foto', [ProfilSiswaController::class, 'hapusFoto'])->name('siswa.profil.foto.hapus');
    Route::put('/siswa/profil/password', [ProfilSiswaController::class, 'updatePassword'])->name('siswa.profil.password');

    // Materi & Quiz Siswa
    Route::prefix('portal')->name('portal.')->group(function () {
        Route::get('/materi', [MateriSiswaController::class, 'index'])->name('materi.index');
        Route::get('/materi/{materi}', [MateriSiswaController::class, 'show'])->name('materi.show');
        Route::get('/materi/{materi}/file', [MateriSiswaController::class, 'serveFile'])->name('materi.file');
        Route::post('/materi/{materi}/selesai', [MateriSiswaController::class, 'tandaiSelesai'])->name('materi.selesai');

        Route::get('/materi/{materi}/quiz', [QuizSiswaController::class, 'index'])->name('quiz.index');
        Route::get('/quiz/{quiz}', [QuizSiswaController::class, 'kerjakan'])->name('quiz.kerjakan');
        Route::post('/quiz/{quiz}', [QuizSiswaController::class, 'submit'])->name('quiz.submit');
    });

    // Sertifikat Siswa
    Route::prefix('siswa')->name('siswa.')->controller(SertifikatSiswaController::class)->group(function () {
        Route::get('/sertifikat', 'index')->name('sertifikat.index');
        Route::get('/sertifikat/create', 'create')->name('sertifikat.create');
        Route::post('/sertifikat', 'store')->name('sertifikat.store');
        Route::delete('/sertifikat/{sertifikat}', 'destroy')->name('sertifikat.destroy');
    });

    // TTS
    Route::get('/test-audio', [TtsController::class, 'testTts']);
});
