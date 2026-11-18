import 'dart:typed_data';

import 'package:pretest_posttest_app/fitur/materi/models/model_materi.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';

class LayananMateri {
  LayananMateri({KlienApi? api}) : _api = api ?? klienApi;

  final KlienApi _api;

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

  Future<ModelMateri> detail(int idMateri) async {
    final response = await _api.getJson('siswa/materi/$idMateri');
    final data = response['data'];
    if (data is! Map<String, dynamic>) {
      throw const FormatException('Detail materi dari server tidak valid.');
    }
    return ModelMateri.fromJson(data);
  }

  Future<Uint8List> unduhFile(int idMateri) =>
      _api.getBytes('siswa/materi/$idMateri/file');
}
