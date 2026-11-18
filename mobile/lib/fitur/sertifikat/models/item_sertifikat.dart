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
    final id = json['id_sertifikat'];
    final tanggal =
        DateTime.tryParse('${json['tanggal_terbit'] ?? json['tanggal']}');
    final kategori = (json['kategori'] ?? json['label'])?.toString();
    return ItemSertifikat(
      id: id is num ? id.toInt() : int.tryParse('$id'),
      label: kategori == null || kategori.isEmpty ? 'Sertifikat' : kategori,
      judul: (json['judul_sertifikat'] ?? json['judul'] ?? '').toString(),
      tanggalTerbit: tanggal == null
          ? 'Tanggal terbit tidak tersedia'
          : 'Diterbitkan pada: ${_formatTanggal(tanggal)}',
      tanggal: tanggal ?? DateTime(1970),
      urlFile: (json['file_url'] ?? json['url_file'])?.toString(),
    );
  }

  static String _formatTanggal(DateTime date) {
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
    return '${date.day.toString().padLeft(2, '0')} '
        '${bulan[date.month - 1]} ${date.year}';
  }
}
