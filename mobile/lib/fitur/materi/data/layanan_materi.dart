import 'package:pretest_posttest_app/inti/layanan/cache_lokal.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';
import 'package:pretest_posttest_app/fitur/materi/models/model_materi.dart';

/// Hasil respons materi dari API (online atau cache offline).
class HasilMateri {
  final List<ModelMateri> daftar;

  /// True jika data diambil dari cache lokal, false jika dari API.
  final bool dariCache;

  const HasilMateri(this.daftar, this.dariCache);
}

/// Layanan komunikasi dengan endpoint materi & bahan ajar Laravel.
class LayananMateri {
  const LayananMateri();

  static const String _keyCache = 'materi_siswa';

  /// Mengambil daftar seluruh materi pelajaran yang tersedia untuk kelas siswa.
  ///
  /// Jika API gagal, otomatis mengembalikan data terakhir dari cache lokal.
  Future<HasilMateri> ambilDaftarMateri() async {
    try {
      final response = await KlienApi.get('/siswa/materi');
      final data = KlienApi.bacaJson(response);

      if (response.statusCode == 200 && data['data'] is List) {
        final listJson = data['data'] as List<dynamic>;
        final daftar = listJson
            .map((item) => ModelMateri.fromJson(item as Map<String, dynamic>))
            .toList();

        // Simpan respons terakhir ke cache untuk offline.
        await CacheLokal.simpan(_keyCache, listJson);

        return HasilMateri(daftar, false);
      }

      throw KesalahanApi(
        KlienApi.pesanKesalahan(
          data,
          bawaan: 'Gagal mengambil daftar materi.',
        ),
        statusCode: response.statusCode,
      );
    } catch (_) {
      // API gagal; coba data cache terakhir.
      final cached = await CacheLokal.baca(_keyCache);
      if (cached is List && cached.isNotEmpty) {
        final daftar = cached
            .map((item) =>
                ModelMateri.fromJson(Map<String, dynamic>.from(item as Map)))
            .toList();
        return HasilMateri(daftar, true);
      }
      // Gagal tanpa cache — lemparkan error ke caller.
      return const HasilMateri([], false);
    }
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
