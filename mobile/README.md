# Pre-Test & Post-Test Dasar Python (Flutter — Frontend Only)

Aplikasi Flutter untuk halaman **Pre-Test**, **Post-Test**, **Materi**,
**Sertifikat**, **Leaderboard**, dan **Profil** siswa **SMKN 2 Jember**
(mata pelajaran **TIK**).

Saat ini masih **murni frontend**: tidak ada pemanggilan API, database,
autentikasi server, atau penyimpanan hasil ke backend. Semua state disimpan
sementara di memory menggunakan `StatefulWidget` + `setState`.

> **Catatan integrasi:** REST API untuk aplikasi ini sudah tersedia di
> [`../laravel`](../laravel/README.md) (login siswa, materi, quiz, leaderboard,
> sertifikat, TTS — lihat `../laravel/routes/api.php`), tetapi **belum
> dihubungkan** ke kode Flutter ini (belum ada dependency `http`/`dio` di
> `pubspec.yaml`).

---

## Struktur Folder (Feature-First)

```
lib/
├── main.dart                              # Entry point (hanya runApp)
├── aplikasi/
│   ├── aplikasi.dart                      # Root MaterialApp + tema
│   └── tema_aplikasi.dart                 # ThemeData & NoTransitionsBuilder
├── inti/
│   ├── konstanta/
│   │   └── warna_aplikasi.dart            # Palet warna terpusat
│   └── widgets/
│       └── navigasi_bawah.dart            # Footer navigasi 3-tab
└── fitur/
     ├── autentikasi/
      │   ├── models/
      │   ├── services/   (api_autentikasi)
      │   ├── controllers/
      │   ├── widgets/    (header, avatar, kolom_teks)
       │   └── screens/    (login_screen)
       ├── beranda/
       │   ├── models/
       │   ├── services/
       │   ├── controllers/
       │   ├── widgets/
       │   └── screens/    (beranda_screen — dashboard utama)
       ├── materi/
       │   ├── models/     (model_materi)
       │   ├── services/   (api_materi)
       │   ├── controllers/
       │   ├── widgets/
       │   └── screens/    (buka_materi_screen)
       ├── mesin_tes/
       │   ├── models/     (soal)
       │   ├── services/   (api_quiz, api_tts)
       │   ├── controllers/
       │   ├── widgets/    (header_tes, kartu_soal, opsi_jawaban, dll.)
       │   └── screens/    (tes_screen — reusable test engine)
       ├── pretest/
       │   ├── models/
       │   ├── services/
       │   ├── controllers/
       │   ├── widgets/
       │   └── screens/    (pretest_screen)
       ├── posttest/
       │   ├── models/
       │   ├── services/
       │   ├── controllers/
       │   ├── widgets/
       │   └── screens/    (posttest_screen)
       ├── leaderboard/
       │   ├── models/     (pengguna_peringkat)
       │   ├── services/   (api_leaderboard)
       │   ├── controllers/
       │   ├── widgets/    (batang_podium)
       │   └── screens/    (leaderboard_screen)
       ├── profil/
       │   ├── models/
       │   ├── services/   (api_profil)
       │   ├── controllers/
       │   ├── widgets/
       │   └── screens/    (profil_screen, pengaturan_akun_screen)
       └── sertifikat/
           ├── models/     (item_sertifikat)
           ├── services/   (api_sertifikat)
           ├── controllers/
           ├── widgets/
           └── screens/    (sertifikat_screen, upload_sertifikat_screen)
```

Folder lain di root project:

```
mobile/
├── assets/                # fonts PlusJakartaSans + gambar (auth_avatar)
├── test/                  # widget_test, beranda_view_test, profile_view_test
├── android/ ios/ ...      # platform shell bawaan Flutter
├── analysis_options.yaml  # konfigurasi flutter_lints
├── CODING_STANDARD.md     # standar penamaan & struktur kode
└── REFACTOR_PROGRESS.md   # status refactor ke arsitektur feature-first
```

> Folder `lib/` lama (`common/`, `views/`, `screens/`, `widgets/`, `theme/`,
> `login/`, `register/`, `pretest/`, `posttest/`, `data/`, `models/`) masih
> tersisa **kosong** sebagai sisa proses refactor — belum dihapus
> (lihat `REFACTOR_PROGRESS.md`, langkah 7).

---

## Fitur

| Fitur | Deskripsi |
|-------|-----------|
| **Autentikasi** | Login & Register (dummy, tanpa backend) |
| **Beranda** | Dashboard profil, statistik, grid materi |
| **Materi** | Daftar materi, viewer materi halaman-per-halaman |
| **Mesin Tes** | Engine Pre-Test/Post-Test reusable (20 soal, navigasi, timer UI) |
| **Pre-Test** | 20 soal dummy Pre-Test (Dasar Python) |
| **Post-Test** | 20 soal dummy Post-Test (beberapa berisi kode Python) |
| **Papan Peringkat** | Leaderboard dengan podium Top 3 dan ranking list |
| **Profil** | Halaman profil, pengaturan akun, logout |
| **Sertifikat** | Daftar sertifikat + CRUD upload sertifikat baru |

---

## Penamaan Kode

Semua identifier buatan sendiri menggunakan **Bahasa Indonesia** (`camelCase` /
`PascalCase`). Istilah Flutter/Dart bawaan (`Widget`, `State`, `BuildContext`)
tetap bahasa Inggris.

Lihat [CODING_STANDARD.md](./CODING_STANDARD.md) untuk detail lengkap.

---

## Cara Menjalankan

```bash
# 1. Install dependencies
flutter pub get

# 2. Jalankan di device/emulator
flutter run

# 3. Build APK release (opsional)
flutter build apk --release
```

---

## Format, Analisis & Test

```bash
dart format lib/
flutter analyze
flutter test
# Harus 0 error dan 0 warning sebelum commit
```
