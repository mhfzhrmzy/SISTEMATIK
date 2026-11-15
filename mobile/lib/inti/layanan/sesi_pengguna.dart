import 'package:pretest_posttest_app/inti/layanan/penyimpanan_sesi.dart';

/// Sesi login siswa yang aktif.
///
/// Token disimpan di memori untuk dipakai request API, dan juga di secure
/// storage agar sesi bertahan saat aplikasi ditutup.
class SesiPengguna {
  SesiPengguna._();

  static String? _token;
  static Map<String, dynamic>? _pengguna;
  static DateTime? _kedaluwarsa;
  static bool _sudahDimuat = false;

  static String? get token => _token;

  static String? get nisn => _pengguna?['nisn']?.toString();

  static String? get nama => _pengguna?['nama_lengkap']?.toString();

  /// Label kelas siap tampil, misalnya "Kelas 10 1 - Teknik Komputer & Jaringan".
  static String? get kelas {
    final namaKelas = _pengguna?['kelas']?.toString();
    final jurusan = _pengguna?['jurusan']?.toString();

    final bagian = <String>[
      if (namaKelas != null && namaKelas.isNotEmpty) 'Kelas $namaKelas',
      if (jurusan != null && jurusan.isNotEmpty) jurusan,
    ];

    return bagian.isEmpty ? null : bagian.join(' - ');
  }

  static Map<String, dynamic>? get pengguna => _pengguna;

  static DateTime? get kedaluwarsaPada => _kedaluwarsa;

  static bool get sudahMasuk => _token != null && _token!.isNotEmpty;

  /// True jika token sudah melewati waktu kedaluwarsa yang diberikan server.
  static bool get kedaluwarsa {
    if (!sudahMasuk) {
      return false;
    }

    final waktu = _kedaluwarsa;

    return waktu == null || !waktu.isAfter(DateTime.now());
  }

  /// Membaca sesi dari secure storage saat aplikasi dibuka.
  ///
  /// Mengembalikan true jika ada sesi tersimpan yang ternyata sudah
  /// kedaluwarsa, supaya halaman login bisa menampilkan pesan yang sesuai.
  static Future<bool> muat() async {
    if (_sudahDimuat) {
      return false;
    }

    _sudahDimuat = true;
    final data = await PenyimpananSesi.baca();

    if (data == null) {
      return false;
    }

    if (!data.kedaluwarsa.isAfter(DateTime.now())) {
      await hapus();
      return true;
    }

    _token = data.token;
    _pengguna = Map<String, dynamic>.from(data.pengguna);
    _kedaluwarsa = data.kedaluwarsa;

    return false;
  }

  /// Menyimpan hasil login ke memori dan secure storage.
  static Future<void> simpan({
    required String token,
    required Map<String, dynamic> pengguna,
    required DateTime kedaluwarsa,
  }) async {
    _token = token;
    _pengguna = Map<String, dynamic>.from(pengguna);
    _kedaluwarsa = kedaluwarsa;

    await PenyimpananSesi.simpan(
      DataSesiTersimpan(
        token: token,
        pengguna: _pengguna!,
        kedaluwarsa: kedaluwarsa,
      ),
    );
  }

  /// Memperbarui profil tanpa mengubah token dan masa berlakunya.
  static Future<void> perbaruiPengguna(Map<String, dynamic> pengguna) async {
    final token = _token;
    final kedaluwarsa = _kedaluwarsa;

    if (token == null || kedaluwarsa == null) {
      return;
    }

    _pengguna = Map<String, dynamic>.from(pengguna);

    await PenyimpananSesi.simpan(
      DataSesiTersimpan(
        token: token,
        pengguna: _pengguna!,
        kedaluwarsa: kedaluwarsa,
      ),
    );
  }

  /// Menghapus sesi dari memori dan secure storage.
  static Future<void> hapus() async {
    _token = null;
    _pengguna = null;
    _kedaluwarsa = null;

    await PenyimpananSesi.hapus();
  }
}
