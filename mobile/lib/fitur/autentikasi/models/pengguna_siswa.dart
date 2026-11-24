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
    String? cleanKelas = kelas;
    if (cleanKelas != null) {
      cleanKelas = cleanKelas
          .replaceFirst(RegExp(r'^10[\s\-]'), 'X-')
          .replaceFirst(RegExp(r'^10$'), 'X');
    }
    final bagian = <String>[
      if (cleanKelas != null && cleanKelas.isNotEmpty) 'Kelas $cleanKelas',
      if (jurusan != null && jurusan!.isNotEmpty) jurusan!,
    ];

    return bagian.isEmpty ? 'Kelas -' : bagian.join(' - ');
  }
}
