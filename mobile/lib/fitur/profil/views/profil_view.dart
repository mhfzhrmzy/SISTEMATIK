import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:pretest_posttest_app/inti/konstanta/konfigurasi_api.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/sesi_pengguna.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/data/layanan_autentikasi.dart';
import 'package:pretest_posttest_app/fitur/autentikasi/views/login_view.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/views/leaderboard_view.dart';
import 'package:pretest_posttest_app/fitur/profil/views/pengaturan_akun_view.dart';
import 'package:pretest_posttest_app/fitur/profil/data/layanan_profil.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/views/sertifikat_view.dart';

/// Halaman Profil pengguna.
///
/// Struktur (mengikuti desain Figma "Profile"):
/// - Header maroon melengkung berisi avatar inisial, nama, dan kelas/sekolah
/// - Menu utama: "Pengaturan akun siswa" (solid) & "Sertifikat & Portofolio" (outline)
/// - Tombol "Keluar Akun" (soft pink) di atas footer
/// - [NavigasiBawah] fixed di bawah dengan tab Profil aktif (index 2)
///
/// Navigasi:
/// - Tab Home  -> kembali ke Beranda (root route)
/// - Tab Leaderboard -> [LeaderboardView]
/// - Sertifikat & Portofolio -> [SertifikatView]
/// - Keluar Akun -> dialog konfirmasi, lalu kembali ke [LoginView]
class ProfilView extends StatefulWidget {
  /// Nama lengkap pengguna. Jika kosong, diambil dari sesi login.
  final String? nama;

  /// Kelas pengguna. Jika kosong, diambil dari sesi login.
  final String? kelas;
  final LayananProfil? layananProfil;

  const ProfilView({
    super.key,
    this.nama,
    this.kelas,
    this.layananProfil,
  });

  @override
  State<ProfilView> createState() => _ProfilViewState();
}

class _ProfilViewState extends State<ProfilView> {
  String get _nama => widget.nama ?? SesiPengguna.nama ?? 'Siswa';

  String get _kelas => widget.kelas ?? SesiPengguna.kelas ?? 'Kelas -';

  // ── Warna khusus halaman Profil ────────────────────────────────────────
  static const Color _warnaLatarAvatar = Color(0xFFBA7D8E);
  static const Color _warnaLatarKeluar = Color(0xFFFFF0F1);
  static const Color _warnaBorderKeluar = Color(0xFFF3C4CB);

