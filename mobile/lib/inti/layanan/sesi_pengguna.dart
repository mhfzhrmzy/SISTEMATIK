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

  static String? get token => _token;

  static String? get nisn => _nisn;

  static String? get nama => _nama;

  static String? get kelas => _kelas;

  static bool get sudahMasuk => _token != null && _token!.isNotEmpty;

  /// Menyimpan data login yang berhasil.
  static void simpan({
    required String token,
    required String nisn,
    required String nama,
    required String kelas,
  }) {
    _token = token;
    _nisn = nisn;
    _nama = nama;
    _kelas = kelas;
  }

  /// Menghapus seluruh data sesi saat logout.
  static void hapus() {
    _token = null;
    _nisn = null;
    _nama = null;
    _kelas = null;
  }
}
