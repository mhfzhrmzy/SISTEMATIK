import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/aplikasi/tema_aplikasi.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/views/login_view.dart';

/// Widget root aplikasi Dasar Python.
class Aplikasi extends StatelessWidget {
  const Aplikasi({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Dasar Python',
      debugShowCheckedModeBanner: false,
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
      // Entry point langsung ke halaman Login
      home: const LoginView(),
    );
  }
}
