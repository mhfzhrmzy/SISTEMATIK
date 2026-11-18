import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Palet warna & style bersama untuk semua widget modul sertifikat.
abstract final class TemaSertifikat {
  static const Color maroon = WarnaAplikasi.utama;
  static const Color maroonTerang = Color(0xFFF6EEF1);
  static const Color maroonBorder = Color(0xFFD4A0B0);

  static BoxDecoration kotak({
    Color warnaLatar = Colors.white,
    Color warnaGaris = WarnaAplikasi.utama,
    double ketebalanGaris = 1.2,
    double radius = 12,
    List<BoxShadow> bayangan = const [],
  }) =>
      BoxDecoration(
        color: warnaLatar,
        borderRadius: BorderRadius.circular(radius),
        border: Border.all(color: warnaGaris, width: ketebalanGaris),
        boxShadow: bayangan,
      );

  /// Style tombol utama (maroon, radius 12, tebal vertikal 13).
  static ButtonStyle tombolUtama({double radius = 12}) =>
      ElevatedButton.styleFrom(
        backgroundColor: maroon,
        foregroundColor: Colors.white,
        elevation: 0,
        padding: const EdgeInsets.symmetric(vertical: 13),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(radius)),
      );

  /// Style tombol outline (garis maroon).
  static ButtonStyle tombolGaris({double ketebalan = 1.5}) =>
      OutlinedButton.styleFrom(
        foregroundColor: maroon,
        side: const BorderSide(color: maroon, width: 1.5),
        padding: const EdgeInsets.symmetric(vertical: 13),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
      );

  /// Dekorasi input kolom teks dengan tema maroon.
  static InputDecoration dekorasiInput(String petunjuk) {
    OutlineInputBorder buatBorder(Color warna, double lebar) =>
        OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: warna, width: lebar),
        );
    return InputDecoration(
      hintText: petunjuk,
      hintStyle: TextStyle(
        color: Colors.grey.shade400,
        fontSize: 13,
        fontWeight: FontWeight.normal,
      ),
      filled: true,
      fillColor: maroonTerang,
      contentPadding:
          const EdgeInsets.symmetric(horizontal: 14, vertical: 13),
      border: buatBorder(maroonBorder, 1.2),
      enabledBorder: buatBorder(maroonBorder, 1.2),
      focusedBorder: buatBorder(maroon, 1.8),
      errorBorder: buatBorder(Colors.red.shade400, 1.5),
      focusedErrorBorder: buatBorder(Colors.red.shade400, 1.8),
      errorStyle: const TextStyle(fontSize: 11),
    );
  }
}
