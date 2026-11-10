import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/widgets/header_autentikasi.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/widgets/ikon_avatar_autentikasi.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/widgets/kolom_teks_autentikasi.dart';
import 'package:pretest_posttest_app/fitur/beranda/views/beranda_view.dart';

/// Halaman Login — entry point pertama setelah aplikasi terbuka.
class LoginView extends StatefulWidget {
  const LoginView({super.key});

  @override
  State<LoginView> createState() => _LoginViewState();
}

class _LoginViewState extends State<LoginView> {
  final _kontrolerNama = TextEditingController();
  final _kontrolerPassword = TextEditingController();

  @override
  void dispose() {
    _kontrolerNama.dispose();
    _kontrolerPassword.dispose();
    super.dispose();
  }

  void _masuk() {
    // TODO: validasi kredensial ke API
    // Sementara langsung masuk ke Beranda
    Navigator.of(context).pushReplacement(
      MaterialPageRoute(
        builder: (_) => const BerandaView(),
      ),
    );
  }

  /// Warna border kartu autentikasi.
  static final Color _warnaKartuBorder =
      WarnaAplikasi.utama.withValues(alpha: 0.18);

  @override
  Widget build(BuildContext context) {
    // Padding horizontal selaras dengan TesView (Pre/Post-Test)
    final double hp =
        (MediaQuery.of(context).size.width * 0.075).clamp(22.0, 40.0);

    return Scaffold(
      backgroundColor: WarnaAplikasi.latarBelakangAutentikasi,
      body: SingleChildScrollView(
        child: Column(
          children: [
            const HeaderAutentikasi(judul: 'Login'),
            const SizedBox(height: 22),
            const IkonAvatarAutentikasi(),
            const SizedBox(height: 22),
            Padding(
              padding: EdgeInsets.symmetric(horizontal: hp),
              child: Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(
                    color: _warnaKartuBorder,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.06),
                      blurRadius: 16,
                      offset: const Offset(0, 6),
                    ),
                  ],
                ),
                padding: const EdgeInsets.fromLTRB(20, 20, 20, 24),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    KolomTeksAutentikasi(
                      label: 'Nama Lengkap',
                      petunjuk: 'Masukkan nama lengkap',
                      kontroler: _kontrolerNama,
                    ),
                    const SizedBox(height: 14),
                    KolomTeksAutentikasi(
                      label: 'Password',
                      petunjuk: 'Masukkan password',
                      rahasia: true,
                      kontroler: _kontrolerPassword,
                    ),
                    const SizedBox(height: 20),
                    Align(
                      alignment: Alignment.centerRight,
                      child: ElevatedButton(
                        onPressed: _masuk,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: WarnaAplikasi.utama,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(
                              horizontal: 36, vertical: 13),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(30),
                          ),
                          elevation: 4,
                        ),
                        child: const Text(
                          'Masuk',
                          style: TextStyle(
                              fontSize: 14, fontWeight: FontWeight.bold),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }
}
