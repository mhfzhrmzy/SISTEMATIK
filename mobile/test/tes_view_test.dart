import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/data/layanan_quiz.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/views/hasil_quiz_view.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/views/tes_view.dart';

void main() {
  testWidgets('TesView handles quizzes without questions', (
    WidgetTester tester,
  ) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: TesView(
          judul: 'Pre-Test',
          daftarSoal: [],
        ),
      ),
    );

    expect(find.text('Quiz ini belum memiliki soal.'), findsOneWidget);
    expect(tester.takeException(), isNull);
  });

  testWidgets('HasilQuizView renders stats accurately and excludes rata-rata waktu', (
    WidgetTester tester,
  ) async {
    const hasil = HasilQuiz(
      benar: 14,
      total: 20,
      totalPoin: 130,
    );

    await tester.pumpWidget(
      const MaterialApp(
        home: HasilQuizView(
          judulMateri: 'Dasar Python',
          tipeTest: 'posttest',
          hasil: hasil,
        ),
      ),
    );

    expect(find.text('Hasil Post-Test Dasar Python'), findsOneWidget);
    expect(find.text('Dasar Python'), findsOneWidget);

    expect(find.text('Total soal'), findsOneWidget);
    expect(find.text('20'), findsOneWidget);

    expect(find.text('Salah'), findsOneWidget);
    expect(find.text('6'), findsOneWidget);

    expect(find.text('Benar'), findsOneWidget);
    expect(find.text('14'), findsOneWidget);

    expect(find.text('Poin'), findsOneWidget);
    expect(find.text('130'), findsOneWidget);

    expect(find.text('Kembali ke Beranda'), findsOneWidget);

    // Memastikan kolom rata rata waktu tidak ada
    expect(find.textContaining('Rata rata waktu', findRichText: true), findsNothing);
    expect(find.textContaining('rata rata', findRichText: true), findsNothing);
  });
}
