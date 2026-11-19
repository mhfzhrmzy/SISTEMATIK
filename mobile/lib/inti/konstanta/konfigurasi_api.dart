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

  /// Menormalkan URL foto profil agar selalu dapat diakses dari platform
  /// yang aktif (misalnya mengganti host localhost ke 10.0.2.2 di emulator Android
  /// atau menyematkan host jika URL berbentuk path relatif).
  static String? perbaikiUrlFoto(String? url) {
    if (url == null || url.trim().isEmpty) return null;
    final trimmed = url.trim();
    final uri = Uri.tryParse(trimmed);
    if (uri == null) return trimmed;

    // Jika path relatif (misal: /api/siswa/foto/... atau /storage/...), tempelkan alamat host
    if (!uri.hasScheme) {
      final base = alamatDasar.replaceAll(RegExp(r'/api/?$'), '');
      final cleanPath = trimmed.startsWith('/') ? trimmed : '/$trimmed';
      return '$base$cleanPath';
    }

    // Jika URL mengarah ke localhost/127.0.0.1 tapi app berjalan di emulator Android atau IP LAN
    final baseApiUri = Uri.parse(alamatDasar);
    if ((uri.host == 'localhost' || uri.host == '127.0.0.1') &&
        (baseApiUri.host != 'localhost' && baseApiUri.host != '127.0.0.1')) {
      return uri.replace(
        host: baseApiUri.host,
        port: baseApiUri.hasPort ? baseApiUri.port : uri.port,
      ).toString();
    }

    return trimmed;
  }
}
