import 'package:pretest_posttest_app/fitur/mesin_tes/models/soal.dart';

/// 20 soal dummy untuk layar Post-Test.
/// Beberapa soal menyertakan cuplikan kode Python, yang ditampilkan
/// dalam blok kode monospace di atas teks pertanyaan.
final List<Soal> daftarSoalPosttest = [
  const Soal(
    pertanyaan:
        'Apakah output yang akan dihasilkan saat kode di atas dijalankan?',
    kode: 'x = "10"\ny = 5\nprint(x * 2)',
    pilihan: ['20', '1010', '105', 'TypeError (Error)'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Apakah output dari kode di atas?',
    kode: 'a = 5\nb = 2\nprint(a // b)',
    pilihan: ['2.5', '2', '3', 'Error'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Nilai apa yang akan dicetak oleh kode berikut?',
    kode: 'for i in range(3):\n    print(i)',
    pilihan: ['1 2 3', '0 1 2', '0 1 2 3', '1 2'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Apa hasil dari kode berikut?',
    kode: 'nama = "Budi"\nprint(nama[0])',
    pilihan: ['B', 'u', 'Budi', 'Error'],
    indeksJawabanBenar: 0,
  ),
  const Soal(
    pertanyaan: 'Apa output dari potongan kode berikut?',
    kode: 'x = 10\nif x > 5:\n    print("Besar")\nelse:\n    print("Kecil")',
    pilihan: ['Besar', 'Kecil', 'Error', 'Tidak ada output'],
    indeksJawabanBenar: 0,
  ),
  const Soal(
    pertanyaan: 'Apa yang akan dicetak oleh kode berikut?',
    kode: 'angka = [1, 2, 3]\nprint(len(angka))',
    pilihan: ['2', '3', '1', 'Error'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Berapa hasil dari kode berikut?',
    kode: 'print(2 ** 3)',
    pilihan: ['6', '8', '9', '5'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Apa output dari kode berikut?',
    kode: 's = "Python"\nprint(s[-1])',
    pilihan: ['P', 'n', 'y', 'Error'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Apa yang terjadi ketika kode berikut dijalankan?',
    kode: 'x = 5\nx = x + "1"\nprint(x)',
    pilihan: ['51', '6', 'TypeError (Error)', '5.1'],
    indeksJawabanBenar: 2,
  ),
  const Soal(
    pertanyaan: 'Apa hasil akhir dari variabel total pada kode berikut?',
    kode: 'total = 0\nfor i in range(1, 4):\n    total += i\nprint(total)',
    pilihan: ['3', '6', '10', '4'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Apa output dari kode berikut?',
    kode: 'data = {"nama": "Ani"}\nprint(data["nama"])',
    pilihan: ['nama', 'Ani', 'Error', '{"nama": "Ani"}'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Berapa nilai yang dicetak oleh kode berikut?',
    kode: 'x = True\ny = False\nprint(x and y)',
    pilihan: ['True', 'False', '1', 'Error'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Apa output dari kode berikut?',
    kode: 'list1 = [1, 2, 3]\nlist1.append(4)\nprint(list1)',
    pilihan: ['[1, 2, 3]', '[1, 2, 3, 4]', '[4, 1, 2, 3]', 'Error'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Apa hasil dari kode di bawah ini?',
    kode: 'def tambah(a, b):\n    return a + b\n\nprint(tambah(3, 4))',
    pilihan: ['34', '7', 'Error', 'None'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Apa output dari kode berikut?',
    kode: 'x = [1, 2, 3]\nprint(x[1:])',
    pilihan: ['[1, 2]', '[2, 3]', '[1, 2, 3]', '[3]'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Berapa hasil dari kode berikut?',
    kode: 'print(10 % 3)',
    pilihan: ['3', '1', '0', '3.33'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Apa output dari kode berikut?',
    kode: 'nilai = "7"\nprint(int(nilai) + 3)',
    pilihan: ['73', '10', 'Error', '7'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Apa yang dicetak oleh kode berikut?',
    kode: 'x = 3\nwhile x > 0:\n    print(x)\n    x -= 1',
    pilihan: ['3 2 1', '1 2 3', '3 2 1 0', 'Error'],
    indeksJawabanBenar: 0,
  ),
  const Soal(
    pertanyaan: 'Apa output dari kode berikut?',
    kode: 'kata = "python"\nprint(kata.upper())',
    pilihan: ['python', 'PYTHON', 'Python', 'Error'],
    indeksJawabanBenar: 1,
  ),
  const Soal(
    pertanyaan: 'Apa hasil akhir dari kode berikut?',
    kode:
        'hasil = []\nfor i in range(3):\n    hasil.append(i * 2)\nprint(hasil)',
    pilihan: ['[0, 1, 2]', '[0, 2, 4]', '[2, 4, 6]', 'Error'],
    indeksJawabanBenar: 1,
  ),
];
