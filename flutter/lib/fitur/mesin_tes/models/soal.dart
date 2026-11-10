/// Model data untuk satu butir soal.
///
/// [kode] bersifat opsional — jika diisi, akan ditampilkan dalam
/// blok kode monospace di atas teks pertanyaan (digunakan oleh Post-Test).
class Soal {
  final String pertanyaan;
  final List<String> pilihan;
  final int indeksJawabanBenar;
  final String? kode;

  const Soal({
    required this.pertanyaan,
    required this.pilihan,
    required this.indeksJawabanBenar,
    this.kode,
  });
}
