import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:pretest_posttest_app/fitur/profil/views/pengaturan_akun_view.dart';
import 'package:pretest_posttest_app/fitur/profil/views/profil_view.dart';

void main() {
  testWidgets(
      'ProfilView renders header, menu, and navigates to PengaturanAkunView',
      (WidgetTester tester) async {
    await tester.pumpWidget(
      const MaterialApp(home: ProfilView()),
    );

    expect(find.text('Seto Son Horeg'), findsOneWidget);
    expect(find.text('Kelas 10 TKJ - SMKN 2 Jember'), findsOneWidget);
    expect(find.text('S'), findsOneWidget);
    expect(find.text('Pengaturan akun siswa'), findsOneWidget);
    expect(find.text('Sertifikat & Portofolio'), findsOneWidget);
    expect(find.text('Keluar Akun'), findsOneWidget);

    // Tap menu Pengaturan akun siswa -> navigasi ke PengaturanAkunView
    await tester.tap(find.text('Pengaturan akun siswa'));
    await tester.pumpAndSettle();

    expect(find.byType(PengaturanAkunView), findsOneWidget);
    expect(find.text('NISN'), findsOneWidget);
    expect(find.text('0084729103'), findsOneWidget);
    expect(find.text('Nama Lengkap'), findsOneWidget);
    expect(find.text('Kelas'), findsOneWidget);
    expect(find.text('Jurusan'), findsOneWidget);
    expect(find.text('Password saat ini'), findsOneWidget);
    expect(find.text('Password baru'), findsOneWidget);

    // Scroll sampai tombol terlihat dan tap Simpan Perubahan
    final tombolSimpan = find.text('Simpan Perubahan');
    await tester.ensureVisible(tombolSimpan);
    await tester.pumpAndSettle();
    await tester.tap(tombolSimpan);
    await tester.pump();

    expect(find.text('Perubahan data akun siswa berhasil disimpan!'),
        findsOneWidget);
  });
}
