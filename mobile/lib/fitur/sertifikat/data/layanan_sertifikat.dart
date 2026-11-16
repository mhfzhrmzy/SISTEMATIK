import 'package:pretest_posttest_app/inti/layanan/cache_lokal.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/item_sertifikat.dart';

/// Hasil daftar sertifikat dari API (online atau cache offline).
class HasilSertifikat {
  final List<ItemSertifikat> daftar;

  /// True jika data diambil dari cache lokal.
  final bool dariCache;

  const HasilSertifikat(this.daftar, this.dariCache);
}

/// Layanan komunikasi dengan endpoint sertifikat siswa Laravel.
class LayananSertifikat {
  const LayananSertifikat();

  static const String _keyCache = 'sertifikat_siswa';

  /// Mengambil daftar sertifikat siswa dari API.
  ///
  /// Jika API gagal, otomatis mengembalikan data terakhir dari cache lokal.
  Future<HasilSertifikat> ambilDaftarSertifikat() async {
    try {
      final response = await KlienApi.get('/siswa/sertifikat');
      final data = KlienApi.bacaJson(response);

      if (response.statusCode == 200 && data['data'] is List) {
        final listJson = data['data'] as List<dynamic>;
        final daftar = _parseDaftar(listJson);

        await CacheLokal.simpan(_keyCache, listJson);

        return HasilSertifikat(daftar, false);
      }

      throw KesalahanApi(
        KlienApi.pesanKesalahan(
          data,
          bawaan: 'Gagal mengambil daftar sertifikat.',
        ),
        statusCode: response.statusCode,
      );
    } catch (_) {
      // Offline — coba cache.
      final cached = await CacheLokal.baca(_keyCache);
      if (cached is List && cached.isNotEmpty) {
        return HasilSertifikat(_parseDaftar(cached), true);
      }
      return const HasilSertifikat([], false);
    }
  }

  List<ItemSertifikat> _parseDaftar(List<dynamic> listJson) {
    return listJson.map(
      (item) {
        final m =
            item is Map ? Map<String, dynamic>.from(item) : <String, dynamic>{};
        return ItemSertifikat(
          label: 'Sertifikat',
          judul:
              m['judul']?.toString() ?? m['nama_sertifikat']?.toString() ?? '',
          tanggalTerbit: m['tanggal_terbit']?.toString() ?? '',
          tanggal: m['tanggal_terbit'] != null
              ? DateTime.tryParse(m['tanggal_terbit'].toString()) ??
                  DateTime.now()
              : DateTime.now(),
          urlFile: m['url_file']?.toString(),
        );
      },
    ).toList();
  }
}
