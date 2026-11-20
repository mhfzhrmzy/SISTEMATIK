import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/data/api_quiz.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/views/langsung_kerjakan_view.dart';

/// Membuka langsung halaman pengerjaan post-test tanpa halaman daftar perantara.
class PosttestView extends StatelessWidget {
  const PosttestView({
    super.key,
    required this.idMateri,
    this.layananQuiz,
  });

  final int idMateri;
  final LayananQuiz? layananQuiz;

  @override
  Widget build(BuildContext context) {
    return LangsungKerjakanView(
      idMateri: idMateri,
      tipeTest: 'posttest',
      layananQuiz: layananQuiz,
    );
  }
}
