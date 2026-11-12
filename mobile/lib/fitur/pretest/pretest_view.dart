import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/views/tes_view.dart';
import 'pretest_data.dart';

/// Halaman Pre-Test — membungkus [TesView] dengan data [daftarSoalPretest].
class PretestView extends StatelessWidget {
  const PretestView({super.key});

  @override
  Widget build(BuildContext context) {
    return TesView(
      judul: 'Pre-Test',
      daftarSoal: daftarSoalPretest,
    );
  }
}
