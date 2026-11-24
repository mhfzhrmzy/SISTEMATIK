import 'dart:convert';

import 'package:shared_preferences/shared_preferences.dart';

/// Layer caching data JSON ke [SharedPreferences] untuk akses offline.
///
/// Menyimpan respons API terakhir dalam bentuk string JSON (di-serialize)
/// berdasarkan key tertentu (misalnya 'materi_kelas_X', 'leaderboard_keseluruhan').
class CacheLokal {
  CacheLokal._();

  static const String _prefixKey = 'cache_api_';

  /// Menyimpan [data] (Map atau List) ke penyimpanan lokal.
  static Future<bool> simpan(String key, dynamic data) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final stringJson = jsonEncode(data);
      return await prefs.setString('$_prefixKey$key', stringJson);
    } catch (_) {
      return false;
    }
  }

  /// Membaca data tersimpan (Map atau List).
  ///
  /// Mengembalikan `null` jika key tidak ditemukan atau gagal di-decode.
  static Future<dynamic> baca(String key) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final stringJson = prefs.getString('$_prefixKey$key');

      if (stringJson == null || stringJson.isEmpty) {
        return null;
      }

      return jsonDecode(stringJson);
    } catch (_) {
      return null;
    }
  }

  /// Menghapus cache spesifik.
  static Future<bool> hapus(String key) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      return await prefs.remove('$_prefixKey$key');
    } catch (_) {
      return false;
    }
  }

  /// Bersihkan seluruh cache API (dipanggil saat logout).
  static Future<void> bersihkanSemuaCacheApi() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final keys = prefs.getKeys().where((k) => k.startsWith(_prefixKey));
      for (final k in keys) {
        await prefs.remove(k);
      }
    } catch (_) {
      // Abaikan error
    }
  }
}
