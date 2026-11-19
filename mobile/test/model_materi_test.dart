import 'package:flutter_test/flutter_test.dart';
import 'package:pretest_posttest_app/fitur/materi/models/model_materi.dart';

void main() {
  group('ModelMateri.fromJson', () {
    test('tidak memakai isi_materi sebagai halaman konten', () {
      final materi = ModelMateri.fromJson({
        'id_materi': 3,
        'judul_materi': 'Statistika',
        'isi_materi': 'stat',
        'upload_file': 'http://127.0.0.1:8000/storage/materi/7aVfKZU.pdf',
        'guru_pengajar': 'Budi Santoso',
      });

      expect(materi.deskripsi, 'stat');
      expect(materi.halamanKonten, isEmpty);
      expect(
        materi.urlFile,
        'http://127.0.0.1:8000/storage/materi/7aVfKZU.pdf',
      );
      expect(materi.totalHalaman, 1);
    });

    test('mengabaikan cache lama yang mengisi halaman dengan deskripsi', () {
      final materi = ModelMateri.fromJson({
        'id': 4,
        'judul': 'Microsoft Word',
        'deskripsi': 'ms word',
        'halaman_konten': ['ms word'],
        'upload_file': null,
      });

      expect(materi.deskripsi, 'ms word');
      expect(materi.halamanKonten, isEmpty);
    });

    test('tetap mempertahankan halaman_konten untuk data dummy', () {
      final materi = ModelMateri.fromJson({
        'id': 1,
        'judul': 'Dasar Python',
        'deskripsi': 'Deskripsi singkat.',
        'halaman_konten': ['Halaman 1', 'Halaman 2'],
      });

      expect(materi.halamanKonten, ['Halaman 1', 'Halaman 2']);
      expect(materi.totalHalaman, 2);
    });
  });
}
