import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Ikon avatar profil bundar yang tampil di halaman Login & Register.
/// Mencoba memuat asset image terlebih dahulu; jika gagal, menggunakan
/// fallback [CustomPaint] bergambar siluet kepala & bahu.
class IkonAvatarAutentikasi extends StatelessWidget {
  final double ukuran;
  const IkonAvatarAutentikasi({super.key, this.ukuran = 80});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: ukuran,
      height: ukuran,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.08),
            blurRadius: 14,
            offset: const Offset(0, 4),
          ),
          BoxShadow(
            color: WarnaAplikasi.utama.withValues(alpha: 0.06),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: ClipOval(
        child: Image.asset(
          'assets/images/auth_avatar.png',
          width: ukuran,
          height: ukuran,
          fit: BoxFit.contain,
          errorBuilder: (context, error, stackTrace) {
            // Fallback vektor presisi tinggi jika aset tidak tersedia
            return CustomPaint(
              size: Size(ukuran, ukuran),
              painter: const _PelukisAvatarProfil(warna: WarnaAplikasi.utama),
            );
          },
        ),
      ),
    );
  }
}

class _PelukisAvatarProfil extends CustomPainter {
  final Color warna;

  const _PelukisAvatarProfil({required this.warna});

  @override
  void paint(Canvas canvas, Size size) {
    final cx = size.width / 2;
    final cy = size.height / 2;
    final r = size.width / 2;

    final catIsi = Paint()
      ..color = warna
      ..style = PaintingStyle.fill
      ..isAntiAlias = true;

    // ── 1. Background putih lingkaran ───────────────────────────
    final catLatar = Paint()
      ..color = Colors.white
      ..style = PaintingStyle.fill
      ..isAntiAlias = true;
    canvas.drawCircle(Offset(cx, cy), r, catLatar);

    // ── 2. Lingkaran luar (stroke) maroon ────────────────────────
    final lebarGaris = size.width * (10.0 / 176.0);
    final catGaris = Paint()
      ..color = warna
      ..style = PaintingStyle.stroke
      ..strokeWidth = lebarGaris
      ..isAntiAlias = true;
    canvas.drawCircle(Offset(cx, cy), r - (lebarGaris / 2), catGaris);

    // ── 3. Kepala (Lingkaran solid maroon) ───────────────────────
    final radiusKepala = size.width * (33.0 / 176.0);
    final pusatKepalaY = size.height * (66.0 / 176.0);
    canvas.drawCircle(Offset(cx, pusatKepalaY), radiusKepala, catIsi);

    // ── 4. Bahu / Torso (Kurva lengkung simetris di bagian bawah) ──
    final radiusDalam = r - lebarGaris;
    canvas.save();
    canvas.clipPath(
      Path()
        ..addOval(Rect.fromCircle(center: Offset(cx, cy), radius: radiusDalam)),
    );

    final skala = size.width / 176.0;
    final jalanBahu = Path();
    jalanBahu.moveTo(15.0 * skala, 160.0 * skala);
    jalanBahu.cubicTo(
      48.0 * skala,
      122.0 * skala,
      68.0 * skala,
      110.0 * skala,
      88.0 * skala,
      110.0 * skala,
    );
    jalanBahu.cubicTo(
      108.0 * skala,
      110.0 * skala,
      128.0 * skala,
      122.0 * skala,
      161.0 * skala,
      160.0 * skala,
    );
    jalanBahu.lineTo(176.0 * skala, 176.0 * skala);
    jalanBahu.lineTo(0.0, 176.0 * skala);
    jalanBahu.close();

    canvas.drawPath(jalanBahu, catIsi);
    canvas.restore();
  }

  @override
  bool shouldRepaint(covariant _PelukisAvatarProfil oldDelegate) {
    return oldDelegate.warna != warna;
  }
}
