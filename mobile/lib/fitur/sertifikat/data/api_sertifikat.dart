import 'dart:typed_data';

import 'package:pretest_posttest_app/inti/layanan/cache_lokal.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/item_sertifikat.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';

class HasilSertifikat {
  const HasilSertifikat(this.daftar, this.dariCache);

  final List<ItemSertifikat> daftar;
  final bool dariCache;
}

class LayananSertifikat {
  LayananSertifikat({KlienApi? api}) : _api = api ?? klienApi;

  final KlienApi _api;
  static const String _keyCache = 'sertifikat_siswa';

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

  Future<HasilSertifikat> ambilDaftarSertifikat() async {
    try {
      final daftarSertifikat = await daftar();
      await CacheLokal.simpan(
        _keyCache,
        daftarSertifikat
            .map(
              (item) => {
                'id_sertifikat': item.id,
                'kategori': item.label,
                'judul_sertifikat': item.judul,
                'tanggal_terbit': item.tanggal.toIso8601String(),
                'file_url': item.urlFile,
              },
            )
            .toList(),
      );
      return HasilSertifikat(daftarSertifikat, false);
    } catch (_) {
      final cached = await CacheLokal.baca(_keyCache);
      if (cached is List && cached.isNotEmpty) {
        return HasilSertifikat(
          cached
              .whereType<Map>()
              .map(
                (item) => ItemSertifikat.fromJson(
                  Map<String, dynamic>.from(item),
                ),
              )
              .toList(),
          true,
        );
      }
      rethrow;
    }
  }

  Future<Uint8List> unduh(int idSertifikat) =>
      _api.getBytes('siswa/sertifikat/$idSertifikat/download');

  Future<ItemSertifikat> unggah({
    required String judul,
    String? penerbit,
    DateTime? tanggalTerbit,
    String? deskripsi,
    required String namaFile,
    required Uint8List bytes,
  }) async {
    final fields = <String, String>{'judul_sertifikat': judul};
    if (penerbit != null && penerbit.isNotEmpty) {
      fields['penerbit'] = penerbit;
    }
    if (tanggalTerbit != null) {
      fields['tanggal_terbit'] =
          '${tanggalTerbit.year.toString().padLeft(4, '0')}-'
              '${tanggalTerbit.month.toString().padLeft(2, '0')}-'
              '${tanggalTerbit.day.toString().padLeft(2, '0')}';
    }
    if (deskripsi != null && deskripsi.isNotEmpty) {
      fields['deskripsi'] = deskripsi;
    }

    final response = await _api.postFile(
      'siswa/sertifikat',
      field: 'file_sertifikat',
      filename: namaFile,
      bytes: bytes,
      fields: fields,
    );

    final data = response['data'] as Map<String, dynamic>?;
    if (data == null) {
      throw const KesalahanApi('Respons upload tidak valid.');
    }

    return ItemSertifikat(
      id: data['id_sertifikat'] is num ? data['id_sertifikat'].toInt() : null,
      label: data['penerbit']?.toString() ?? 'Sertifikat',
      judul: data['judul_sertifikat']?.toString() ?? judul,
      tanggalTerbit:
          'Diterbitkan pada: ${data['tanggal_terbit']?.toString() ?? ''}',
      tanggal: tanggalTerbit ?? DateTime.now(),
      urlFile: data['file_url']?.toString(),
    );
  }

  Future<void> hapus(int idSertifikat) async {
    await _api.delete('siswa/sertifikat/$idSertifikat');
  }
}
