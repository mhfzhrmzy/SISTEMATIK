import 'dart:typed_data';

import 'package:pretest_posttest_app/fitur/autentikasi/models/pengguna_siswa.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';

class LayananProfil {
  LayananProfil({KlienApi? api}) : _api = api ?? klienApi;

  final KlienApi _api;

  Future<PenggunaSiswa> ambil() async {
    final response = await _api.getJson('siswa/profil');
    return _pengguna(response);
  }

  Future<PenggunaSiswa> ubahNama(String nama) async {
    final response = await _api.putJson(
      'siswa/profil',
      {'nama_lengkap': nama},
    );
    return _pengguna(response);
  }

  Future<void> ubahPassword({
    required String lama,
    required String baru,
  }) async {
    await _api.putJson('siswa/profil/password', {
      'password_lama': lama,
      'password_baru': baru,
      'password_baru_confirmation': baru,
    });
  }

  Future<String> unggahFoto({
    required String namaFile,
    required Uint8List bytes,
  }) async {
    final response = await _api.postFile(
      'siswa/profil/foto',
      field: 'foto',
      filename: namaFile,
      bytes: bytes,
    );
    final url = response['foto_profile'];
    if (url is! String || url.isEmpty) {
      throw const FormatException('URL foto profil dari server tidak valid.');
    }
    return url;
  }

  Future<void> hapusFoto() async {
    await _api.delete('siswa/profil/foto');
  }

  Future<void> keluar() async {
    await _api.postJson('siswa/logout', const {});
  }

  PenggunaSiswa _pengguna(Map<String, dynamic> response) {
    final user = response['user'];
    if (user is! Map<String, dynamic>) {
      throw const FormatException('Data profil dari server tidak valid.');
    }
    return PenggunaSiswa.fromJson(user);
  }
}
