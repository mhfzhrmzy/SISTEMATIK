/// Item data siswa untuk leaderboard.
class PenggunaPeringkat {
  final int peringkat;
  final String nama;
  final int? skor;
  final bool isMe;

  const PenggunaPeringkat({
    required this.peringkat,
    required this.nama,
    this.skor,
    this.isMe = false,
  });

  factory PenggunaPeringkat.fromJson(Map<String, dynamic> json) {
    final rank = json['peringkat'];
    final score = json['total_poin'];
    return PenggunaPeringkat(
      peringkat: rank is num ? rank.toInt() : int.tryParse('$rank') ?? 0,
      nama: (json['nama_lengkap'] ?? '').toString(),
      skor: score is num ? score.toInt() : int.tryParse('$score') ?? 0,
      isMe: json['is_me'] == true,
    );
  }
}
