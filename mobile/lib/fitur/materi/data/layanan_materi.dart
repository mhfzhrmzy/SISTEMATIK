import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';
import 'package:pretest_posttest_app/fitur/materi/models/model_materi.dart';

/// Layanan komunikasi dengan endpoint materi & bahan ajar Laravel.
class LayananMateri {
  const LayananMateri();

  /// Mengambil daftar seluruh materi pelajaran yang tersedia untuk kelas siswa.
  Future<List<ModelMateri>> ambilDaftarMateri() async {
    final response = await KlienApi.get('/siswa/materi');
    final data = KlienApi.bacaJson(response);

    if (response.statusCode == 200 && data['data'] is List) {
      final listJson = data['data'] as List<dynamic>;
      return listJson
          .map((item) => ModelMateri.fromJson(item as Map<String, dynamic>))
          .toList();
    }

    throw KesalahanApi(
      KlienApi.pesanKesalahan(data, bawaan: 'Gagal mengambil daftar materi.'),
      statusCode: response.statusCode,
    );
  }

  /// Mengambil detail satu materi pelajaran beserta info akses pre-test dan kuisnya.
  Future<ModelMateri> ambilDetailMateri(int idMateri) async {
    final response = await KlienApi.get('/siswa/materi/$idMateri');
    final data = KlienApi.bacaJson(response);

    if (response.statusCode == 200 && data['data'] is Map<String, dynamic>) {
      return ModelMateri.fromJson(data['data'] as Map<String, dynamic>);
    }

    throw KesalahanApi(
      KlienApi.pesanKesalahan(data, bawaan: 'Gagal mengambil detail materi.'),
      statusCode: response.statusCode,
    );
  }

  /// Menandai modul materi selesai dipelajari oleh siswa atau membatalkan status selesai.
  Future<bool> tandaiSelesai(int idMateri) async {
    final response = await KlienApi.post('/siswa/materi/$idMateri/selesai');
    final data = KlienApi.bacaJson(response);

    if (response.statusCode == 200) {
      return data['is_selesai'] == true;
    }

    throw KesalahanApi(
      KlienApi.pesanKesalahan(data, bawaan: 'Gagal memperbarui status materi.'),
      statusCode: response.statusCode,
    );
  }
}
