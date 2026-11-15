import 'dart:async';
import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:pretest_posttest_app/inti/konstanta/konfigurasi_api.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/navigasi_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/sesi_pengguna.dart';

/// Klien HTTP untuk request API yang membutuhkan token login.
///
/// Menambahkan header `Authorization: Bearer <token>` secara otomatis dan
/// mengembalikan pengguna ke halaman login saat server menjawab 401.
class KlienApi {
  KlienApi._();

  static const Duration _batasWaktu = Duration(seconds: 15);

  static Future<http.Response> get(
    String path, {
    bool tanganiSesiBerakhir = true,
  }) {
    return _kirim(
      'GET',
      path,
      tanganiSesiBerakhir: tanganiSesiBerakhir,
    );
  }

  static Future<http.Response> post(
    String path, {
    Map<String, dynamic>? body,
    bool tanganiSesiBerakhir = true,
  }) {
    return _kirim(
      'POST',
      path,
      body: body,
      tanganiSesiBerakhir: tanganiSesiBerakhir,
    );
  }

  static Future<http.Response> put(
    String path, {
    Map<String, dynamic>? body,
    bool tanganiSesiBerakhir = true,
  }) {
    return _kirim(
      'PUT',
      path,
      body: body,
      tanganiSesiBerakhir: tanganiSesiBerakhir,
    );
  }

  /// Membaca body JSON dengan aman.
  static Map<String, dynamic> bacaJson(http.Response response) {
    if (response.body.trim().isEmpty) {
      return <String, dynamic>{};
    }

    final hasil = jsonDecode(response.body);

    return hasil is Map<String, dynamic> ? hasil : <String, dynamic>{};
  }

  /// Mengambil pesan error pertama dari response validasi Laravel.
  static String pesanKesalahan(
    Map<String, dynamic> data, {
    String bawaan = 'Terjadi kesalahan. Silakan coba lagi.',
  }) {
    final errors = data['errors'];

    if (errors is Map && errors.isNotEmpty) {
      final pesanPertama = errors.values.first;

      if (pesanPertama is List && pesanPertama.isNotEmpty) {
        return pesanPertama.first.toString();
      }
    }

    return data['message']?.toString() ?? bawaan;
  }

  static Future<http.Response> _kirim(
    String metode,
    String path, {
    Map<String, dynamic>? body,
    required bool tanganiSesiBerakhir,
  }) async {
    final uri = Uri.parse('${KonfigurasiApi.alamatDasar}$path');
    final headers = <String, String>{
      'Accept': 'application/json',
    };

    if (body != null) {
      headers['Content-Type'] = 'application/json';
    }

    final token = SesiPengguna.token;

    if (token != null && token.isNotEmpty) {
      headers['Authorization'] = 'Bearer $token';
    }

    late http.Response response;

    try {
      switch (metode) {
        case 'POST':
          response = await http
              .post(
                uri,
                headers: headers,
                body: body == null ? null : jsonEncode(body),
              )
              .timeout(_batasWaktu);
          break;
        case 'PUT':
          response = await http
              .put(
                uri,
                headers: headers,
                body: body == null ? null : jsonEncode(body),
              )
              .timeout(_batasWaktu);
          break;
        default:
          response = await http.get(uri, headers: headers).timeout(_batasWaktu);
      }
    } on TimeoutException {
      throw const KesalahanApi(
        'Server terlalu lama merespons. Periksa koneksi Anda.',
      );
    } on http.ClientException {
      throw const KesalahanApi(
        'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
      );
    }

    if (response.statusCode == 401 && tanganiSesiBerakhir) {
      await NavigasiAplikasi.sesiBerakhir();
    }

    return response;
  }
}
