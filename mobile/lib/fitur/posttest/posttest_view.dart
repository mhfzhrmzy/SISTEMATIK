import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/data/layanan_quiz.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/views/pemilih_quiz_view.dart';

/// Mengambil dan menampilkan post-test yang tersedia untuk materi.
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
    return PemilihQuizView(
      idMateri: idMateri,
      tipeTest: 'posttest',
      layananQuiz: layananQuiz,
    );
  }
}
