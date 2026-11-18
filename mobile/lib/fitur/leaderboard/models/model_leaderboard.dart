/// Model data satu baris leaderboard (peringkat siswa).
class ModelLeaderboard {
  final int peringkat;
  final String nisn;
  final String nama;
  final int? skor;
  final bool saya;

  const ModelLeaderboard({
    required this.peringkat,
    required this.nisn,
    required this.nama,
    this.skor,
    this.saya = false,
  });

  factory ModelLeaderboard.fromJson(Map<String, dynamic> json) {
    final rawNisn = json['nisn']?.toString() ?? '';
    final rawNama = json['nama_lengkap']?.toString() ?? '';
    final rawPoin = json['total_poin'];

    return ModelLeaderboard(
      peringkat: json['peringkat'] is num ? json['peringkat'].toInt() : 0,
      nisn: rawNisn,
      nama: rawNama,
      skor: rawPoin is num ? rawPoin.toInt() : null,
      saya: json['is_me'] == true,
    );
  }
}
