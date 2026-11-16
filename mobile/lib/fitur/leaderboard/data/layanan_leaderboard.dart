import 'package:pretest_posttest_app/inti/layanan/cache_lokal.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/models/model_leaderboard.dart';

/// Hasil respons leaderboard dari API.
class HasilLeaderboard {
  final String? kelas;
  final String? jurusan;
  final int totalSiswa;
  final List<ModelLeaderboard> data;

  /// True jika data diambil dari cache lokal.
  final bool dariCache;

  const HasilLeaderboard({
    this.kelas,
    this.jurusan,
    required this.totalSiswa,
    required this.data,
    this.dariCache = false,
  });
}

/// Layanan komunikasi dengan endpoint leaderboard Laravel.
class LayananLeaderboard {
  const LayananLeaderboard();

  static const String _keyCacheKeseluruhan = 'leaderboard_keseluruhan';
  static String _keyCachePermateri(int idMateri) =>
      'leaderboard_permateri_$idMateri';

  /// Mengambil daftar peringkat siswa sekelas untuk semua materi
  /// (mode "Keseluruhan").
  Future<HasilLeaderboard> ambilLeaderboardKeseluruhan() {
    return _ambilLeaderboard(
      tipeLeaderboard: 'keseluruhan',
      keyCache: _keyCacheKeseluruhan,
    );
  }

  /// Mengambil daftar peringkat siswa sekelas untuk satu materi tertentu
  /// (mode "Permateri").
  Future<HasilLeaderboard> ambilLeaderboardPermateri(int idMateri) {
    return _ambilLeaderboard(
      tipeLeaderboard: 'permateri',
      idMateri: idMateri,
      keyCache: _keyCachePermateri(idMateri),
    );
  }

  Future<HasilLeaderboard> _ambilLeaderboard({
    required String tipeLeaderboard,
    int? idMateri,
    required String keyCache,
  }) async {
    final path = idMateri == null
        ? '/siswa/leaderboard?tipe_leaderboard=$tipeLeaderboard'
        : '/siswa/leaderboard?tipe_leaderboard=$tipeLeaderboard'
            '&materi_id=$idMateri';

    try {
      final response = await KlienApi.get(path);
      final data = KlienApi.bacaJson(response);

      if (response.statusCode == 200 && data['data'] is List) {
        final listJson = data['data'] as List<dynamic>;
        final daftar = listJson
            .map((item) =>
                ModelLeaderboard.fromJson(item as Map<String, dynamic>))
            .toList();

        // Simpan ke cache untuk offline.
        await CacheLokal.simpan(keyCache, listJson);

        return HasilLeaderboard(
          kelas: data['kelas']?.toString(),
          jurusan: data['jurusan']?.toString(),
          totalSiswa: data['total_siswa'] is num
              ? data['total_siswa'].toInt()
              : listJson.length,
          data: daftar,
        );
      }

      // Status code tidak 200 — lanjut ke catch
      throw KesalahanApi(
        KlienApi.pesanKesalahan(
          data,
          bawaan: 'Gagal mengambil data leaderboard.',
        ),
        statusCode: response.statusCode,
      );
    } catch (e) {
      // Offline atau API gagal — coba cache.
      if (e is KesalahanApi && e.statusCode == 401) {
        rethrow; // Sesi berakhir, jangan pakai cache.
      }

      final cached = await CacheLokal.baca(keyCache);
      if (cached is List && cached.isNotEmpty) {
        final daftar = cached
            .map((item) => ModelLeaderboard.fromJson(
                Map<String, dynamic>.from(item as Map)))
            .toList();

        return HasilLeaderboard(
          totalSiswa: daftar.length,
          data: daftar,
          dariCache: true,
        );
      }

      rethrow;
    }
  }
}