  // ── Navigasi footer ──────────────────────────────────────────────────────
  void _saatFooterDiketuk(BuildContext context, int idx) {
    switch (idx) {
      case 0:
        // Beranda adalah root route setelah login, jadi cukup pop sampai root.
        Navigator.of(context).popUntil((route) => route.isFirst);
        break;
      case 1:
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (_) => const LeaderboardView()),
        );
        break;
      case 2:
        // Sudah berada di halaman Profil.
        break;
    }
  }

  Future<void> _konfirmasiKeluar(BuildContext context) async {
    final bool? yakin = await showDialog<bool>(
      context: context,
      builder: (dialogCtx) => AlertDialog(
        backgroundColor: Colors.white,
        surfaceTintColor: Colors.transparent,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text(
          'Keluar Akun?',
          style: TextStyle(
            color: WarnaAplikasi.utama,
            fontSize: 17,
            fontWeight: FontWeight.bold,
          ),
        ),
        content: const Text(
          'Kamu akan keluar dari akun ini dan kembali ke halaman Login.',
          style: TextStyle(fontSize: 13),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(dialogCtx).pop(false),
            child: Text(
              'Batal',
              style: TextStyle(color: Colors.grey.shade600),
            ),
          ),
          TextButton(
            onPressed: () => Navigator.of(dialogCtx).pop(true),
            child: const Text(
              'Keluar',
              style: TextStyle(
                color: WarnaAplikasi.utama,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );

    if (yakin != true || !context.mounted) return;

    final navigator = Navigator.of(context);
    final navigatorRoot = Navigator.of(context, rootNavigator: true);
    var dialogMasihTerbuka = true;

    // Tampilkan indikator selama token dicabut di server.
    showDialog<void>(
      context: context,
      barrierDismissible: false,
      builder: (_) => const Center(
        child: CircularProgressIndicator(color: WarnaAplikasi.utama),
      ),
    ).whenComplete(() => dialogMasihTerbuka = false);

    try {
      if (widget.layananProfil != null) {
        await widget.layananProfil!.keluar();
        await SesiPengguna.hapus();
      } else {
        await const LayananAutentikasi().keluar();
      }
    } finally {
      if (mounted && dialogMasihTerbuka) {
        navigatorRoot.pop();
      }
    }

    if (!mounted) return;

    navigator.pushAndRemoveUntil(
      MaterialPageRoute(builder: (_) => const LoginView()),
      (route) => false,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: WarnaAplikasi.latarBelakang,
      // ── Footer fixed di bawah (tab Profil aktif) ─────────────────────────
      bottomNavigationBar: NavigasiBawah(
        indeksSaatIni: 2,
        saatDiketuk: (idx) => _saatFooterDiketuk(context, idx),
      ),
      body: AnnotatedRegion<SystemUiOverlayStyle>(
        value: SystemUiOverlayStyle.light.copyWith(
          statusBarColor: Colors.transparent,
        ),
        child: SafeArea(
          top: false,
          bottom: false,
          child: Column(
            children: [
              _HeaderProfil(
                nama: _nama,
                kelas: _kelas,
                warnaAvatar: _warnaLatarAvatar,
                fotoUrl: KonfigurasiApi.perbaikiUrlFoto(SesiPengguna.fotoProfil),
              ),
              Expanded(
                child: LayoutBuilder(
                  builder: (ctx, constraints) {
                    final double hp =
                        (constraints.maxWidth * 0.075).clamp(22.0, 40.0);
                    return SingleChildScrollView(
                      physics: const BouncingScrollPhysics(),
                      child: ConstrainedBox(
                        constraints:
                            BoxConstraints(minHeight: constraints.maxHeight),
                        child: IntrinsicHeight(
                          child: Padding(
                            padding: EdgeInsets.fromLTRB(hp, 0, hp, 20),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.stretch,
                              children: [
                                const Spacer(flex: 3),

                                // ── Menu utama ───────────────────────────────
                                _TombolMenuProfil(
                                  label: 'Pengaturan akun siswa',
                                  terisi: true,
                                  saatDiketuk: () => Navigator.of(context).push(
                                    MaterialPageRoute(
                                      builder: (_) => PengaturanAkunView(
                                        namaAwal: _nama,
                                        layananProfil: widget.layananProfil,
                                      ),
                                    ),
                                  ),
                                ),
                                const SizedBox(height: 14),
                                _TombolMenuProfil(
                                  label: 'Sertifikat & Portofolio',
                                  terisi: false,
                                  saatDiketuk: () => Navigator.of(context).push(
                                    MaterialPageRoute(
                                        builder: (_) => const SertifikatView()),
                                  ),
                                ),

                                const Spacer(flex: 4),

                                // ── Keluar Akun ──────────────────────────────
                                _TombolKeluar(
                                  warnaLatar: _warnaLatarKeluar,
                                  warnaBorder: _warnaBorderKeluar,
                                  saatDiketuk: () => _konfirmasiKeluar(context),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// ────────────────────────────────────────────────────────────────────────────
// Header Profil (maroon melengkung, full bleed sampai atas status bar)
// ────────────────────────────────────────────────────────────────────────────
class _HeaderProfil extends StatelessWidget {
  final String nama;
  final String kelas;
  final Color warnaAvatar;
  final String? fotoUrl;

  const _HeaderProfil({
    required this.nama,
    required this.kelas,
    this.warnaAvatar = const Color(0xFFBA7D8E),
    this.fotoUrl,
  });

  Widget _inisial() {
    return Center(
      child: Text(
        nama.isNotEmpty ? nama[0].toUpperCase() : 'U',
        style: const TextStyle(
          color: WarnaAplikasi.utamaGelap,
          fontSize: 24,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final mediaQuery = MediaQuery.of(context);
    final double tinggiStatusBar = mediaQuery.viewPadding.top;
    final double hp = (mediaQuery.size.width * 0.075).clamp(22.0, 40.0);

    return AnnotatedRegion<SystemUiOverlayStyle>(
      // Ikon status bar putih agar kontras di atas maroon.
      value: SystemUiOverlayStyle.light.copyWith(
        statusBarColor: Colors.transparent,
      ),
      child: Material(
        elevation: 6,
        shadowColor: WarnaAplikasi.bayanganLembut,
        color: WarnaAplikasi.utama,
        clipBehavior: Clip.antiAlias,
        borderRadius: const BorderRadius.only(
          bottomLeft: Radius.circular(36),
          bottomRight: Radius.circular(36),
        ),
        child: Stack(
          children: [
            // Aksen lengkung halus di latar header (mengikuti gelombang Figma)
            Positioned(
              top: -70,
              right: -50,
              child: _lingkaranLembut(220),
            ),
            Positioned(
              bottom: -90,
              left: -60,
              child: _lingkaranLembut(200),
            ),
            Padding(
              padding: EdgeInsets.only(
                top: tinggiStatusBar + 34,
                bottom: 34,
                left: hp,
                right: hp,
              ),
              child: Row(
                children: [
                  // Avatar profil
                  Container(
                    width: 56,
                    height: 56,
                    decoration: BoxDecoration(
                      color: warnaAvatar,
                      borderRadius: BorderRadius.circular(18),
                    ),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(18),
                      child: fotoUrl != null && fotoUrl!.isNotEmpty
                          ? Image.network(
                              fotoUrl!,
                              width: 56,
                              height: 56,
                              fit: BoxFit.cover,
                              errorBuilder: (_, __, ___) => _inisial(),
                            )
                          : _inisial(),
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          nama,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 4),
                        Text(
                          kelas,
                          style: const TextStyle(
                            color: Colors.white70,
                            fontSize: 12,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  static Widget _lingkaranLembut(double ukuran) {
    return Container(
      width: ukuran,
      height: ukuran,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: Colors.white.withValues(alpha: 0.05),
      ),
    );
  }
}

// ────────────────────────────────────────────────────────────────────────────
// Tombol menu (solid / outline)
// ────────────────────────────────────────────────────────────────────────────
class _TombolMenuProfil extends StatelessWidget {
  final String label;

  /// true  = solid maroon, teks putih
  /// false = outline maroon, background putih, teks maroon
  final bool terisi;
  final VoidCallback saatDiketuk;

  const _TombolMenuProfil({
    required this.label,
    required this.terisi,
    required this.saatDiketuk,
  });

  @override
  Widget build(BuildContext context) {
    final BorderRadius radius = BorderRadius.circular(12);

    return SizedBox(
      width: double.infinity,
      height: 48,
      child: Material(
        color: terisi ? WarnaAplikasi.utama : Colors.white,
        borderRadius: radius,
        clipBehavior: Clip.antiAlias,
        child: InkWell(
          onTap: saatDiketuk,
          borderRadius: radius,
          child: Container(
            width: double.infinity,
            height: 48,
            alignment: Alignment.centerLeft,
            padding: const EdgeInsets.symmetric(horizontal: 16),
            decoration: BoxDecoration(
              borderRadius: radius,
              border: Border.all(
                color: WarnaAplikasi.utama,
                width: 1.5,
              ),
            ),
            child: Text(
              label,
              style: TextStyle(
                color: terisi ? Colors.white : WarnaAplikasi.utama,
                fontSize: 14,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),
      ),
    );
  }
}

// ────────────────────────────────────────────────────────────────────────────
// Tombol Keluar Akun (soft pink)
// ────────────────────────────────────────────────────────────────────────────
class _TombolKeluar extends StatelessWidget {
  final Color warnaLatar;
  final Color warnaBorder;
  final VoidCallback saatDiketuk;

  const _TombolKeluar({
    required this.warnaLatar,
    required this.warnaBorder,
    required this.saatDiketuk,
  });

  @override
  Widget build(BuildContext context) {
    final BorderRadius radius = BorderRadius.circular(12);

    return Center(
      child: FractionallySizedBox(
        widthFactor: 0.8,
        child: Material(
          color: warnaLatar,
          borderRadius: radius,
          child: InkWell(
            onTap: saatDiketuk,
            borderRadius: radius,
            child: Container(
              height: 40,
              alignment: Alignment.center,
              decoration: BoxDecoration(
                borderRadius: radius,
                border: Border.all(color: warnaBorder),
              ),
              child: const Text(
                'Keluar Akun',
                style: TextStyle(
                  color: WarnaAplikasi.utama,
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
