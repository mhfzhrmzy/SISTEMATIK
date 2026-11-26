import 'dart:typed_data';

/// Jenis file sertifikat yang didukung aplikasi.
///
/// Sertifikat dapat berupa PDF atau gambar (PNG/JPG). Deteksi dilakukan dari
/// magic bytes agar file yang hanya mengganti ekstensi tetap ditolak.
enum JenisFileSertifikat {
  pdf('pdf'),
  png('png'),
  jpeg('jpg'),
  tidakDiketahui('bin');

  const JenisFileSertifikat(this.ekstensi);

  /// Ekstensi yang dipakai saat menyimpan file hasil unduhan.
  final String ekstensi;

  /// Apakah jenis file ini dapat ditampilkan sebagai gambar.
  bool get adalahGambar =>
      this == JenisFileSertifikat.png || this == JenisFileSertifikat.jpeg;

  /// Apakah jenis file ini didukung untuk unggah/pratinjau.
  bool get didukung => this != JenisFileSertifikat.tidakDiketahui;
}

/// Mendeteksi jenis file dari isi (magic bytes), lalu dari nama file.
JenisFileSertifikat deteksiJenisFile(Uint8List bytes, {String? namaFile}) {
  if (_apakahPdf(bytes)) return JenisFileSertifikat.pdf;
  if (_apakahPng(bytes)) return JenisFileSertifikat.png;
  if (_apakahJpeg(bytes)) return JenisFileSertifikat.jpeg;
  return jenisDariNama(namaFile);
}

/// Mendeteksi jenis file hanya dari ekstensinya.
JenisFileSertifikat jenisDariNama(String? namaFile) {
  final nama = namaFile?.trim().toLowerCase() ?? '';
  final akhiran = nama.contains('.') ? nama.split('.').last : '';
  switch (akhiran) {
    case 'pdf':
      return JenisFileSertifikat.pdf;
    case 'png':
      return JenisFileSertifikat.png;
    case 'jpg':
    case 'jpeg':
      return JenisFileSertifikat.jpeg;
    default:
      return JenisFileSertifikat.tidakDiketahui;
  }
}

bool _apakahPdf(Uint8List bytes) =>
    bytes.length >= 4 &&
    bytes[0] == 0x25 &&
    bytes[1] == 0x50 &&
    bytes[2] == 0x44 &&
    bytes[3] == 0x46;

bool _apakahPng(Uint8List bytes) =>
    bytes.length >= 8 &&
    bytes[0] == 0x89 &&
    bytes[1] == 0x50 &&
    bytes[2] == 0x4E &&
    bytes[3] == 0x47 &&
    bytes[4] == 0x0D &&
    bytes[5] == 0x0A &&
    bytes[6] == 0x1A &&
    bytes[7] == 0x0A;

bool _apakahJpeg(Uint8List bytes) =>
    bytes.length >= 3 &&
    bytes[0] == 0xFF &&
    bytes[1] == 0xD8 &&
    bytes[2] == 0xFF;
