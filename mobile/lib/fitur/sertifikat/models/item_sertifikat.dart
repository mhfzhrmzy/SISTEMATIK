/// Item model untuk daftar sertifikat.
class ItemSertifikat {
  final int? id;
  final String label;
  final String judul;
  final String tanggalTerbit;
  final DateTime tanggal;
  final String? urlFile;

  const ItemSertifikat({
    this.id,
    required this.label,
    required this.judul,
    required this.tanggalTerbit,
    required this.tanggal,
    this.urlFile,
  });

  factory ItemSertifikat.fromJson(Map<String, dynamic> json) {
    final rawTanggal = json['tanggal_terbit'] ?? json['tanggal'];
    final tanggal = _parseTanggal(rawTanggal);
    final kategori = (json['kategori'] ?? json['label'])?.toString();
    return ItemSertifikat(
      id: json['id_sertifikat'] is num
          ? json['id_sertifikat'] as int
          : int.tryParse('${json['id_sertifikat'] ?? ''}'),
      label: kategori == null || kategori.isEmpty ? 'Sertifikat' : kategori,
      judul: (json['judul_sertifikat'] ?? json['judul'] ?? '').toString(),
      tanggalTerbit: tanggal == null
          ? 'Tanggal terbit tidak tersedia'
          : 'Diterbitkan pada: ${_formatTanggal(tanggal)}',
      tanggal: tanggal ?? DateTime(1970),
      urlFile: json['file_url'] ?? json['url_file'],
    );
  }

  static DateTime? _parseTanggal(dynamic raw) {
    if (raw == null) return null;
    if (raw is DateTime) return raw;
    return DateTime.tryParse(raw.toString());
  }

  static String formatTanggalPenuh(DateTime date) {
    const bulan = [
      'Jan',
      'Feb',
      'Mar',
      'Apr',
      'Mei',
      'Jun',
      'Jul',
      'Agt',
      'Sep',
      'Okt',
      'Nov',
      'Des',
    ];
    return '${date.day} ${bulan[date.month - 1]} ${date.year}';
  }

  static String _formatTanggal(DateTime date) => formatTanggalPenuh(date);
}
