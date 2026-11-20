import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Header maroon melengkung untuk halaman Sertifikat & Portofolio.
class HeaderSertifikat extends StatelessWidget {
  const HeaderSertifikat({super.key});

  @override
  Widget build(BuildContext context) {
    final mediaQuery = MediaQuery.of(context);
    final double tinggiStatusBar = mediaQuery.viewPadding.top;
    final double hp = (mediaQuery.size.width * 0.075).clamp(22.0, 40.0);

    return Material(
      elevation: 4,
      shadowColor: WarnaAplikasi.bayanganLembut,
      color: WarnaAplikasi.utama,
      borderRadius: const BorderRadius.only(
        bottomLeft: Radius.circular(32),
        bottomRight: Radius.circular(32),
      ),
      child: Padding(
        padding: EdgeInsets.only(
          top: tinggiStatusBar + 20,
          bottom: 22,
          left: hp,
          right: hp,
        ),
        child: const Center(
          child: Text(
            'Sertifikat & Portofolio',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: Colors.white,
              fontSize: 20,
              fontWeight: FontWeight.bold,
              letterSpacing: -0.2,
            ),
          ),
        ),
      ),
    );
  }
}
