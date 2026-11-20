import 'dart:typed_data';

import 'package:pretest_posttest_app/inti/layanan/cache_lokal.dart';
import 'package:pretest_posttest_app/fitur/materi/models/model_materi.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';

class HasilMateri {
  const HasilMateri(this.daftar, this.dariCache);

  final List<ModelMateri> daftar;
  final bool dariCache;
}

class LayananMateri {
  LayananMateri({KlienApi? api}) : _api = api ?? klienApi;

  final KlienApi _api;
  static const String _keyCache = 'materi_siswa';

  Future<List<ModelMateri>> daftar() async {
    final response = await _api.getJson('siswa/materi');
    final data = response['data'];
    if (data is! List) {
      throw const FormatException('Daftar materi dari server tidak valid.');
    }
    return data
        .whereType<Map<String, dynamic>>()
        .map(ModelMateri.fromJson)
        .toList();
  }

  Future<HasilMateri> ambilDaftarMateri() async {
    try {
      final materis = await daftar();
      await CacheLokal.simpan(
        _keyCache,
        materis.map((materi) => materi.toJson()).toList(),
      );
      return HasilMateri(materis, false);
    } catch (_) {
      final cached = await CacheLokal.baca(_keyCache);
      if (cached is List && cached.isNotEmpty) {
        return HasilMateri(
          cached
              .whereType<Map>()
              .map(
                (item) => ModelMateri.fromJson(Map<String, dynamic>.from(item)),
              )
              .toList(),
          true,
        );
      }
      rethrow;
    }
  }

  Future<ModelMateri> detail(int idMateri) async {
    final response = await _api.getJson('siswa/materi/$idMateri');
    final data = response['data'];
    if (data is! Map<String, dynamic>) {
      throw const FormatException('Detail materi dari server tidak valid.');
    }
    return ModelMateri.fromJson(data);
  }

  Future<ModelMateri> ambilDetailMateri(int idMateri) => detail(idMateri);

  Future<bool> tandaiSelesai(int idMateri) async {
    final response = await _api.postJson(
      'siswa/materi/$idMateri/selesai',
      const {},
    );
    final selesai = response['is_selesai'];
    if (selesai is! bool) {
      throw const FormatException('Status materi dari server tidak valid.');
    }
    return selesai;
  }

  Future<Uint8List> unduhFile(int idMateri) =>
      _api.getBytes('siswa/materi/$idMateri/file');
}
