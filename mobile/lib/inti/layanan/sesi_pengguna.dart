import 'package:pretest_posttest_app/inti/layanan/penyimpanan_sesi.dart';

/// Sesi siswa yang aktif, disimpan di memori dan secure storage.
class SesiPengguna {
  SesiPengguna._();

  static String? _token;
  static Map<String, dynamic>? _pengguna;
  static DateTime? _kedaluwarsa;
  static bool _sudahDimuat = false;

  static String? get token => _token;
  static Map<String, dynamic>? get pengguna => _pengguna;
  static DateTime? get kedaluwarsaPada => _kedaluwarsa;
  static bool get sudahMasuk => _token?.isNotEmpty == true;

  static String? get nisn => _pengguna?['nisn']?.toString();
  static String? get nama => _pengguna?['nama_lengkap']?.toString();
  static String? get jurusan => _pengguna?['jurusan']?.toString();
  static String? get fotoProfil =>
      (_pengguna?['foto_profile'] ?? _pengguna?['foto_profil'])?.toString();

  /// Mengambil nama kelas murni (misal: "X-1") tanpa prefiks atau nama jurusan.
  static String? get namaKelasMurni {
    var rawKelas = _pengguna?['kelas']?.toString().trim();
    if (rawKelas == null || rawKelas.isEmpty) return null;

    final jurusan = _pengguna?['jurusan']?.toString().trim();
    if (jurusan != null &&
        jurusan.isNotEmpty &&
        rawKelas.toLowerCase().contains(jurusan.toLowerCase())) {
      rawKelas = rawKelas
          .replaceAll(
              RegExp(' - ${RegExp.escape(jurusan)}', caseSensitive: false), '')
          .replaceAll(RegExp(RegExp.escape(jurusan), caseSensitive: false), '')
          .trim();
      if (rawKelas.endsWith('-')) {
        rawKelas = rawKelas.substring(0, rawKelas.length - 1).trim();
      }
    }

    rawKelas = rawKelas
        .replaceFirst(RegExp(r'^Kelas\s+', caseSensitive: false), '')
        .replaceFirst(RegExp(r'^10[\s\-]'), 'X-')
        .replaceFirst(RegExp(r'^10$'), 'X');

    return rawKelas.isEmpty ? null : rawKelas;
  }

  static String? get kelas {
    final murni = namaKelasMurni;
    final jurusan = _pengguna?['jurusan']?.toString().trim();

    final parts = <String>[
      if (murni != null && murni.isNotEmpty) 'Kelas $murni',
      if (jurusan != null && jurusan.isNotEmpty) jurusan,
    ];
    return parts.isEmpty ? null : parts.join(' - ');
  }

  static bool get kedaluwarsa {
    if (!sudahMasuk) return false;
    final expiry = _kedaluwarsa;
    return expiry == null || !expiry.isAfter(DateTime.now());
  }

  static Future<bool> muat() async {
    if (_sudahDimuat) return false;
    _sudahDimuat = true;
    final data = await PenyimpananSesi.baca();
    if (data == null) return false;
    if (!data.kedaluwarsa.isAfter(DateTime.now())) {
      await hapus();
      return true;
    }
    _token = data.token;
    _pengguna = Map<String, dynamic>.from(data.pengguna);
    _kedaluwarsa = data.kedaluwarsa;
    return false;
  }

  static Future<void> simpan({
    required String token,
    Map<String, dynamic>? pengguna,
    DateTime? kedaluwarsa,
    String? nisn,
    String? nama,
    String? kelas,
    String? jurusan,
    String? fotoProfil,
  }) async {
    final profile = pengguna == null
        ? <String, dynamic>{
            if (nisn != null) 'nisn': nisn,
            if (nama != null) 'nama_lengkap': nama,
            if (kelas != null) 'kelas': kelas,
            if (jurusan != null) 'jurusan': jurusan,
            if (fotoProfil != null) 'foto_profile': fotoProfil,
          }
        : Map<String, dynamic>.from(pengguna);
    final expiry = kedaluwarsa ?? DateTime.now().add(const Duration(days: 30));

    _token = token;
    _pengguna = profile;
    _kedaluwarsa = expiry;
    _sudahDimuat = true;
    await PenyimpananSesi.simpan(
      DataSesiTersimpan(
        token: token,
        pengguna: profile,
        kedaluwarsa: expiry,
      ),
    );
  }

  static Future<void> perbaruiPengguna(
    Map<String, dynamic> pengguna,
  ) async {
    final token = _token;
    final expiry = _kedaluwarsa;
    if (token == null || expiry == null) return;
    _pengguna = Map<String, dynamic>.from(pengguna);
    await PenyimpananSesi.simpan(
      DataSesiTersimpan(
        token: token,
        pengguna: _pengguna!,
        kedaluwarsa: expiry,
      ),
    );
  }

  static Future<void> perbaruiProfil({
    String? nama,
    String? kelas,
    String? jurusan,
    String? fotoProfil,
  }) async {
    final updated = Map<String, dynamic>.from(_pengguna ?? const {});
    if (nama != null) updated['nama_lengkap'] = nama;
    if (kelas != null) updated['kelas'] = kelas;
    if (jurusan != null) updated['jurusan'] = jurusan;
    if (fotoProfil != null) updated['foto_profile'] = fotoProfil;
    await perbaruiPengguna(updated);
  }

  static Future<void> hapus() async {
    _token = null;
    _pengguna = null;
    _kedaluwarsa = null;
    _sudahDimuat = true;
    await PenyimpananSesi.hapus();
  }
}
