import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/services/api_quiz.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/screens/langsung_kerjakan_screen.dart';

/// Membuka langsung halaman pengerjaan post-test tanpa halaman daftar perantara.
class PosttestScreen extends StatelessWidget {
  const PosttestScreen({
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
      tipeTest: 'posttest',
      layananQuiz: layananQuiz,
    );
  }
}
