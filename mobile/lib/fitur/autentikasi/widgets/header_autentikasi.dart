import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Header maroon melengkung untuk halaman Login & Register.
class HeaderAutentikasi extends StatelessWidget {
  final String judul;
  const HeaderAutentikasi({super.key, required this.judul});

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 200,
      decoration: const BoxDecoration(
        color: WarnaAplikasi.utama,
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(40),
          bottomRight: Radius.circular(40),
        ),
      ),
      child: Center(
        child: Padding(
          padding: const EdgeInsets.only(bottom: 10),
          child: Text(
            judul,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 34,
              fontWeight: FontWeight.bold,
              letterSpacing: 1.2,
            ),
          ),
        ),
      ),
    );
  }
}
