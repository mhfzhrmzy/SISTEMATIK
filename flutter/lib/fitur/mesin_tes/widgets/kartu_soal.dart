import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Kartu yang menampilkan teks soal saat ini, dan jika ada,
/// blok kode monospace di atasnya.
///
/// Tidak memiliki tinggi tetap: membungkus kontennya sehingga
/// soal panjang / cuplikan kode membuat kartu lebih tinggi.
class KartuSoal extends StatelessWidget {
  final String teksSoal;
  final String? kode;

  const KartuSoal({
    super.key,
    required this.teksSoal,
    this.kode,
  });

  @override
  Widget build(BuildContext context) {
    final lebar = MediaQuery.of(context).size.width;
    final double ukuranFont = lebar > 600 ? 16 : 15;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: WarnaAplikasi.latarBelakang,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: WarnaAplikasi.border, width: 1),
        boxShadow: [
          BoxShadow(
            color: WarnaAplikasi.bayanganLembut,
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisSize: MainAxisSize.min,
        children: [
          Text(
            'Soal:',
            style: TextStyle(
              color: WarnaAplikasi.utama,
              fontWeight: FontWeight.w700,
              fontSize: ukuranFont,
            ),
          ),
          if (kode != null) ...[
            const SizedBox(height: 10),
            _BlokKode(kode: kode!),
            const SizedBox(height: 12),
          ] else
            const SizedBox(height: 6),
          Text(
            teksSoal,
            softWrap: true,
            style: TextStyle(
              color: WarnaAplikasi.utama,
              fontSize: ukuranFont,
              height: 1.4,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }
}

/// Blok kode monospace yang bisa di-scroll horizontal, digunakan untuk
/// menampilkan cuplikan Python di dalam kartu soal tanpa overflow
/// pada layar sempit.
class _BlokKode extends StatelessWidget {
  final String kode;

  const _BlokKode({required this.kode});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
      decoration: BoxDecoration(
        color: WarnaAplikasi.border.withValues(alpha: 0.5),
        borderRadius: BorderRadius.circular(8),
      ),
      child: SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: Text(
          kode,
          style: const TextStyle(
            fontFamily: 'monospace',
            fontSize: 14,
            color: WarnaAplikasi.utamaGelap,
            height: 1.5,
          ),
        ),
      ),
    );
  }
}
