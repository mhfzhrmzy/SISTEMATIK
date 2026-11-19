import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/sesi_pengguna.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/data/layanan_autentikasi.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/views/login_view.dart';
import 'package:pretest_posttest_app/fitur/beranda/views/beranda_view.dart';

enum _StatusSesi { memuat, masuk, login, galat }

/// Menentukan halaman awal berdasarkan sesi yang tersimpan.
///
/// Alur:
/// 1. Baca token dari secure storage.
/// 2. Jika tidak ada atau kedaluwarsa, tampilkan Login.
/// 3. Jika ada, validasi ke `GET /api/siswa/profil`.
/// 4. Jika valid, masuk Beranda. Jika 401, kembali ke Login.
class GerbangSesiView extends StatefulWidget {
  const GerbangSesiView({super.key});

  @override
  State<GerbangSesiView> createState() => _GerbangSesiViewState();
}

class _GerbangSesiViewState extends State<GerbangSesiView> {
  _StatusSesi _status = _StatusSesi.memuat;
  String? _pesan;

  @override
  void initState() {
    super.initState();
    _periksaSesi();
  }

  Future<void> _periksaSesi() async {
    setState(() {
      _status = _StatusSesi.memuat;
      _pesan = null;
    });

    final sesiKedaluwarsa = await SesiPengguna.muat();

    if (!mounted) return;

    if (!SesiPengguna.sudahMasuk) {
      setState(() {
        _status = _StatusSesi.login;
        _pesan = sesiKedaluwarsa
            ? 'Sesi Anda telah berakhir. Silakan login kembali.'
            : null;
      });
      return;
    }

    try {
      await const LayananAutentikasi().ambilProfil();

      if (!mounted) return;
      setState(() => _status = _StatusSesi.masuk);
    } on KesalahanApi catch (kesalahan) {
      if (!mounted) return;

      if (kesalahan.statusCode == 401) {
        await SesiPengguna.hapus();

        if (!mounted) return;
        setState(() {
          _status = _StatusSesi.login;
          _pesan = 'Sesi Anda telah berakhir. Silakan login kembali.';
        });
        return;
      }

      // Offline atau server bermasalah: tetap masuk dengan data terakhir.
      if (SesiPengguna.pengguna != null) {
        setState(() => _status = _StatusSesi.masuk);
        _tampilkanPesanOffline();
        return;
      }

      setState(() {
        _status = _StatusSesi.galat;
        _pesan = kesalahan.pesan;
      });
    } catch (_) {
      if (!mounted) return;

      if (SesiPengguna.pengguna != null) {
        setState(() => _status = _StatusSesi.masuk);
        _tampilkanPesanOffline();
        return;
      }

      setState(() {
        _status = _StatusSesi.galat;
        _pesan = 'Tidak dapat memeriksa sesi. Periksa koneksi Anda.';
      });
    }
  }

  void _tampilkanPesanOffline() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!mounted) return;

      ScaffoldMessenger.of(context)
        ..hideCurrentSnackBar()
        ..showSnackBar(
          const SnackBar(
            content: Text(
              'Tidak dapat memverifikasi sesi. Data terakhir tetap ditampilkan.',
            ),
          ),
        );
    });
  }

  Future<void> _keluar() async {
    await const LayananAutentikasi().keluar();

    if (!mounted) return;

    setState(() {
      _status = _StatusSesi.login;
      _pesan = null;
    });
  }

  @override
  Widget build(BuildContext context) {
    switch (_status) {
      case _StatusSesi.memuat:
        return const _HalamanMemuat();
      case _StatusSesi.login:
        return LoginView(pesanAwal: _pesan);
      case _StatusSesi.masuk:
        return BerandaView(
          nama: SesiPengguna.nama,
          kelas: SesiPengguna.kelas,
        );
      case _StatusSesi.galat:
        return _HalamanGalat(
          pesan: _pesan ?? 'Terjadi kesalahan saat memeriksa sesi.',
          saatCobaLagi: _periksaSesi,
          saatKeluar: _keluar,
        );
    }
  }
}

class _HalamanMemuat extends StatelessWidget {
  const _HalamanMemuat();

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: WarnaAplikasi.utama,
      body: Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              'SISTEMATIK',
              style: TextStyle(
                color: Colors.white,
                fontSize: 22,
                fontWeight: FontWeight.bold,
                letterSpacing: 0.5,
              ),
            ),
            SizedBox(height: 18),
            SizedBox(
              width: 26,
              height: 26,
              child: CircularProgressIndicator(
                strokeWidth: 2.5,
                color: Colors.white,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _HalamanGalat extends StatelessWidget {
  final String pesan;
  final Future<void> Function() saatCobaLagi;
  final Future<void> Function() saatKeluar;

  const _HalamanGalat({
    required this.pesan,
    required this.saatCobaLagi,
    required this.saatKeluar,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: WarnaAplikasi.latarBelakangAutentikasi,
      body: Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 32),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(
                Icons.cloud_off_outlined,
                size: 54,
                color: WarnaAplikasi.utama,
              ),
              const SizedBox(height: 16),
              Text(
                pesan,
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 14),
              ),
              const SizedBox(height: 22),
              ElevatedButton(
                onPressed: saatCobaLagi,
                style: ElevatedButton.styleFrom(
                  backgroundColor: WarnaAplikasi.utama,
                  foregroundColor: Colors.white,
                ),
                child: const Text('Coba Lagi'),
              ),
              TextButton(
                onPressed: saatKeluar,
                child: const Text(
                  'Kembali ke Login',
                  style: TextStyle(color: WarnaAplikasi.utama),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
