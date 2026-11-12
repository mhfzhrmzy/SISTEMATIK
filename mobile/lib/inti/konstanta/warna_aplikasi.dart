import 'package:flutter/material.dart';

/// Palet warna terpusat untuk seluruh aplikasi Dasar Python.
///
/// Gabungan dari palette tes (Pre-Test / Post-Test) dan palette
/// autentikasi (Login / Register). Semua komponen mengacu ke sini
/// agar warna konsisten di seluruh aplikasi.
class WarnaAplikasi {
  WarnaAplikasi._();

  /// Warna utama merah maroon untuk header, tombol submit, dan teks.
  static const Color utama = Color(0xFF7D1238);

  /// Variasi gelap untuk bayangan / pressed state.
  static const Color utamaGelap = Color(0xFF5E0D2A);

  /// Abu-abu hangat untuk menandai opsi jawaban yang dipilih.
  static const Color terpilih = Color(0xFFE8E4E6);

  /// Warna border pada kartu jawaban yang dipilih.
  static const Color borderTerpilih = Color(0xFFB8A8AF);

  /// Warna border untuk kartu jawaban / kartu soal yang belum dipilih.
  static const Color border = Color(0xFFEDE1E5);

  /// Latar belakang halaman utama (putih).
  static const Color latarBelakang = Colors.white;

  /// Latar belakang halaman autentikasi (Login & Register).
  /// Berbeda dari [latarBelakang] agar tampilan login tetap sama.
  static const Color latarBelakangAutentikasi = Color(0xFFF5F5F5);

  /// Versi redup dari warna utama, digunakan untuk tombol Submit
  /// dan kontrol Back/Next saat tidak aktif.
  static Color utamaRedup = utama.withValues(alpha: 0.35);

  /// Warna bayangan lembut yang dipakai di header, kartu, dan tombol.
  static Color bayanganLembut = Colors.black.withValues(alpha: 0.08);
}
