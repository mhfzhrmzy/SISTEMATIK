import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
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
}
