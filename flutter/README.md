# Pre-Test & Post-Test Dasar Python (Flutter — Frontend Only)

Aplikasi Flutter frontend untuk halaman **Pre-Test**, **Post-Test**, **Materi**,
**Sertifikat**, **Leaderboard**, dan **Profil** untuk siswa SMK Kelas 10 TKJ.

Murni frontend: **tidak ada backend, API, database, autentikasi, atau
penyimpanan hasil ke server**. Semua state disimpan sementara di memory
menggunakan `StatefulWidget` + `setState`.

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
    │   ├── views/    (login_view, register_view)
    │   └── widgets/  (header, avatar, kolom_teks)
    ├── beranda/
    │   └── views/    (beranda_view — dashboard utama)
    ├── materi/
    │   ├── data/     (data_materi — dummy data)
    │   ├── models/   (model_materi)
    │   └── views/    (buka_materi_view)
    ├── mesin_tes/
    │   ├── models/   (soal)
    │   ├── views/    (tes_view — reusable test engine)
    │   └── widgets/  (header_tes, kartu_soal, opsi_jawaban, dll.)
    ├── pretest/      (pretest_data, pretest_view)
    ├── posttest/     (posttest_data, posttest_view)
    ├── papan_peringkat/
    │   ├── models/   (pengguna_peringkat)
    │   ├── views/    (papan_peringkat_view)
    │   └── widgets/  (batang_podium)
    ├── profil/
    │   └── views/    (profil_view)
    └── sertifikat/
        ├── models/   (item_sertifikat)
        └── views/    (sertifikat_view, upload_sertifikat_view)
```

---

## Fitur

| Fitur | Deskripsi |
|-------|-----------|
| **Autentikasi** | Login & Register (dummy, tanpa backend) |
| **Beranda** | Dashboard profil, statistik, grid materi |
| **Materi** | Daftar materi, viewer materi halaman-per-halaman |
| **Mesin Tes** | Engine Pre-Test/Post-Test reusable (20 soal, navigasi, timer UI) |
| **Pre-Test** | 20 soal dummy Pre-Test menggunakan mesin tes |
| **Post-Test** | 20 soal dummy Post-Test (beberapa berisi kode Python) |
| **Papan Peringkat** | Leaderboard dengan podium Top 3 dan ranking list |
| **Profil** | Halaman profil, pengaturan akun, logout |
| **Sertifikat** | Daftar sertifikat + CRUD upload sertifikat baru |

---

## Penamaan Kode

Semua identifier buatan sendiri menggunakan **Bahasa Indonesia** (`camelCase` / `PascalCase`).
Istilah Flutter/Dart bawaan (`Widget`, `State`, `BuildContext`) tetap bahasa Inggris.

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

## Format & Analisis

```bash
dart format lib/
flutter analyze
# Harus 0 error dan 0 warning sebelum commit
```
