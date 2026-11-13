# Laravel — Backend & Web Sistem Pre-Test / Post-Test

Aplikasi web (Blade + Tailwind) sekaligus REST API untuk sistem pembelajaran
Pre-Test & Post-Test mata pelajaran **TIK** siswa **SMKN 2 Jember**.
Dibangun dengan **Laravel 13**, **PHP 8.3**, **MySQL**, dan **Laravel Sanctum**
untuk autentikasi API.

Repo ini adalah bagian dari monorepo [`semester3testdatabase`](../README.md)
(`laravel/` = backend, `mobile/` = klien Flutter).

---

## Kebutuhan

- PHP 8.3 + ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`
- Composer 2
- MySQL 8 (atau MariaDB setara)
- Node.js + npm (hanya untuk build CSS Tailwind)
- **LibreOffice** (opsional) — untuk fitur preview dokumen PPTX/DOC/DOCX,
  lihat [`README.txt`](./README.txt)

---

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Sesuaikan kredensial database di `.env` (default bawaan `.env.example`):

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=semester3testdatabase
DB_USERNAME=root
DB_PASSWORD=
```

Lalu migrate + seed, dan build aset frontend:

```bash
php artisan migrate --seed
npm install
npm run build          # atau: npm run build:css
php artisan serve      # http://127.0.0.1:8000
```

Satu perintah (mencakup install → env → key → migrate → build):

```bash
composer setup
```

---

## Akun Demo (dari Seeder)

Semua password: **`password123`**

| Peran | Login | Contoh |
|-------|-------|--------|
| Superadmin | NIP | `123456789012345678` |
| Guru | NIP | `198501012010011001` (Budi Santoso), `198803152012022002`, `199005202015031003` |
| Siswa | NISN | `0051234561` (Andi Pratama), `0051234562` … `0051234565` |

Seeder yang dijalankan `db:seed`:
`JurusanSeeder`, `KelasSeeder`, `SuperAdminSeeder`, `GuruSeeder`,
`SiswaSeeder`, `MateriSeeder`, `SoalSeeder` (quiz pretest + 20 soal contoh).

---

## Peran & Hak Akses

Autentikasi memakai **multi-guard session** (`config/auth.php`) + middleware
`role:*` (`app/Http/Middleware/CheckRole.php`):

| Guard | Model | Login |
|-------|-------|-------|
| `superadmin` | `SuperAdmin` | via halaman login guru (`/login/guru`) |
| `admin` (guru) | `AdminGuru` | `/login/guru` |
| `siswa` | `PenggunaSiswa` | `/login/siswa` |

| Area | Route prefix | Konten |
|------|--------------|--------|
| Superadmin | `/superadmin` | Dashboard + manajemen guru, siswa (termasuk import CSV), materi, quiz, soal |
| Guru | `/materi`, `/admin/quiz`, `/sertifikat` | Materi, quiz & jadwal posttest, soal, penerbitan sertifikat |
| Siswa | `/portal/*`, `/siswa/*` | Portal materi, kerjakan quiz, profil, sertifikat |
| Publik | `/leaderboard` | Peringkat poin pretest/posttest |

### Alur utama siswa

1. Login → daftar materi.
2. **Wajib menyelesaikan Pre-Test** sebelum file materi bisa diakses.
3. Buka materi (preview PDF/PPTX/DOC langsung di halaman — lihat `README.txt`).
4. Kerjakan **Post-Test** (dikunci sesuai jadwal: tanggal + jam mulai + timer).
5. Poin masuk **leaderboard**; sertifikat diterbitkan guru.

---

## Struktur Penting

```
laravel/
├── app/Http/Controllers/
│   ├── Api/            # REST API untuk mobile (Sanctum)
│   ├── Auth/           # Login guru & siswa
│   ├── Guru/           # Materi, quiz, soal, sertifikat (guard admin)
│   ├── Siswa/          # Portal materi, quiz, profil, sertifikat
│   └── Superadmin/     # Manajemen seluruh data (guard superadmin)
├── app/Http/Middleware/ # CheckRole, EnsureGuru, EnsureSiswa, EnsureSuperadmin
├── app/Models/         # Materi, Quiz, Soal, PilihanJawaban, Leaderboard,
│                       # Sertifikat, PenggunaSiswa, AdminGuru, SuperAdmin, dll.
├── database/migrations/  # skema lengkap + view
├── database/seeders/     # data demo (lihat tabel akun di atas)
├── resources/views/      # Blade: admin, guru, siswa, portal, quiz, leaderboard, ...
├── routes/web.php        # web (session)
├── routes/api.php        # REST API /api
└── tests/                # feature tests
```

---

## REST API (`/api`)

Auth memakai **Sanctum** token (`Authorization: Bearer <token>`).

### Publik

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/login/siswa` | Login siswa → token |
| POST | `/api/login/guru` | Login guru → token |
| POST | `/api/tts` | Text-to-speech (cache MP3 di `storage/app/public/tts`) |

### Siswa (token `auth:sanctum`)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/siswa/logout` | Logout |
| GET | `/api/siswa/profil` | Profil siswa |
| POST | `/api/siswa/profil/foto` | Update foto profil |
| PUT | `/api/siswa/profil/password` | Ganti password |
| GET | `/api/siswa/materi` | Daftar materi + status pretest |
| GET | `/api/siswa/materi/{materi}` | Detail materi + quiz |
| GET | `/api/siswa/materi/{materi}/file` | Unduh/ambil file materi |
| GET | `/api/siswa/materi/{materi}/quiz` | Daftar quiz materi |
| GET | `/api/siswa/quiz/{quiz}` | Ambil soal (terkunci bila jadwal posttest belum buka) |
| POST | `/api/siswa/quiz/{quiz}` | Submit jawaban → poin & leaderboard |
| GET | `/api/siswa/leaderboard` | Peringkat |
| GET | `/api/siswa/sertifikat` | Daftar sertifikat |
| GET | `/api/siswa/sertifikat/{sertifikat}/download` | Unduh sertifikat |

Inspeksi lengkap: `php artisan route:list --path=api`

---

## Pengujian

```bash
php artisan test                # semua test
php artisan test --filter=Quiz   # sebagian
vendor/bin/pint                 # format ulang kode PHP (wajib setelah edit)
```

Test yang tersedia: `AuthTest`, `RoleAuthorizationTest`, `GuruManagementTest`,
`SuperadminManagementTest`, `QuizLeaderboardTest`, `JadwalPostTestTest`,
`MateriPreviewTest`.

---

## Catatan Tambahan

- **Preview dokumen**: konversi PPTX/DOC/DOCX → PDF memakai LibreOffice
  (`soffice`). Bila belum terinstall, halaman tetap jalan dan hanya menampilkan
  tombol download manual. Instruksi patch lengkap ada di [`README.txt`](./README.txt).
- **Naming folder**: `app/Http/Controllers/Portal` wajib huruf `P` besar agar
  namespace cocok saat di-deploy ke Linux.
- **Panduan AI/agent**: lihat [`AGENTS.md`](./AGENTS.md) / `CLAUDE.md`
  (Laravel Boost guidelines, aturan di `.ai/rules` bila ada).
