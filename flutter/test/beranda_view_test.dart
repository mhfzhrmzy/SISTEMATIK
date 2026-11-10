import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:pretest_posttest_app/fitur/beranda/views/beranda_view.dart';

void main() {
  testWidgets('BerandaView renders header and status bar style properly',
      (WidgetTester tester) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: BerandaView(),
      ),
    );

    // Verifikasi teks di header profil
    expect(find.text('Halo, Seto 👋'), findsOneWidget);
    expect(find.text('Seto Son Horeg'), findsOneWidget);
    expect(find.text('Kelas 10 TKJ - SMKN 2 Jember'), findsOneWidget);

    // Verifikasi card statistik
    expect(find.text('Materi'), findsOneWidget);
    expect(find.text('Total Quiz'), findsOneWidget);

    // Verifikasi section materi
    expect(find.text('Materi Tersedia'), findsOneWidget);
  });
}
