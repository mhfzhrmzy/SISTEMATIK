import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Bottom Navigation Bar yang selalu fixed di bagian bawah layar.
///
/// Gunakan di dalam [Scaffold.bottomNavigationBar] pada setiap halaman
/// yang membutuhkan navigasi bawah ini.
///
/// Contoh penggunaan:
/// ```dart
/// Scaffold(
///   bottomNavigationBar: NavigasiBawah(indeksSaatIni: 0),
///   body: ...,
/// )
/// ```
class NavigasiBawah extends StatelessWidget {
  /// Index tab yang sedang aktif:
  /// 0 = Home, 1 = Peringkat (Trophy), 2 = Profil
  final int indeksSaatIni;

  /// Callback saat item di-tap. Jika null, navigasi dihandle secara internal.
  final ValueChanged<int>? saatDiketuk;

  const NavigasiBawah({
    super.key,
    this.indeksSaatIni = 0,
    this.saatDiketuk,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.08),
            blurRadius: 12,
            offset: const Offset(0, -2),
          ),
        ],
      ),
      child: SafeArea(
        top: false,
        child: SizedBox(
          height: 64,
          child: Padding(
            // Padding kiri-kanan agar ikon tidak terlalu mepet tepi layar
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Row(
              children: [
                _ItemNavigasi(
                  ikon: Icons.home_outlined,
                  ikonAktif: Icons.home_rounded,
                  label: 'Home',
                  aktif: indeksSaatIni == 0,
                  saatDiketuk: () => (saatDiketuk ?? (_) {})(0),
                ),
                _ItemNavigasi(
                  ikon: Icons.emoji_events_outlined,
                  ikonAktif: Icons.emoji_events_rounded,
                  label: 'Peringkat',
                  aktif: indeksSaatIni == 1,
                  saatDiketuk: () => (saatDiketuk ?? (_) {})(1),
                ),
                _ItemNavigasi(
                  ikon: Icons.person_outline_rounded,
                  ikonAktif: Icons.person_rounded,
                  label: 'Profil',
                  aktif: indeksSaatIni == 2,
                  saatDiketuk: () => (saatDiketuk ?? (_) {})(2),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

/// Item tunggal pada NavigasiBawah.
class _ItemNavigasi extends StatelessWidget {
  final IconData ikon;
  final IconData ikonAktif;
  final String label;
  final bool aktif;
  final VoidCallback saatDiketuk;

  const _ItemNavigasi({
    required this.ikon,
    required this.ikonAktif,
    required this.label,
    required this.aktif,
    required this.saatDiketuk,
  });

  @override
  Widget build(BuildContext context) {
    final warna = aktif ? WarnaAplikasi.utama : Colors.grey.shade500;

    return Expanded(
      child: InkWell(
        onTap: saatDiketuk,
        borderRadius: BorderRadius.circular(12),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              aktif ? ikonAktif : ikon,
              color: warna,
              size: 26,
            ),
            const SizedBox(height: 3),
            Text(
              label,
              style: TextStyle(
                color: warna,
                fontSize: 10,
                fontWeight: aktif ? FontWeight.w600 : FontWeight.normal,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
