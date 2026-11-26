import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/models/pengguna_siswa.dart';
import 'package:pretest_posttest_app/fitur/profil/services/api_profil.dart';
import 'package:pretest_posttest_app/fitur/profil/screens/pengaturan_akun_screen.dart';
import 'package:pretest_posttest_app/fitur/profil/screens/profil_screen.dart';

class _LayananProfilPalsu extends LayananProfil {
  @override
  Future<PenggunaSiswa> ambil() async => _pengguna('Seto Son Horeg');

  @override
  Future<void> ubahPassword({
    required String lama,
    required String baru,
  }) async {}

  PenggunaSiswa _pengguna(String nama) => PenggunaSiswa(
        nisn: '0084729103',
        namaLengkap: nama,
        kelas: 'X-1',
        jurusan: 'TKJ',
      );
}

void main() {
  testWidgets(
      'ProfilScreen renders header, menu, and navigates to PengaturanAkunScreen',
      (WidgetTester tester) async {
    await tester.pumpWidget(
      MaterialApp(
        home: ProfilScreen(
          nama: 'Seto Son Horeg',
          kelas: 'Kelas X TKJ - SMKN 2 Jember',
          layananProfil: _LayananProfilPalsu(),
        ),
      ),
    );

    expect(find.text('Seto Son Horeg'), findsOneWidget);
    expect(find.text('Kelas X TKJ - SMKN 2 Jember'), findsOneWidget);
    expect(find.text('S'), findsOneWidget);
    expect(find.text('Pengaturan akun siswa'), findsOneWidget);
    expect(find.text('Sertifikat & Portofolio'), findsOneWidget);
    expect(find.text('Keluar Akun'), findsOneWidget);

    // Tap menu Pengaturan akun siswa -> navigasi ke PengaturanAkunScreen
    await tester.tap(find.text('Pengaturan akun siswa'));
    await tester.pumpAndSettle();

    expect(find.byType(PengaturanAkunScreen), findsOneWidget);
    expect(find.text('NISN'), findsOneWidget);
    expect(find.text('0084729103'), findsOneWidget);
    expect(find.text('Nama Lengkap'), findsOneWidget);
    expect(find.text('Kelas'), findsOneWidget);
    expect(find.text('Jurusan'), findsOneWidget);
    expect(find.text('Password saat ini'), findsOneWidget);
    expect(find.text('Password baru'), findsOneWidget);

    // Scroll sampai tombol terlihat dan tap Simpan Perubahan tanpa password
    final tombolSimpan = find.text('Simpan Perubahan');
    await tester.ensureVisible(tombolSimpan);
    await tester.pumpAndSettle();
    await tester.tap(tombolSimpan);
    await tester.pump();

    expect(
      find.text(
          'Masukkan password saat ini dan password baru untuk mengubah kata sandi.'),
      findsOneWidget,
    );

    // Isi password saat ini dan baru
    final passwordFields = find.byType(TextFormField);
    await tester.enterText(passwordFields.at(4), 'passwordLama');
    await tester.enterText(passwordFields.at(5), 'passwordBaru123');
    await tester.pump();

    await tester.tap(tombolSimpan);
    await tester.pump();
    await tester.pumpAndSettle();

    expect(find.text('Password berhasil diubah.'), findsOneWidget);
  });
}
