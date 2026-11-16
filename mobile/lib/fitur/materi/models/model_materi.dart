/// Model data satu materi pelajaran.
/// Mendukung pemetaan langsung dari respons JSON API Laravel maupun data lokal.
class ModelMateri {
  final int id;
  final String kelas;
  final String judul;
  final String guruMapel;
  final String deskripsi;

  /// Daftar halaman konten materi (teks per halaman).
  final List<String> halamanKonten;

  /// Kode modul (misal: 10TKJ-01)
  final String? kodeModul;

  /// URL file materi PDF (jika tersedia)
  final String? fileUrl;

  /// Persentase kemajuan belajar siswa (0 - 100%)
  final int progress;

  /// Status apakah materi sudah pernah dibuka
  final bool isDibuka;

  /// Status apakah materi sudah ditandai selesai
  final bool isSelesai;

  /// Info pre-test & post-test
  final bool hasPretest;
  final int? idPretest;
  final bool pretestCompleted;
  final bool hasPosttest;
  final int? idPosttest;
  final bool posttestCompleted;

  const ModelMateri({
    required this.id,
    required this.kelas,
    required this.judul,
    required this.guruMapel,
    required this.deskripsi,
    this.halamanKonten = const [],
    this.kodeModul,
    this.fileUrl,
    this.progress = 0,
    this.isDibuka = false,
    this.isSelesai = false,
    this.hasPretest = false,
    this.idPretest,
    this.pretestCompleted = false,
    this.hasPosttest = false,
    this.idPosttest,
    this.posttestCompleted = false,
  });

  /// Jumlah halaman materi.
  int get totalHalaman => halamanKonten.isEmpty ? 1 : halamanKonten.length;

  /// Label tampilan kode/kelas untuk kartu materi.
  String get labelTampilanKelas =>
      kodeModul != null && kodeModul!.isNotEmpty ? kodeModul! : kelas;

  // ── Factory dari JSON (API Laravel & Dummy) ───────────────────────────────
  factory ModelMateri.fromJson(Map<String, dynamic> json) {
    final rawId = json['id_materi'] ?? json['id'] ?? 0;
    final rawJudul = json['judul_materi'] ?? json['judul'] ?? '';
    final rawGuru = json['guru_pengajar'] ?? json['guru_mapel'] ?? '';
    final rawDeskripsi = json['isi_materi'] ?? json['deskripsi'] ?? '';
    final rawKelas = json['kode_modul'] ?? json['kelas'] ?? '';

    List<String> halaman = [];
    if (json['halaman_konten'] is List) {
      halaman = List<String>.from(json['halaman_konten'] as List);
    } else if (rawDeskripsi.toString().isNotEmpty) {
      halaman = [rawDeskripsi.toString()];
    }

    final progressVal = json['progress'];
    final idPretestVal = json['id_pretest'];
    final idPosttestVal = json['id_posttest'];

    return ModelMateri(
      id: rawId is num ? rawId.toInt() : int.tryParse(rawId.toString()) ?? 0,
      kelas: rawKelas.toString(),
      judul: rawJudul.toString(),
      guruMapel: rawGuru.toString(),
      deskripsi: rawDeskripsi.toString(),
      halamanKonten: halaman,
      kodeModul: json['kode_modul']?.toString(),
      fileUrl: json['upload_file']?.toString(),
      progress: progressVal is num ? progressVal.toInt() : 0,
      isDibuka: json['is_dibuka'] == true,
      isSelesai: json['is_selesai'] == true,
      hasPretest: json['has_pretest'] == true,
      idPretest: idPretestVal is num ? idPretestVal.toInt() : null,
      pretestCompleted: json['pretest_completed'] == true,
      hasPosttest: json['has_posttest'] == true,
      idPosttest: idPosttestVal is num ? idPosttestVal.toInt() : null,
      posttestCompleted: json['posttest_completed'] == true,
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'kelas': kelas,
        'judul': judul,
        'guru_mapel': guruMapel,
        'deskripsi': deskripsi,
        'halaman_konten': halamanKonten,
        'kode_modul': kodeModul,
        'upload_file': fileUrl,
        'progress': progress,
        'is_dibuka': isDibuka,
        'is_selesai': isSelesai,
      };
}
