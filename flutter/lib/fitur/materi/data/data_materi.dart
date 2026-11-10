import 'package:pretest_posttest_app/fitur/materi/models/model_materi.dart';

/// Dummy data materi pelajaran.
/// Ganti dengan panggilan API nyata saat backend siap.
/// Contoh endpoint: GET /api/materi
const List<ModelMateri> daftarMateriDummy = [
  ModelMateri(
    id: 1,
    kelas: '10TKJ-01',
    judul: 'Dasar Python',
    guruMapel: 'Aji Seto Son Horeg S.D, S.Mp, S.Ma',
    deskripsi: 'Materi ini membahas dasar-dasar pemrograman Python, '
        'mulai dari sintaks dasar, tipe data, kondisional, perulangan, '
        'hingga fungsi sederhana.',
    halamanKonten: [
      'Halaman 1: Pengenalan Python\n\nPython adalah bahasa pemrograman tingkat tinggi yang mudah dipelajari...',
      'Halaman 2: Instalasi Python\n\nUnduh Python dari python.org dan ikuti petunjuk instalasi...',
      'Halaman 3: Sintaks Dasar\n\nPython menggunakan indentasi untuk mendefinisikan blok kode...',
      'Halaman 4: Tipe Data\n\nPython mendukung berbagai tipe data: int, float, str, list, dict...',
      'Halaman 5: Variabel\n\nVariabel di Python tidak perlu deklarasi tipe data...',
      'Halaman 6: Operator\n\nPython mendukung operator aritmatika, perbandingan, dan logika...',
      'Halaman 7: Input & Output\n\nGunakan input() untuk menerima masukan dan print() untuk menampilkan output...',
      'Halaman 8: String\n\nString adalah urutan karakter yang diapit tanda kutip satu atau dua...',
      'Halaman 9: List\n\nList adalah koleksi data yang bisa berubah (mutable) dan berurutan...',
      'Halaman 10: Tuple\n\nTuple mirip list namun bersifat immutable (tidak bisa diubah)...',
      'Halaman 11: Dictionary\n\nDictionary adalah pasangan key-value yang sangat berguna...',
      'Halaman 12: Kondisional if-else\n\nPernyataan if-else digunakan untuk pengambilan keputusan...',
      'Halaman 13: Perulangan for\n\nPerulangan for digunakan untuk mengiterasi koleksi data...',
      'Halaman 14: Perulangan while\n\nPerulangan while terus berjalan selama kondisi bernilai True...',
      'Halaman 15: Fungsi\n\nFungsi didefinisikan dengan kata kunci def...',
      'Halaman 16: Parameter & Argumen\n\nFungsi dapat menerima parameter dan mengembalikan nilai...',
      'Halaman 17: Scope Variabel\n\nVariabel memiliki scope lokal dan global...',
      'Halaman 18: Modul & Import\n\nGunakan import untuk menggunakan modul built-in maupun eksternal...',
      'Halaman 19: Exception Handling\n\nGunakan try-except untuk menangani kesalahan program...',
      'Halaman 20: Latihan & Kesimpulan\n\nSelamat! Anda telah menyelesaikan materi Dasar Python...',
    ],
  ),
  ModelMateri(
    id: 2,
    kelas: '10TKJ-01',
    judul: 'Struktur Data Python',
    guruMapel: 'Aji Seto Son Horeg S.D, S.Mp, S.Ma',
    deskripsi: 'Materi lanjutan tentang struktur data di Python seperti stack, '
        'queue, dan linked list menggunakan Python.',
    halamanKonten: [
      'Halaman 1: Pengenalan Struktur Data\n\nStruktur data adalah cara mengorganisasi data...',
      'Halaman 2: Stack\n\nStack bekerja dengan prinsip LIFO (Last In First Out)...',
    ],
  ),
];
