import 'dart:convert';

import 'package:flutter_secure_storage/flutter_secure_storage.dart';

/// Data sesi yang disimpan di penyimpanan terenkripsi perangkat.
class DataSesiTersimpan {
  final String token;
  final Map<String, dynamic> pengguna;
  final DateTime kedaluwarsa;

  const DataSesiTersimpan({
    required this.token,
    required this.pengguna,
    required this.kedaluwarsa,
  });
}

/// Penyimpanan token dan profil siswa di secure storage.
///
/// Android memakai Keystore, iOS memakai Keychain. Jika plugin tidak
/// tersedia (misalnya saat widget test), sesi tetap berjalan di memori.
class PenyimpananSesi {
  PenyimpananSesi._();

  static const String _kunciSesi = 'sesi_pengguna';
  static const FlutterSecureStorage _penyimpanan = FlutterSecureStorage();
  static const Duration _batasWaktu = Duration(seconds: 5);

  static Future<void> simpan(DataSesiTersimpan data) async {
    try {
      await _penyimpanan
          .write(
            key: _kunciSesi,
            value: jsonEncode({
              'token': data.token,
              'pengguna': data.pengguna,
              'kedaluwarsa': data.kedaluwarsa.toUtc().toIso8601String(),
            }),
          )
          .timeout(_batasWaktu);
    } catch (_) {
      // Penyimpanan tidak tersedia; sesi tetap dipakai selama aplikasi hidup.
    }
  }

  static Future<DataSesiTersimpan?> baca() async {
    try {
      final mentah =
          await _penyimpanan.read(key: _kunciSesi).timeout(_batasWaktu);

      if (mentah == null || mentah.isEmpty) {
        return null;
      }

      final data = jsonDecode(mentah);

      if (data is! Map<String, dynamic>) {
        return null;
      }

      final token = data['token'];
      final pengguna = data['pengguna'];
      final kedaluwarsa = DateTime.tryParse(
        data['kedaluwarsa']?.toString() ?? '',
      );

      if (token is! String ||
          token.isEmpty ||
          pengguna is! Map<String, dynamic> ||
          kedaluwarsa == null) {
        return null;
      }

      return DataSesiTersimpan(
        token: token,
        pengguna: pengguna,
        kedaluwarsa: kedaluwarsa.toLocal(),
      );
    } catch (_) {
      return null;
    }
  }

  static Future<void> hapus() async {
    try {
      await _penyimpanan.delete(key: _kunciSesi).timeout(_batasWaktu);
    } catch (_) {
      // Tidak ada yang perlu dilakukan jika penyimpanan tidak tersedia.
    }
  }
}
