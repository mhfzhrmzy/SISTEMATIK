# OpenCode Prompt — Tahap 4: Hapus Vite, Sertakan Tailwind CSS via File Statis

## Context
Project: C:/laragon/www/semester3testdatabase (Laravel 13, versi framework 13.32.0)
Backend Laravel 11-style + Flutter (guard: siswa/admin/superadmin, Sanctum API).

Keadaan sekarang:
- `package.json` memakai Vite 8 + `laravel-vite-plugin` + `@tailwindcss/vite` + `tailwindcss` 4.
- `vite.config.js` ada di root, build-nya output ke `public/build/`.
- `resources/css/app.css` adalah file Tailwind v4 (`@import 'tailwindcss';` + `@theme { ... }`), sudah berisi tema maroon sekolah.
- `resources/js/app.js` kosong (hanya komentar `//`).
- 3 view memanggil `@vite(['resources/css/app.css', 'resources/js/app.js'])`:
  - `resources/views/layouts/app.blade.php`
  - `resources/views/errors/403.blade.php`
  - `resources/views/welcome.blade.php`
- Sisanya (±69 view) sudah memakai Tailwind utility class + inline `<script>`/`<style>`.
- `node_modules/` terinstall (47 top-level pkg) dan `public/build/` sudah ada hasil Vite.

Tujuan: project "tidak berat saat run". Vite dev server (live-reload, watcher, ESBUILD) adalah beban berat saat `php artisan serve` / Laragon jalan. Maka kita **stop pakai Vite** dan **sertakan Tailwind lewat CSS file statis** yang di-compile satu kali. Tidak ada build step saat run.

## Alur Kerja (urutan pasti)
1. Compile Tailwind v4 dari `resources/css/app.css` jadi satu file `public/css/app.css` (lihat "Command compile" di bawah). Ini SATU kali, bukan run-time.
2. Ganti 3 `@vite(...)` jadi tag `<link>`/`<script>` ke file statis di `public/`.
3. Hapus `vite.config.js`, hapus dependencies Vite dari `package.json`.
4. Bersihkan `public/build/` dan (opsional) `node_modules/`.
5. Test semua halaman.

## Detail per langkah

### 1) Compile Tailwind ke file statis
Tailwind v4 sudah terinstall di `node_modules`, jadi pakai npx TAILWIND CSS CLI untuk compile sekali:

```bash
cd C:/laragon/www/semester3testdatabase
npx tailwindcss -i resources/css/app.css -o public/css/app.css --minify
```

Catatan penting:
- Tailwind v4 CLI **tidak perlu `tailwind.config.js`** karena source sudah dideklarasi di CSS lewat `@source`. Kalau CLI memaksa konfigurasi, tambahkan `content` manual (lihat "Pitfall" di bawah).
- Hasil `public/css/app.css` ini yang kita pakai selamanya saat run. Setiap ganti markup/class baru, jalankan ulang 1 command ini.
- `--minify` mengkecil file agar ringan di browser.

### 2) Ganti @vite di 3 view

`resources/views/layouts/app.blade.php` — ganti:
```html
@vite(['resources/css/app.css', 'resources/js/app.js'])
```
menjadi:
```html
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
```
> `resources/js/app.js` itu kosong, jadi TIDAK perlu di-include sama sekali.

`resources/views/errors/403.blade.php` — ganti `@vite([...])` dengan `<link rel="stylesheet" href="{{ asset('css/app.css') }}">`.

`resources/views/welcome.blade.php` — ganti `@vite([...])` dengan `<link rel="stylesheet" href="{{ asset('css/app.css') }}">`.

### 3) Hapus konfigurasi Vite
Hapus file di root:
```
vite.config.js
```
Edit `package.json`: hapus semua script + devDependencies yang terkait Vite. Hasil final `package.json`:
```json
{
    "$schema": "https://www.schemastore.org/package.json",
    "private": true,
    "type": "module",
    "scripts": {
        "build:css": "tailwindcss -i resources/css/app.css -o public/css/app.css --minify"
    },
    "devDependencies": {
        "tailwindcss": "^4.0.0"
    }
}
```
(Jaga `tailwindcss` untuk compile ulang bila perlu. `@tailwindcss/vite`, `laravel-vite-plugin`, `vite`, `concurrently` dihapus. `optionalDependencies`/`@laravel/multiplex` juga boleh dihapus kalau tidak dipakai — cek dulu.)

Lalu:
```bash
rm -rf node_modules package-lock.json
npm install
```

### 4) Bersihkan artefak Vite
```bash
rm -rf public/build
```

### 5) Test (verifikasi)
```bash
cd C:/laragon/www/semester3testdatabase
php artisan serve
```
Lalu buka di browser:
- `http://localhost:8000/` (welcome)
- halaman siswa, guru, superadmin (login per peran)
- halaman error 403 (test akses tanpa permission)
Pastikan: styling maroon + layout utuh, tidak ada halaman kosong/roti, console tanpa error.

## Pitfall
1. **Tailwind v4 CLI butuh content.** Kalau `npx tailwindcss -i ... -o ...` menghasilkan CSS kosong atau class hilang, buat `tailwind.config.js` berisi:
   ```js
   module.exports = {
       content: [
           './resources/views/**/*.blade.php',
           './app/**/*.php',
       ],
       theme: { extend: {} },
       plugins: [],
   };
   ```
   lalu compile dengan `npx tailwindcss -c tailwind.config.js -i resources/css/app.css -o public/css/app.css --minify`. (v4 tetap support config file sebagai fallback.)
2. **Jangan hapus `@source` di `resources/css/app.css`** sebelum compile — source untuk pagination blade butuh itu. Setelah jadi file statis, `resources/css/app.css` tetap dipakai sebagai input compile, jadi biarkan apa adanya.
3. **Cache Blade.** Setelah ganti `@vite` → `<link>`, view yang sudah ter-cache bisa nyangkut. Jalankan `php artisan view:clear` sebelum test.
4. **Inline `<style>`/`<script>` di view tidak terdampak** — itu bukan Vite, tetap jalan.
5. **Aset lain.** Kalau ada view yang sudah load CSS/JS lain lewat `{{ asset() }}` atau `<script src>`, biarkan — tidak kita ubah. Yang kita sentuh hanya 3 `@vite`.

## Definisi "Berhasil"
- [ ] Tidak ada file `vite.config.js` di root
- [ ] `package.json` tidak lagi memuat `vite` / `laravel-vite-plugin` / `@tailwindcss/vite`
- [ ] `public/build/` terhapus
- [ ] `public/css/app.css` ada (hasil compile, ukuran < 100KB wajar)
- [ ] 3 view di atas memakai `asset('css/app.css')`, tidak ada lagi string `@vite`
- [ ] `grep -rn "@vite" resources/views` → 0 hasil
- [ ] `php artisan serve` + browse welcome/siswa/guru/superadmin/403 → styling normal, tanpa Vite dev server yang jalan di background
- [ ] `npm run build:css` (atau command compile) bisa dijalankan ulang kapanpun tanpa error
