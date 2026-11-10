CARA PAKAI FOLDER INI
=====================

LANGKAH 0 -- WAJIB: INSTALL LIBREOFFICE DULU
==============================================
Fitur ini butuh LibreOffice terpasang di komputer kamu (buat convert
PPTX/DOC/DOCX -> PDF di belakang layar). Kalau belum ada:

1. Download di https://www.libreoffice.org/download/download/
2. Install seperti biasa (Next-Next-Finish)
3. Setelah install, LibreOffice BIASANYA otomatis masuk ke PATH Windows.
   Cek dengan buka PowerShell baru, ketik: soffice --version
   Kalau muncul versi LibreOffice, berarti sudah kebaca otomatis, lanjut
   ke langkah di bawah tanpa perlu edit apa-apa lagi.

   Kalau muncul error "not recognized", tambahkan baris ini di file
   .env project kamu (sesuaikan path kalau lokasi install-nya beda):

       LIBREOFFICE_PATH="C:\Program Files\LibreOffice\program\soffice.exe"


FILE YANG DIUBAH / DITIMPA (4 file)
======================================
1. app/Http/Controllers/Portal/MateriController.php
   -> folder aslinya "portal" (huruf kecil) SAYA GANTI jadi "Portal"
      (huruf besar) supaya cocok sama namespace di kodenya -- kalau
      dibiarkan, project ini bakal ERROR TOTAL begitu di-deploy ke
      hosting Linux (kebetulan di Windows kamu selama ini tetap jalan
      karena Windows tidak strict soal besar-kecil huruf nama folder).
      Ditambah method baru: preview() -- convert PPTX/DOC/DOCX ke PDF
      otomatis, dengan cache (convert sekali saja per file, bukan
      setiap kali dibuka).
2. app/Http/Controllers/Portal/SoalController.php
   -> isinya SAMA PERSIS seperti punya kamu, cuma ikut pindah folder
      (portal -> Portal). Kalau kamu sudah rename foldernya sendiri,
      file ini boleh dilewati.
3. resources/views/portal/materi/show.blade.php
   -> sekarang SEMUA jenis file (PDF, PPTX, DOC, dll) ditampilkan
      lewat satu viewer yang sama, langsung di halaman, bisa di-scroll.
4. routes/web.php
   -> tambah 1 route baru: portal/materi/{materi}/preview


PENTING -- INI JUGA MENGGANTI NAMA FOLDER
=============================================
Folder app/Http/Controllers/portal/ (huruf kecil) perlu di-RENAME jadi
app/Http/Controllers/Portal/ (huruf P besar). Kalau kamu copy-timpa file
di atas ke folder LAMA (portal huruf kecil), tetap akan jalan di Windows
(karena Windows tidak peduli besar-kecil huruf folder), TAPI supaya
project ini siap kalau nanti di-deploy ke hosting, sebaiknya:

1. Rename folder app/Http/Controllers/portal menjadi Portal
   (klik kanan folder > Rename)
2. Baru timpa 2 file Controller di atas ke folder yang sudah di-rename itu


LANGKAH SETELAH FILE DITIMPA
=============================
1. Pastikan LibreOffice sudah terinstall (Langkah 0 di atas).
2. Rename folder portal -> Portal (lihat di atas).
3. Timpa 4 file di atas ke lokasi masing-masing.
4. Tidak perlu migrate/php artisan apa pun -- ini cuma perubahan kode,
   bukan database.
5. php artisan serve (kalau belum jalan), lalu buka halaman detail
   materi yang ada file PPTX/DOC/PDF-nya -- sekarang harusnya langsung
   tampil & bisa di-scroll di halaman, apa pun jenis filenya.

Kalau LibreOffice belum sempat diinstall / gagal convert, halaman TIDAK
akan error/crash -- cuma akan muncul pesan kecil di area preview minta
kamu download filenya secara manual, sambil tombol Download tetap ada
seperti biasa.
