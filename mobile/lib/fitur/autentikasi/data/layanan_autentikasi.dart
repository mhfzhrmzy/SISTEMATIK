import 'dart:async';
import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:pretest_posttest_app/inti/konstanta/konfigurasi_api.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/sesi_pengguna.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/models/hasil_login.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/models/pengguna_siswa.dart';

/// Layanan komunikasi dengan endpoint autentikasi Laravel.
class LayananAutentikasi {
  const LayananAutentikasi();

  /// Login siswa menggunakan NISN dan password.
  Future<HasilLogin> masukSiswa({
    required String nisn,
    required String password,
  }) async {
    final uri = Uri.parse('${KonfigurasiApi.alamatDasar}/login/siswa');

    try {
      final response = await http
          .post(
            uri,
            headers: const {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            body: jsonEncode({
              'nisn': nisn,
              'password': password,
            }),
          )
          .timeout(const Duration(seconds: 15));

      final data = _bacaJson(response.body);
      final dataPengguna = data['user'];
      final token = data['token'];

      if (response.statusCode == 200 &&
          token is String &&
          dataPengguna is Map<String, dynamic>) {
        final pengguna = PenggunaSiswa.fromJson(dataPengguna);

        SesiPengguna.simpan(
          token: token,
          nisn: pengguna.nisn,
          nama: pengguna.namaLengkap,
          kelas: pengguna.labelKelas,
        );

        return HasilLogin(
          token: token,
          pengguna: pengguna,
        );
      }

      throw KesalahanApi(
        _pesanKesalahan(data),
        statusCode: response.statusCode,
      );
    } on TimeoutException {
      throw const KesalahanApi(
        'Server terlalu lama merespons. Periksa koneksi Anda.',
      );
    } on http.ClientException {
      throw const KesalahanApi(
        'Tidak dapat terhubung ke server. Pastikan API Laravel berjalan.',
      );
    } on FormatException {
      throw const KesalahanApi('Respons server tidak valid.');
    }
  }

  Map<String, dynamic> _bacaJson(String body) {
    if (body.trim().isEmpty) {
      return <String, dynamic>{};
    }

    final hasil = jsonDecode(body);

    return hasil is Map<String, dynamic> ? hasil : <String, dynamic>{};
  }

  String _pesanKesalahan(Map<String, dynamic> data) {
    final errors = data['errors'];

    if (errors is Map && errors.isNotEmpty) {
      final pesanPertama = errors.values.first;

      if (pesanPertama is List && pesanPertama.isNotEmpty) {
        return pesanPertama.first.toString();
      }
    }

    return data['message']?.toString() ?? 'Login gagal. Silakan coba lagi.';
  }
}
