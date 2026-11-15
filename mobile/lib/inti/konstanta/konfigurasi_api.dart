import 'package:flutter/foundation.dart';

/// Konfigurasi alamat dasar API Laravel.
class KonfigurasiApi {
  KonfigurasiApi._();

  static const String _alamatDariDefine =
      String.fromEnvironment('API_BASE_URL');

  /// Alamat dasar API yang dipakai seluruh layanan.
  ///
  /// Bisa dioverride saat menjalankan aplikasi, misalnya untuk HP fisik:
  /// `flutter run --dart-define=API_BASE_URL=http://192.168.1.10:8000/api`
  static String get alamatDasar {
    final alamat =
        _alamatDariDefine.isNotEmpty ? _alamatDariDefine : _alamatDefault;

    return alamat.endsWith('/')
        ? alamat.substring(0, alamat.length - 1)
        : alamat;
  }

  static String get _alamatDefault {
    if (kIsWeb) {
      return 'http://127.0.0.1:8000/api';
    }

    // Android emulator memakai 10.0.2.2 untuk mengakses localhost komputer.
    if (defaultTargetPlatform == TargetPlatform.android) {
      return 'http://10.0.2.2:8000/api';
    }

    return 'http://127.0.0.1:8000/api';
  }
}
