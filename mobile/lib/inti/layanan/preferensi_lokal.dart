import 'package:shared_preferences/shared_preferences.dart';

/// Penyimpanan preferensi user (key-value kecil) ke [SharedPreferences].
///
/// Dipakai untuk data kecil seperti indeks tab terakhir, flag, dsb.
class PreferensiLokal {
  PreferensiLokal._();

  static const String _prefixKey = 'pref_';

  static Future<SharedPreferences> _instansi() =>
      SharedPreferences.getInstance();

  /// Simpan string.
  static Future<bool> simpanString(String key, String nilai) async {
    try {
      final prefs = await _instansi();
      return await prefs.setString('$_prefixKey$key', nilai);
    } catch (_) {
      return false;
    }
  }

  /// Baca string.
  static Future<String?> bacaString(String key) async {
    try {
      final prefs = await _instansi();
      return prefs.getString('$_prefixKey$key');
    } catch (_) {
      return null;
    }
  }

  /// Simpan int.
  static Future<bool> simpanInt(String key, int nilai) async {
    try {
      final prefs = await _instansi();
      return await prefs.setInt('$_prefixKey$key', nilai);
    } catch (_) {
      return false;
    }
  }

  /// Baca int.
  static Future<int?> bacaInt(String key) async {
    try {
      final prefs = await _instansi();
      return prefs.getInt('$_prefixKey$key');
    } catch (_) {
      return null;
    }
  }

  /// Hapus satu preferensi.
  static Future<bool> hapus(String key) async {
    try {
      final prefs = await _instansi();
      return await prefs.remove('$_prefixKey$key');
    } catch (_) {
      return false;
    }
  }

  /// Bersihkan seluruh preferensi (dipanggil saat logout).
  static Future<void> bersihkanSemuaPreferensi() async {
    try {
      final prefs = await _instansi();
      final keys = prefs.getKeys().where((k) => k.startsWith(_prefixKey));
      for (final k in keys) {
        await prefs.remove(k);
      }
    } catch (_) {
      // Abaikan
    }
  }
}
