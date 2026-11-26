import 'dart:typed_data';

import 'package:flutter_test/flutter_test.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/jenis_file_sertifikat.dart';

void main() {
  group('deteksiJenisFile', () {
    test('mengenali PDF dari magic bytes', () {
      final bytes = Uint8List.fromList([0x25, 0x50, 0x44, 0x46, 0x2D]);

      expect(deteksiJenisFile(bytes), JenisFileSertifikat.pdf);
    });

    test('mengenali PNG dari magic bytes', () {
      final bytes = Uint8List.fromList(
        [0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A, 0x00],
      );

      expect(deteksiJenisFile(bytes), JenisFileSertifikat.png);
      expect(deteksiJenisFile(bytes).adalahGambar, isTrue);
    });

    test('mengenali JPEG dari magic bytes', () {
      final bytes = Uint8List.fromList([0xFF, 0xD8, 0xFF, 0xE0]);

      expect(deteksiJenisFile(bytes), JenisFileSertifikat.jpeg);
      expect(deteksiJenisFile(bytes).adalahGambar, isTrue);
    });

    test('memakai ekstensi saat magic bytes tidak dikenali', () {
      final bytes = Uint8List.fromList([0x00, 0x01, 0x02]);

      expect(
        deteksiJenisFile(bytes, namaFile: 'sertifikat.PDF'),
        JenisFileSertifikat.pdf,
      );
      expect(
        deteksiJenisFile(bytes, namaFile: 'sertifikat.jpeg'),
        JenisFileSertifikat.jpeg,
      );
    });

    test('menolak file yang tidak didukung', () {
      final bytes = Uint8List.fromList([0x00, 0x01, 0x02]);

      final jenis = deteksiJenisFile(bytes, namaFile: 'catatan.txt');

      expect(jenis, JenisFileSertifikat.tidakDiketahui);
      expect(jenis.didukung, isFalse);
      expect(jenis.ekstensi, 'bin');
    });
  });

  group('jenisDariNama', () {
    test('mengenali ekstensi jpg dan jpeg sebagai gambar', () {
      expect(jenisDariNama('a.jpg'), JenisFileSertifikat.jpeg);
      expect(jenisDariNama('a.jpeg'), JenisFileSertifikat.jpeg);
      expect(jenisDariNama('a.png'), JenisFileSertifikat.png);
      expect(jenisDariNama('a.pdf'), JenisFileSertifikat.pdf);
    });

    test('mengembalikan tidakDiketahui untuk nama kosong', () {
      expect(jenisDariNama(null), JenisFileSertifikat.tidakDiketahui);
      expect(jenisDariNama(''), JenisFileSertifikat.tidakDiketahui);
    });
  });
}
