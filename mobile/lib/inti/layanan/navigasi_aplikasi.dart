import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/layanan/sesi_pengguna.dart';

/// Mengatur perpindahan halaman saat sesi berakhir.
///
/// [Aplikasi] mendaftarkan callback navigasi agar lapisan layanan tidak perlu
/// bergantung pada halaman Login.
class NavigasiAplikasi {
  NavigasiAplikasi._();

  static final GlobalKey<NavigatorState> kunci = GlobalKey<NavigatorState>();

  /// Callback yang dipasang oleh root aplikasi.
  static void Function(String? pesan)? saatSesiBerakhir;

  static bool _sedangMemproses = false;

  /// Menghapus sesi lalu mengembalikan pengguna ke halaman login.
  static Future<void> sesiBerakhir({String? pesan}) async {
    if (_sedangMemproses) {
      return;
    }

    _sedangMemproses = true;

    try {
      await SesiPengguna.hapus();
      saatSesiBerakhir?.call(
        pesan ?? 'Sesi Anda telah berakhir. Silakan login kembali.',
      );
    } finally {
      _sedangMemproses = false;
    }
  }
}
