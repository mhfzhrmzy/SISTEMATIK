import 'package:pretest_posttest_app/fitur/mesin_tes/models/soal.dart';

/// 20 soal dummy untuk layar Pre-Test.
/// Frontend-only: tidak ada backend, tidak ada penyimpanan.
final List<Soal> daftarSoalPretest = [
  const Soal(
    pertanyaan: 'Manakah perintah Python yang benar untuk menampilkan teks '
        '"Hello World" ke layar?',
    pilihan: [
      'echo "Hello World"',
      'print("Hello World")',
      'System.out.println("Hello World");',
      'console.log("Halo Dunia")',
    ],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Ekstensi file untuk skrip Python adalah?',
    pilihan: ['.py', '.java', '.js', '.cpp'],
    indeksJawabanBenar: 0,
  ),
  const Soal(
    pertanyaan:
        'Fungsi bawaan Python untuk mengetahui panjang sebuah list disebut?',
    pilihan: ['size()', 'length()', 'len()', 'count()'],
    indeksJawabanBenar: 2,
  ),
  const Soal(
    pertanyaan:
        'Tipe data manakah yang digunakan untuk menyimpan nilai benar/salah di Python?',
    pilihan: ['int', 'bool', 'str', 'float'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan:
        'Simbol yang digunakan untuk memberi komentar satu baris di Python adalah?',
    pilihan: ['//', '#', '/* */', '--'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Manakah cara yang benar untuk membuat list kosong di Python?',
    pilihan: ['list = ()', 'list = {}', 'list = []', 'list = ""'],
    indeksJawabanBenar: 2,
  ),
  const Soal(
    pertanyaan:
        'Kata kunci yang digunakan untuk mendefinisikan sebuah fungsi di Python adalah?',
    pilihan: ['function', 'def', 'func', 'define'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan:
        'Operator yang digunakan untuk pembagian bilangan bulat (hasil tanpa desimal) di Python adalah?',
    pilihan: ['/', '//', '%', '**'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Manakah struktur perulangan yang tersedia di Python?',
    pilihan: [
      'for dan while',
      'repeat dan until',
      'loop dan foreach',
      'do dan while'
    ],
    indeksJawabanBenar: 0,
  ),
  const Soal(
    pertanyaan:
        'Fungsi untuk mengubah tipe data string menjadi integer di Python adalah?',
    pilihan: ['str()', 'int()', 'float()', 'bool()'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Bagaimana cara mengimpor modul math di Python?',
    pilihan: ['include math', 'using math', 'import math', 'require math'],
    indeksJawabanBenar: 2,
  ),
  const Soal(
    pertanyaan: 'Manakah penulisan dictionary yang benar di Python?',
    pilihan: [
      '{"nama": "Budi", "umur": 20}',
      '["nama": "Budi", "umur": 20]',
      '("nama": "Budi", "umur": 20)',
      '<nama: "Budi", umur: 20>',
    ],
    indeksJawabanBenar: 0,
  ),
  const Soal(
    pertanyaan: 'Apa hasil dari operasi 7 % 2 di Python?',
    pilihan: ['3', '0', '1', '3.5'],
    indeksJawabanBenar: 2,
  ),
  const Soal(
    pertanyaan: 'Manakah nama variabel yang valid di Python?',
    pilihan: ['2var', 'my-var', '_my_var', 'my var'],
    indeksJawabanBenar: 2,
  ),
  const Soal(
    pertanyaan:
        'Fungsi bawaan untuk membaca input dari pengguna di Python adalah?',
    pilihan: ['input()', 'scan()', 'read()', 'get()'],
    indeksJawabanBenar: 0,
  ),
  const Soal(
    pertanyaan:
        'Manakah pernyataan kondisional yang benar setelah "if" pertama di Python?',
    pilihan: ['elseif', 'elif', 'else if', 'elsif'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan:
        'Apa fungsi dari kata kunci "return" dalam sebuah fungsi Python?',
    pilihan: [
      'Menghentikan program sepenuhnya',
      'Mengembalikan nilai dari fungsi',
      'Mencetak nilai ke layar',
      'Membuat variabel baru',
    ],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan:
        'Manakah cara yang benar untuk membuat komentar banyak baris (multi-line) di Python?',
    pilihan: [
      'Menggunakan // di setiap baris',
      'Menggunakan tanda kutip tiga ("""...""")',
      'Menggunakan <!-- -->',
      'Menggunakan /* ... */',
    ],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan:
        'Apa yang akan terjadi jika kita menjalankan kode "print(5 + "5")" di Python?',
    pilihan: [
      'Hasilnya 10',
      'Hasilnya "55"',
      'Akan terjadi TypeError',
      'Hasilnya 5',
    ],
    indeksJawabanBenar: 2,
  ),
  const Soal(
    pertanyaan:
        'Manakah tipe data yang bersifat immutable (tidak dapat diubah) di Python?',
    pilihan: ['list', 'dictionary', 'set', 'tuple'],
    indeksJawabanBenar: 3,
  ),
];
