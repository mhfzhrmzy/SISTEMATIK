/// Model data untuk satu butir soal.
///
/// [kode] bersifat opsional — jika diisi, akan ditampilkan dalam
/// blok kode monospace di atas teks pertanyaan (digunakan oleh Post-Test).
class Soal {
  final int? idSoal;
  final String pertanyaan;
  final List<String> pilihan;
  final List<String> kunciPilihan;
  final int? indeksJawabanBenar;
  final String? kode;

  const Soal({
    this.idSoal,
    required this.pertanyaan,
    required this.pilihan,
    this.kunciPilihan = const ['a', 'b', 'c', 'd'],
    this.indeksJawabanBenar,
    this.kode,
  });

  factory Soal.fromApiJson(Map<String, dynamic> json) {
    final choices = json['choices'];
    if (choices is! Map) {
      throw const FormatException('Pilihan jawaban soal tidak valid.');
    }

    final pilihan = <String>[];
    final kunci = <String>[];
    for (final entry in choices.entries) {
      final key = entry.key.toString();
      if (!const ['a', 'b', 'c', 'd'].contains(key) || entry.value == null) {
        continue;
      }
      pilihan.add(entry.value.toString());
      kunci.add(key);
    }

    final id = json['id_soal'];
    return Soal(
      idSoal: id is num ? id.toInt() : int.tryParse('$id'),
      pertanyaan: (json['pertanyaan'] ?? '').toString(),
      pilihan: pilihan,
      kunciPilihan: kunci,
      kode: json['kode']?.toString(),
    );
  }
}
