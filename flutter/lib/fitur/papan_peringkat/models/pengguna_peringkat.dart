/// Item data siswa untuk leaderboard.
class PenggunaPeringkat {
  final int peringkat;
  final String nama;
  final int? skor;

  const PenggunaPeringkat({
    required this.peringkat,
    required this.nama,
    this.skor,
  });
}
