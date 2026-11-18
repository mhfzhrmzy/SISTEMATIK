/// Model data satu materi pelajaran.
/// Dirancang agar mudah di-map dari respons JSON API nantinya.
class ModelMateri {
  final int id;
  final String kelas;
  final String judul;
  final String guruMapel;
  final String deskripsi;
  final String? urlFile;
  final int jumlahQuiz;
  final bool pretestSelesai;

  /// Daftar halaman konten materi (teks/HTML per halaman).
  final List<String> halamanKonten;

  const ModelMateri({
    required this.id,
    required this.kelas,
    required this.judul,
    required this.guruMapel,
    required this.deskripsi,
    required this.halamanKonten,
    this.urlFile,
    this.jumlahQuiz = 0,
    this.pretestSelesai = false,
  });

  /// Jumlah halaman materi.
  int get totalHalaman => halamanKonten.length;

  // ── Factory dari JSON (siap pakai saat integrasi API) ───────────────────
  factory ModelMateri.fromJson(Map<String, dynamic> json) {
    final isi = (json['isi_materi'] ?? json['deskripsi'] ?? '').toString();
    final pages = json['halaman_konten'];
    final id = json['id_materi'] ?? json['id'];

    return ModelMateri(
      id: id is num ? id.toInt() : int.parse(id.toString()),
      kelas: (json['kelas'] ?? '').toString(),
      judul: (json['judul_materi'] ?? json['judul'] ?? '').toString(),
      guruMapel: (json['guru_pengajar'] ?? json['guru_mapel'] ?? '').toString(),
      deskripsi: isi,
      halamanKonten: pages is List
          ? pages.map((page) => page.toString()).toList()
          : (isi.isEmpty ? const [] : [isi]),
      urlFile: json['upload_file']?.toString(),
      jumlahQuiz: _integer(json['quiz_count']),
      pretestSelesai: json['pretest_completed'] == true,
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

  static int _integer(dynamic value) =>
      value is num ? value.toInt() : int.tryParse('$value') ?? 0;
}
