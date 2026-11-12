/// Model data satu materi pelajaran.
/// Dirancang agar mudah di-map dari respons JSON API nantinya.
class ModelMateri {
  final int id;
  final String kelas;
  final String judul;
  final String guruMapel;
  final String deskripsi;

  /// Daftar halaman konten materi (teks/HTML per halaman).
  final List<String> halamanKonten;

  const ModelMateri({
    required this.id,
    required this.kelas,
    required this.judul,
    required this.guruMapel,
    required this.deskripsi,
    required this.halamanKonten,
  });

  /// Jumlah halaman materi.
  int get totalHalaman => halamanKonten.length;

  // ── Factory dari JSON (siap pakai saat integrasi API) ───────────────────
  factory ModelMateri.fromJson(Map<String, dynamic> json) {
    return ModelMateri(
      id: json['id'] as int,
      kelas: json['kelas'] as String,
      judul: json['judul'] as String,
      guruMapel: json['guru_mapel'] as String,
      deskripsi: json['deskripsi'] as String,
      halamanKonten: List<String>.from(json['halaman_konten'] as List<dynamic>),
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'kelas': kelas,
        'judul': judul,
        'guru_mapel': guruMapel,
        'deskripsi': deskripsi,
        'halaman_konten': halamanKonten,
      };
}
