import 'dart:async';
import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:pretest_posttest_app/inti/konstanta/konfigurasi_api.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';
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

      final data = KlienApi.bacaJson(response);
      final dataPengguna = data['user'];
      final token = data['token'];

      if (response.statusCode == 200 &&
          token is String &&
          dataPengguna is Map<String, dynamic>) {
        final pengguna = PenggunaSiswa.fromJson(dataPengguna);
        final kedaluwarsa = DateTime.tryParse(
              data['expires_at']?.toString() ?? '',
            )?.toLocal() ??
            DateTime.now().add(const Duration(days: 30));

        await SesiPengguna.simpan(
          token: token,
          pengguna: Map<String, dynamic>.from(dataPengguna),
          kedaluwarsa: kedaluwarsa,
        );

        return HasilLogin(
          token: token,
          pengguna: pengguna,
        );
      }

      throw KesalahanApi(
        KlienApi.pesanKesalahan(
          data,
          bawaan: 'Login gagal. Silakan coba lagi.',
        ),
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

  /// Mengambil profil terbaru dan memperbarui cache sesi.
  ///
  /// Dipakai saat aplikasi dibuka untuk memastikan token masih berlaku.
  Future<Map<String, dynamic>> ambilProfil({
    bool tanganiSesiBerakhir = false,
  }) async {
    final response = await KlienApi.get(
      '/siswa/profil',
      tanganiSesiBerakhir: tanganiSesiBerakhir,
    );
    final data = KlienApi.bacaJson(response);
    final dataPengguna = data['user'];

    if (response.statusCode == 200 && dataPengguna is Map<String, dynamic>) {
      final pengguna = Map<String, dynamic>.from(dataPengguna);
      await SesiPengguna.perbaruiPengguna(pengguna);

      return pengguna;
    }

    throw KesalahanApi(
      KlienApi.pesanKesalahan(
        data,
        bawaan: 'Profil siswa gagal diambil.',
      ),
      statusCode: response.statusCode,
    );
  }

  /// Logout: cabut token di server, lalu hapus sesi lokal.
  ///
  /// Sesi lokal tetap dihapus walaupun perangkat sedang offline atau token
  /// sudah tidak valid, supaya pengguna benar-benar keluar dari aplikasi.
  Future<void> keluar() async {
    try {
      if (SesiPengguna.sudahMasuk) {
        await KlienApi.post(
          '/siswa/logout',
          tanganiSesiBerakhir: false,
        ).timeout(const Duration(seconds: 8));
      }
    } catch (_) {
      // Abaikan kegagalan jaringan; sesi lokal tetap harus dihapus.
    } finally {
      await SesiPengguna.hapus();
    }
  }
}
