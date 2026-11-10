import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Tombol Submit lebar penuh.
///
/// Aktif (maroon, bisa di-tap) hanya ketika [aktif] bernilai true;
/// jika tidak, ditampilkan dalam warna redup dan tap diabaikan.
class TombolKirim extends StatelessWidget {
  final bool aktif;
  final VoidCallback saatDitekan;

  const TombolKirim({
    super.key,
    required this.aktif,
    required this.saatDitekan,
  });

  @override
  Widget build(BuildContext context) {
    final Color warnaLatar =
        aktif ? WarnaAplikasi.utama : WarnaAplikasi.utamaRedup;

    return SizedBox(
      width: double.infinity,
      height: 50,
      child: Material(
        color: warnaLatar,
        borderRadius: BorderRadius.circular(14),
        elevation: aktif ? 4 : 0,
        shadowColor: WarnaAplikasi.bayanganLembut,
        child: InkWell(
          borderRadius: BorderRadius.circular(14),
          onTap: aktif ? saatDitekan : null,
          child: const Center(
            child: Text(
              'Submit',
              style: TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.bold,
                fontSize: 16,
                letterSpacing: 0.3,
              ),
            ),
          ),
        ),
      ),
    );
  }
}
