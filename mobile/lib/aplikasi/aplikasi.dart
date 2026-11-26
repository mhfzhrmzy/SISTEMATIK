import 'dart:async';

import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/aplikasi/tema_aplikasi.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/navigasi_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/sesi_pengguna.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/screens/gerbang_sesi_screen.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/screens/login_screen.dart';

/// Widget root aplikasi Dasar Python.
class Aplikasi extends StatefulWidget {
  const Aplikasi({super.key});

  @override
  State<Aplikasi> createState() => _AplikasiState();
}

class _AplikasiState extends State<Aplikasi> with WidgetsBindingObserver {
  @override
  void initState() {
    super.initState();
    NavigasiAplikasi.saatSesiBerakhir = _bukaLogin;
    WidgetsBinding.instance.addObserver(this);
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    NavigasiAplikasi.saatSesiBerakhir = null;
    super.dispose();
  }

  /// Dipanggil saat API menjawab 401 atau token sudah kedaluwarsa.
  void _bukaLogin(String? pesan) {
    final navigator = NavigasiAplikasi.kunci.currentState;

    if (navigator == null) {
      return;
    }

    navigator.pushAndRemoveUntil(
      MaterialPageRoute(builder: (_) => LoginScreen(pesanAwal: pesan)),
      (route) => false,
    );
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state != AppLifecycleState.resumed) {
      return;
    }

    if (SesiPengguna.sudahMasuk && SesiPengguna.kedaluwarsa) {
      unawaited(NavigasiAplikasi.sesiBerakhir());
    }
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'SISTEMATIK',
      debugShowCheckedModeBanner: false,
      navigatorKey: NavigasiAplikasi.kunci,
      theme: ThemeData(
        useMaterial3: true,
        colorSchemeSeed: WarnaAplikasi.utama,
        scaffoldBackgroundColor: WarnaAplikasi.latarBelakang,
        fontFamily: 'PlusJakartaSans',
        pageTransitionsTheme: const PageTransitionsTheme(
          builders: {
            TargetPlatform.android: TanpaTransisiBuilder(),
            TargetPlatform.iOS: TanpaTransisiBuilder(),
            TargetPlatform.windows: TanpaTransisiBuilder(),
            TargetPlatform.macOS: TanpaTransisiBuilder(),
            TargetPlatform.linux: TanpaTransisiBuilder(),
            TargetPlatform.fuchsia: TanpaTransisiBuilder(),
          },
        ),
      ),
      // Tentukan halaman awal dari sesi yang tersimpan.
      home: const GerbangSesiScreen(),
    );
  }
}
