import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/data/api_quiz.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/models/soal.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/views/hasil_quiz_view.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/views/tes_view.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/baris_timer_audio.dart';

const _soalContoh = Soal(
  idSoal: 1,
  pertanyaan: 'Apa itu variabel?',
  pilihan: ['A', 'B', 'C', 'D'],
);

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

  testWidgets('Pre-Test tidak menampilkan timer (mengikuti Laravel)', (
    WidgetTester tester,
  ) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: TesView(
          judul: 'Pre-Test',
          tipeTest: 'pretest',
          daftarSoal: [_soalContoh],
          timerMenit: 30,
        ),
      ),
    );

    expect(find.byType(BarisTimerAudio), findsOneWidget);
    expect(find.text('sisa'), findsNothing);
    expect(find.textContaining('30 Menit'), findsNothing);

    await tester.pumpWidget(const SizedBox());
  });

  testWidgets('Timer post-test menghitung mundur dari sisaDetik server', (
    WidgetTester tester,
  ) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: TesView(
          judul: 'Post-Test',
          tipeTest: 'posttest',
          daftarSoal: [_soalContoh],
          timerMenit: 30,
          sisaDetik: 65,
        ),
      ),
    );

    expect(find.text('00:01:05'), findsOneWidget);
    expect(find.text('sisa'), findsOneWidget);

    await tester.pump(const Duration(seconds: 1));
    expect(find.text('00:01:04'), findsOneWidget);

    await tester.pump(const Duration(seconds: 1));
    expect(find.text('00:01:03'), findsOneWidget);

    await tester.pumpWidget(const SizedBox());
  });

  testWidgets('Waktu habis mengunci jawaban dan mengirim otomatis', (
    WidgetTester tester,
  ) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: TesView(
          judul: 'Post-Test',
          tipeTest: 'posttest',
          daftarSoal: [_soalContoh],
          timerMenit: 30,
          sisaDetik: 1,
        ),
      ),
    );

    expect(find.text('00:00:01'), findsOneWidget);

    await tester.pump(const Duration(seconds: 1));
    expect(find.text('00:00:00'), findsOneWidget);
    expect(find.text('Waktu Habis'), findsOneWidget);

    // Menunggu jeda 500 ms sebelum pengiriman otomatis dijalankan.
    await tester.pump(const Duration(milliseconds: 600));
    expect(find.text('Waktu Habis'), findsOneWidget);

    await tester.pumpWidget(const SizedBox());
  });

  testWidgets(
      'HasilQuizView renders stats accurately and excludes rata-rata waktu', (
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
    expect(find.textContaining('Rata rata waktu', findRichText: true),
        findsNothing);
    expect(find.textContaining('rata rata', findRichText: true), findsNothing);
  });
}
