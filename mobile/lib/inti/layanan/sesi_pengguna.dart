/// Penyimpanan sesi login selama aplikasi berjalan.
///
/// Token dipakai untuk header `Authorization: Bearer <token>` pada
/// request API berikutnya.
class SesiPengguna {
  SesiPengguna._();

  static String? _token;
  static String? _nisn;
  static String? _nama;
  static String? _kelas;
  static String? _jurusan;
  static String? _fotoProfil;

  static String? get token => _token;

  static String? get nisn => _nisn;

  static String? get nama => _nama;

  static String? get kelas => _kelas;

  static String? get jurusan => _jurusan;

  static String? get fotoProfil => _fotoProfil;

  static bool get sudahMasuk => _token != null && _token!.isNotEmpty;

  /// Menyimpan data login yang berhasil.
  static void simpan({
    required String token,
    required String nisn,
    required String nama,
    required String kelas,
    String? jurusan,
    String? fotoProfil,
  }) {
    _token = token;
    _nisn = nisn;
    _nama = nama;
    _kelas = kelas;
    _jurusan = jurusan;
    _fotoProfil = fotoProfil;
  }

  static void perbaruiProfil({
    String? nama,
    String? kelas,
    String? jurusan,
    String? fotoProfil,
  }) {
    if (nama != null) _nama = nama;
    if (kelas != null) _kelas = kelas;
    if (jurusan != null) _jurusan = jurusan;
    if (fotoProfil != null) _fotoProfil = fotoProfil;
  }

  /// Menghapus seluruh data sesi saat logout.
  static void hapus() {
    _token = null;
    _nisn = null;
    _nama = null;
    _kelas = null;
    _jurusan = null;
    _fotoProfil = null;
  }
}
