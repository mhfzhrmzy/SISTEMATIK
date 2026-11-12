# Standar Kode — Aplikasi Pre-Test & Post-Test

## 1. Arsitektur Folder (Feature-First)

```
lib/
├── main.dart                         ← Entry point (hanya runApp)
├── aplikasi/                         ← Root widget & konfigurasi tema
│   ├── aplikasi.dart
│   └── tema_aplikasi.dart
├── inti/                             ← Shared utilities lintas fitur
│   ├── konstanta/
│   │   └── warna_aplikasi.dart
│   └── widgets/
│       └── navigasi_bawah.dart
└── fitur/                            ← Satu sub-folder per fitur
    ├── autentikasi/
    │   ├── views/
    │   └── widgets/
    ├── beranda/
    │   └── views/
    ├── materi/
    │   ├── data/
    │   ├── models/
    │   └── views/
    ├── mesin_tes/
    │   ├── models/
    │   ├── views/
    │   └── widgets/
    ├── pretest/
    ├── posttest/
    ├── papan_peringkat/
    │   ├── models/
    │   ├── views/
    │   └── widgets/
    ├── profil/
    │   └── views/
    └── sertifikat/
        ├── models/
        └── views/
```

### Aturan folder
- **`fitur/<nama_fitur>/`** — Setiap fitur mandiri; berisi `views/`, `widgets/`, `models/`, `data/` sesuai kebutuhan.
- **`inti/`** — Hanya kode yang dipakai ≥ 2 fitur. Jangan taruh logic spesifik fitur di sini.
- **`aplikasi/`** — Konfigurasi root: `MaterialApp`, tema, page-transitions builder.

---

## 2. Penamaan (Bahasa Indonesia)

| Elemen | Konvensi | Contoh |
|--------|----------|--------|
| File | `snake_case` Indonesia | `warna_aplikasi.dart`, `tes_view.dart` |
| Class | `PascalCase` Indonesia | `WarnaAplikasi`, `TesView`, `ModelMateri` |
| Variabel & field | `camelCase` Indonesia | `daftarSoal`, `halamanSaatIni` |
| Konstanta | `camelCase` Indonesia | `utama`, `latarBelakang` |
| Private | Awali `_` lalu `camelCase` Indonesia | `_indeksFooter`, `_kontrolerJudul` |
| Parameter named | `camelCase` Indonesia | `saatDiketuk:`, `indeksSaatIni:` |

> **Pengecualian**: Istilah Flutter/Dart bawaan (`Widget`, `State`, `BuildContext`, `StatelessWidget`, dsb.) tetap bahasa Inggris. Jangan terjemahkan nama API framework.

---

## 3. Import

- Selalu gunakan **package import absolut**:
  ```dart
  import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
  ```
- Jangan gunakan relative import (`../..`).
- Urutan: `dart:*` → `package:flutter/*` → `package:pretest_posttest_app/*` → file lokal dalam fitur yang sama.

---

## 4. Clean Code

1. **Satu class per file** — Kecuali private helper widgets (`_TombolKembali`, `_KartuMateri`, dsb.) yang boleh di file view yang sama.
2. **Max 400 baris per file** — Pecah ke sub-widget jika melebihi.
3. **Tidak ada magic number/string** — Gunakan konstanta di `warna_aplikasi.dart` atau variabel lokal bernama jelas.
4. **Docstring `///`** — Wajib pada setiap class publik dan method penting.
5. **Naming > komentar** — Nama variabel/method harus cukup deskriptif; komentar hanya untuk *mengapa*, bukan *apa*.

---

## 5. Widget Guidelines

- Gunakan `const` constructor jika memungkinkan.
- Jangan hardcode warna — gunakan `WarnaAplikasi.*`.
- Footer navigasi selalu pakai `NavigasiBawah` dari `inti/widgets/`.
- Header tes selalu pakai `HeaderTes` dari `fitur/mesin_tes/widgets/`.

---

## 6. Format & Analisis

```bash
dart format lib/
flutter analyze
```

- Harus **0 error** dan **0 warning** sebelum commit.
- Gunakan `dart format` agar konsisten.

---

## 7. Git Workflow

- Branch fitur: `fitur/<nama-fitur>`
- Branch refactor: `refactor/<deskripsi>`
- Commit message: bahasa Indonesia, imperatif (`Tambah halaman login`, `Perbaiki navigasi footer`)
