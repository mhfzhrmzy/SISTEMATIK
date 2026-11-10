import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/views/tes_view.dart';
import 'posttest_data.dart';

/// Halaman Post-Test — membungkus [TesView] dengan data [daftarSoalPosttest].
class PosttestView extends StatelessWidget {
  const PosttestView({super.key});

  @override
  Widget build(BuildContext context) {
    return TesView(
      judul: 'Post-Test',
      daftarSoal: daftarSoalPosttest,
    );
  }
}
