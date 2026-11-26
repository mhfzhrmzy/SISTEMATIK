import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/services/api_quiz.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/screens/langsung_kerjakan_screen.dart';

/// Membuka langsung halaman pengerjaan pre-test tanpa halaman daftar perantara.
class PretestScreen extends StatelessWidget {
  const PretestScreen({
    super.key,
    required this.idMateri,
    this.layananQuiz,
  });

  final int idMateri;
  final LayananQuiz? layananQuiz;

  @override
  Widget build(BuildContext context) {
    return LangsungKerjakanScreen(
      idMateri: idMateri,
      tipeTest: 'pretest',
      layananQuiz: layananQuiz,
    );
  }
}
