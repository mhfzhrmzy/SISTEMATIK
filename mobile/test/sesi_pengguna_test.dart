import 'package:flutter_test/flutter_test.dart';
import 'package:pretest_posttest_app/inti/layanan/sesi_pengguna.dart';

void main() {
  tearDown(() async {
    await SesiPengguna.hapus();
  });

  test('sesi yang baru disimpan dianggap sudah masuk', () async {
    await SesiPengguna.simpan(
      token: 'token-tes',
      pengguna: const {
        'nisn': '0051234561',
        'nama_lengkap': 'Andi Pratama',
        'kelas': '10 1',
        'jurusan': 'Teknik Komputer & Jaringan',
      },
      kedaluwarsa: DateTime.now().add(const Duration(days: 30)),
    );

    expect(SesiPengguna.sudahMasuk, isTrue);
    expect(SesiPengguna.kedaluwarsa, isFalse);
    expect(SesiPengguna.nama, 'Andi Pratama');
    expect(
      SesiPengguna.kelas,
      'Kelas 10 1 - Teknik Komputer & Jaringan',
    );
  });

  test('sesi yang sudah melewati waktu kedaluwarsa terdeteksi', () async {
    await SesiPengguna.simpan(
      token: 'token-tes',
      pengguna: const {
        'nisn': '0051234561',
        'nama_lengkap': 'Andi Pratama',
      },
      kedaluwarsa: DateTime.now().subtract(const Duration(minutes: 1)),
    );

    expect(SesiPengguna.sudahMasuk, isTrue);
    expect(SesiPengguna.kedaluwarsa, isTrue);
  });

  test('hapus sesi membersihkan token dan data pengguna', () async {
    await SesiPengguna.simpan(
      token: 'token-tes',
      pengguna: const {
        'nisn': '0051234561',
        'nama_lengkap': 'Andi Pratama',
      },
      kedaluwarsa: DateTime.now().add(const Duration(days: 30)),
    );

    await SesiPengguna.hapus();

    expect(SesiPengguna.sudahMasuk, isFalse);
    expect(SesiPengguna.token, isNull);
    expect(SesiPengguna.nama, isNull);
    expect(SesiPengguna.kedaluwarsa, isFalse);
  });
}
