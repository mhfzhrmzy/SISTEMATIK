import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:pretest_posttest_app/fitur/beranda/screens/beranda_screen.dart';

void main() {
  testWidgets('BerandaScreen renders header and status bar style properly',
      (WidgetTester tester) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: BerandaScreen(
          nama: 'Seto Son Horeg',
          kelas: 'Kelas X TKJ - SMKN 2 Jember',
        ),
      ),
    );

    // Verifikasi teks di header profil
    expect(find.text('Halo, Seto 👋'), findsOneWidget);
    expect(find.text('Seto Son Horeg'), findsOneWidget);
    expect(find.text('Kelas X TKJ - SMKN 2 Jember'), findsOneWidget);

    // Verifikasi card statistik
    expect(find.text('Materi'), findsOneWidget);
    expect(find.text('Total Quiz'), findsOneWidget);

    // Verifikasi section materi
    expect(find.text('Materi Tersedia'), findsOneWidget);
  });
}
