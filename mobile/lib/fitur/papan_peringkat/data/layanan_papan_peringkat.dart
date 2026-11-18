import 'package:pretest_posttest_app/fitur/papan_peringkat/models/pengguna_peringkat.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';

class DataPapanPeringkat {
  final String? kelas;
  final String? jurusan;
  final List<PenggunaPeringkat> siswa;

  const DataPapanPeringkat({
    required this.siswa,
    this.kelas,
    this.jurusan,
  });
}

class LayananPapanPeringkat {
  LayananPapanPeringkat({KlienApi? api}) : _api = api ?? klienApi;

  final KlienApi _api;

  Future<DataPapanPeringkat> ambil() async {
    final response = await _api.getJson('siswa/leaderboard');
    final data = response['data'];
    if (data is! List) {
      throw const FormatException('Data leaderboard dari server tidak valid.');
    }
    return DataPapanPeringkat(
      kelas: response['kelas']?.toString(),
      jurusan: response['jurusan']?.toString(),
      siswa: data
          .whereType<Map<String, dynamic>>()
          .map(PenggunaPeringkat.fromJson)
          .toList(),
    );
  }
}
