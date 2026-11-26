import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/services/api_autentikasi.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/widgets/header_autentikasi.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/widgets/ikon_avatar_autentikasi.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/widgets/kolom_teks_autentikasi.dart';
import 'package:pretest_posttest_app/fitur/beranda/screens/beranda_screen.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';

/// Halaman Login — entry point pertama setelah aplikasi terbuka.
class LoginScreen extends StatefulWidget {
  /// Pesan yang langsung ditampilkan, misalnya saat sesi kedaluwarsa.
  final String? pesanAwal;

  const LoginScreen({super.key, this.pesanAwal});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _kontrolerNisn = TextEditingController();
  final _kontrolerPassword = TextEditingController();

  bool _sedangMemuat = false;
  String? _pesanGalat;

  @override
  void initState() {
    super.initState();
    _pesanGalat = widget.pesanAwal;
  }

  @override
  void dispose() {
    _kontrolerNisn.dispose();
    _kontrolerPassword.dispose();
    super.dispose();
  }

  /// Mengirim NISN dan password ke API Laravel.
  Future<void> _masuk() async {
    FocusScope.of(context).unfocus();

    final nisn = _kontrolerNisn.text.trim();
    final password = _kontrolerPassword.text;

    if (nisn.length != 10) {
      setState(() => _pesanGalat = 'NISN harus terdiri dari 10 digit.');
      return;
    }

    if (password.isEmpty) {
      setState(() => _pesanGalat = 'Password wajib diisi.');
      return;
    }

    setState(() {
      _sedangMemuat = true;
      _pesanGalat = null;
    });

    try {
      final hasil = await const LayananAutentikasi().masukSiswa(
        nisn: nisn,
        password: password,
      );

      if (!mounted) return;

      setState(() => _sedangMemuat = false);
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(
          builder: (_) => BerandaScreen(
            nama: hasil.pengguna.namaLengkap,
            kelas: hasil.pengguna.labelKelas,
          ),
        ),
      );
    } on KesalahanApi catch (kesalahan) {
      if (!mounted) return;

      setState(() {
        _sedangMemuat = false;
        _pesanGalat = kesalahan.pesan;
      });
    } catch (_) {
      if (!mounted) return;

      setState(() {
        _sedangMemuat = false;
        _pesanGalat = 'Terjadi kesalahan. Silakan coba lagi.';
      });
    }
  }

  /// Warna border kartu autentikasi.
  static final Color _warnaKartuBorder =
      WarnaAplikasi.utama.withValues(alpha: 0.18);

  @override
  Widget build(BuildContext context) {
    // Padding horizontal selaras dengan TesScreen (Pre/Post-Test)
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
                      label: 'NISN',
                      petunjuk: 'Masukkan 10 digit NISN',
                      kontroler: _kontrolerNisn,
                      tipeKeyboard: TextInputType.number,
                    ),
                    const SizedBox(height: 14),
                    KolomTeksAutentikasi(
                      label: 'Password',
                      petunjuk: 'Masukkan password',
                      rahasia: true,
                      kontroler: _kontrolerPassword,
                    ),
                    if (_pesanGalat != null) ...[
                      const SizedBox(height: 14),
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Theme.of(context).colorScheme.errorContainer,
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Text(
                          _pesanGalat!,
                          style: TextStyle(
                            color:
                                Theme.of(context).colorScheme.onErrorContainer,
                            fontSize: 12,
                          ),
                        ),
                      ),
                    ],
                    const SizedBox(height: 20),
                    Align(
                      alignment: Alignment.centerRight,
                      child: ElevatedButton(
                        onPressed: _sedangMemuat ? null : _masuk,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: WarnaAplikasi.utama,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(
                            horizontal: 36,
                            vertical: 13,
                          ),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(30),
                          ),
                          elevation: 4,
                        ),
                        child: _sedangMemuat
                            ? const SizedBox(
                                width: 18,
                                height: 18,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                  color: Colors.white,
                                ),
                              )
                            : const Text(
                                'Masuk',
                                style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.bold,
                                ),
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
