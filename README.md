# SISTEMATIK — Semester 3
**Pengembangan Media Pembelajaran Digital Mata Pelajaran TIK Berbasis Website dan Mobile Application untuk Siswa SMKN 2 Jember**

---

Monorepo proyek sistem pembelajaran online untuk siswa **SMKN 2 Jember**
(mata pelajaran **TIK** — Teknologi Informasi & Komunikasi): siswa mengerjakan
**Pre-Test** sebelum membuka materi dan **Post-Test** setelah belajar, lengkap
dengan leaderboard, sertifikat, serta bank soal yang dikelola guru/superadmin.

## Struktur Repository

```
semester3testdatabase/
├── laravel/    # Backend + aplikasi web (Laravel, PHP 8.3, MySQL, Blade + Tailwind)
├── mobile/     # Aplikasi Flutter (UI Pre-Test/Post-Test, saat ini frontend-only)
└── README.md   # Dokumen ini
```

| Folder | Teknologi | Peran |
|--------|-----------|-------|
| [`laravel/`](./laravel/README.md) | Laravel 13, Sanctum, MySQL, Blade, Tailwind 4 | REST API + web (guru, siswa, superadmin) |
| [`mobile/`](./mobile/README.md) | Flutter / Dart | Klien UI Pre-Test & Post-Test |

## Peran Pengguna

| Peran | Guard | Hak Akses |
|-------|-------|-----------|
| **Superadmin** | `superadmin` | Kelola guru, siswa (import CSV), materi, quiz, dan soal |
| **Guru** | `guru` | Kelola materi, quiz (pretest/posttest + jadwal), soal, penerbitan sertifikat |
| **Siswa** | `siswa` | Portal materi, kerjakan pretest/posttest, leaderboard, sertifikat, profil |

## Fitur Utama

- Autentikasi terpisah per peran (session-based, middleware `role:*`).
- Materi + **preview dokumen** (PDF — lihat [`laravel/README.txt`](./laravel/README.txt)).
- Quiz Pre-Test & Post-Test: timer, poin, acak soal, **jadwal Post-Test** dengan penguncian waktu.
- **Leaderboard publik** (poin pretest/posttest per siswa, kelas, dan jurusan).
- Sertifikat: guru menerbitkan, siswa mengunduh.
- **REST API (Sanctum)** di `/api` untuk klien mobile: login, profil, materi, quiz, leaderboard, sertifikat, dan TTS.
- Fitur TTS (text-to-speech) dengan cache audio.

## Cara Menjalankan

### Backend (Laravel)

```bash
cd laravel
composer install
cp .env.example .env          # sesuaikan kredensial MySQL
php artisan key:generate
php artisan migrate --seed    # termasuk akun demo (lihat laravel/README.md)
npm install && npm run build  # build Tailwind
php artisan serve             # http://127.0.0.1:8000
```

Atau satu perintah: `composer setup` (sudah mencakup install, env, migrate, build).

### Frontend (Flutter)

```bash
cd mobile
flutter pub get
flutter run                   # device/emulator
```

## Pengujian

```bash
cd laravel && php artisan test    # feature tests (auth, role, quiz, leaderboard, dll.)
cd mobile   && flutter test       # widget/unit tests
```

## Dokumen Lain

- [`laravel/README.md`](./laravel/README.md) — panduan lengkap backend, API, dan akun demo.
- [`laravel/README.txt`](./laravel/README.txt) — patch fitur preview dokumen (LibreOffice).
- [`mobile/README.md`](./mobile/README.md) — arsitektur dan fitur aplikasi Flutter.
- [`mobile/CODING_STANDARD.md`](./mobile/CODING_STANDARD.md) — standar penamaan & struktur kode Flutter.
- [`mobile/REFACTOR_PROGRESS.md`](./mobile/REFACTOR_PROGRESS.md) — status refactor frontend Flutter.
