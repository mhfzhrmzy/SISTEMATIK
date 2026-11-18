import 'dart:typed_data';

import 'package:pretest_posttest_app/fitur/sertifikat/models/item_sertifikat.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';

class LayananSertifikat {
  LayananSertifikat({KlienApi? api}) : _api = api ?? klienApi;

  final KlienApi _api;

  Future<List<ItemSertifikat>> daftar() async {
    final response = await _api.getJson('siswa/sertifikat');
    final data = response['data'];
    if (data is! List) {
      throw const FormatException('Daftar sertifikat dari server tidak valid.');
    }
    return data
        .whereType<Map<String, dynamic>>()
        .map(ItemSertifikat.fromJson)
        .toList();
  }

  Future<Uint8List> unduh(int idSertifikat) =>
      _api.getBytes('siswa/sertifikat/$idSertifikat/download');
}
