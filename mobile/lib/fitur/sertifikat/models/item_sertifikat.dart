/// Item model untuk daftar sertifikat.
class ItemSertifikat {
  final String label;
  final String judul;
  final String tanggalTerbit;
  final DateTime tanggal;
  final String? urlFile;

  const ItemSertifikat({
    required this.label,
    required this.judul,
    required this.tanggalTerbit,
    required this.tanggal,
    this.urlFile,
  });
}
