import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Header maroon melengkung untuk halaman tes (Pre-Test, Post-Test, Materi).
///
/// Full-bleed hingga status bar — ikon jam, baterai, wifi berwarna putih
/// agar kontras di atas warna merah maroon. Judul ditempatkan di tengah
/// dengan jarak aman di bawah ikon status bar.
class HeaderTes extends StatelessWidget {
  final String judul;

  const HeaderTes({super.key, required this.judul});

  @override
  Widget build(BuildContext context) {
    final mediaQuery = MediaQuery.of(context);
    final double tinggiStatusBar = mediaQuery.viewPadding.top;
    final double lebar = mediaQuery.size.width;
    final double paddingHorizontal = lebar > 600 ? 32 : 20;

    return AnnotatedRegion<SystemUiOverlayStyle>(
      // Ikon putih terang agar terbaca jelas di atas latar maroon gelap
      // di belakang status bar.
      value: SystemUiOverlayStyle.light.copyWith(
        statusBarColor: Colors.transparent,
      ),
      child: Material(
        elevation: 6,
        shadowColor: WarnaAplikasi.bayanganLembut,
        color: WarnaAplikasi.utama,
        borderRadius: const BorderRadius.only(
          bottomLeft: Radius.circular(36),
          bottomRight: Radius.circular(36),
        ),
        child: Padding(
          padding: EdgeInsets.only(
            // Bersihkan baris status bar, lalu dorong judul lebih ke bawah
            // agar ada ruang bernafas yang cukup di bawah ikon.
            top: tinggiStatusBar + 36,
            bottom: 32,
            left: paddingHorizontal,
            right: paddingHorizontal,
          ),
          child: Center(
            child: Text(
              judul,
              textAlign: TextAlign.center,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 20,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),
      ),
    );
  }
}
