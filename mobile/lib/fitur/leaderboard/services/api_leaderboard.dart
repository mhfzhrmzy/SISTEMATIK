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
///
/// Mendukung paginasi: batch pertama (limit=10) untuk Top 3 + baris 4–10,
/// batch berikutnya (offset=10,20,...) untuk siswa di peringkat 11+.
class LayananLeaderboard {
  const LayananLeaderboard();

  static const String _keyCacheKeseluruhan = 'leaderboard_keseluruhan';
  static String _keyCachePermateri(int idMateri) =>
      'leaderboard_permateri_$idMateri';

  static const int _batasHalaman = 10;

  /// Mengambil daftar peringkat siswa sekelas untuk semua materi
  /// (mode "Keseluruhan").
  Future<HasilLeaderboard> ambilLeaderboardKeseluruhan({
    int offset = 0,
  }) {
    return _ambilLeaderboard(
      tipeLeaderboard: 'keseluruhan',
      offset: offset,
      keyCache: _keyCacheKeseluruhan,
    );
  }

  /// Mengambil daftar peringkat siswa sekelas untuk satu materi tertentu
  /// (mode "Permateri").
  Future<HasilLeaderboard> ambilLeaderboardPermateri(
    int idMateri, {
    int offset = 0,
  }) {
    return _ambilLeaderboard(
      tipeLeaderboard: 'permateri',
      idMateri: idMateri,
      offset: offset,
      keyCache: _keyCachePermateri(idMateri),
    );
  }

  Future<HasilLeaderboard> _ambilLeaderboard({
    required String tipeLeaderboard,
    int? idMateri,
    required int offset,
    required String keyCache,
  }) async {
    final basePath = idMateri == null
        ? '/siswa/leaderboard'
        : '/siswa/leaderboard?materi_id=$idMateri';
    final path = '$basePath?tipe_leaderboard=$tipeLeaderboard'
        '&limit=$_batasHalaman&offset=$offset';

    try {
      final response = await KlienApi.get(path);
      final data = KlienApi.bacaJson(response);

      if (response.statusCode == 200 && data['data'] is List) {
        final listJson = data['data'] as List<dynamic>;
        final daftar = listJson
            .map((item) =>
                ModelLeaderboard.fromJson(item as Map<String, dynamic>))
            .toList();

        // Simpan ke cache untuk offline (hanya batch pertama).
        if (offset == 0) {
          await CacheLokal.simpan(keyCache, listJson);
        }

        return HasilLeaderboard(
          kelas: data['kelas']?.toString(),
          jurusan: data['jurusan']?.toString(),
          totalSiswa: data['total_siswa'] is num
              ? data['total_siswa'].toInt()
              : (offset + daftar.length),
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
      // Offline atau API gagal — coba cache (hanya batch pertama).
      if (e is KesalahanApi && e.statusCode == 401) {
        rethrow; // Sesi berakhir, jangan pakai cache.
      }
      if (offset != 0) {
        rethrow; // Batch lanjutan tidak ada cache.
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
