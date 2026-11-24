/// Data siswa yang dikembalikan endpoint login dan profil.
class PenggunaSiswa {
  final String nisn;
  final String namaLengkap;
  final int? idKelas;
  final String? kelas;
  final String? jurusan;
  final String? tahun;
  final int poin;
  final String? fotoProfil;

  const PenggunaSiswa({
    required this.nisn,
    required this.namaLengkap,
    this.idKelas,
    this.kelas,
    this.jurusan,
    this.tahun,
    this.poin = 0,
    this.fotoProfil,
  });

  /// Membuat objek dari response JSON API.
  factory PenggunaSiswa.fromJson(Map<String, dynamic> json) {
    final idKelas = json['id_kelas'];
    final poin = json['poin'];

    return PenggunaSiswa(
      nisn: json['nisn']?.toString() ?? '',
      namaLengkap: json['nama_lengkap']?.toString() ?? '',
      idKelas: idKelas is num ? idKelas.toInt() : null,
      kelas: json['kelas']?.toString(),
      jurusan: json['jurusan']?.toString(),
      tahun: json['tahun']?.toString(),
      poin: poin is num ? poin.toInt() : 0,
      fotoProfil: json['foto_profile']?.toString(),
    );
  }

  /// Label kelas untuk ditampilkan di Beranda dan Profil.
  String get labelKelas {
    var rawKelas = kelas?.trim();
    if (rawKelas == null || rawKelas.isEmpty) {
      return (jurusan != null && jurusan!.isNotEmpty) ? jurusan! : 'Kelas -';
    }

    if (jurusan != null &&
        jurusan!.isNotEmpty &&
        rawKelas.toLowerCase().contains(jurusan!.toLowerCase())) {
      rawKelas = rawKelas
          .replaceAll(
              RegExp(' - ${RegExp.escape(jurusan!)}', caseSensitive: false), '')
          .replaceAll(RegExp(RegExp.escape(jurusan!), caseSensitive: false), '')
          .trim();
      if (rawKelas.endsWith('-')) {
        rawKelas = rawKelas.substring(0, rawKelas.length - 1).trim();
      }
    }

    rawKelas = rawKelas
        .replaceFirst(RegExp(r'^Kelas\s+', caseSensitive: false), '')
        .replaceFirst(RegExp(r'^10[\s\-]'), 'X-')
        .replaceFirst(RegExp(r'^10$'), 'X');

    final parts = <String>[
      if (rawKelas.isNotEmpty) 'Kelas $rawKelas',
      if (jurusan != null && jurusan!.isNotEmpty) jurusan!,
    ];

    return parts.isEmpty ? 'Kelas -' : parts.join(' - ');
  }
}
