import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Kartu opsi jawaban untuk satu pilihan ganda.
///
/// Menampilkan label huruf (A, B, C, D) dan teks jawaban.
/// Tampilan berubah sesuai status [terpilih].
class OpsiJawaban extends StatelessWidget {
  final String label;
  final String teks;
  final bool terpilih;
  final VoidCallback saatDiketuk;

  const OpsiJawaban({
    super.key,
    required this.label,
    required this.teks,
    required this.terpilih,
    required this.saatDiketuk,
  });

  @override
  Widget build(BuildContext context) {
    final lebar = MediaQuery.of(context).size.width;
    final double ukuranFont = lebar > 600 ? 16 : 14.5;

    return Material(
      color: Colors.transparent,
      borderRadius: BorderRadius.circular(12),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: saatDiketuk,
        child: Container(
          width: double.infinity,
          constraints: const BoxConstraints(minHeight: 52),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          decoration: BoxDecoration(
            color: terpilih ? WarnaAplikasi.terpilih : Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: terpilih
                  ? WarnaAplikasi.borderTerpilih
                  : WarnaAplikasi.border,
              width: terpilih ? 1.5 : 1,
            ),
            boxShadow: [
              BoxShadow(
                color: WarnaAplikasi.bayanganLembut,
                blurRadius: 8,
                offset: const Offset(0, 3),
              ),
            ],
          ),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                '$label.',
                style: TextStyle(
                  color: WarnaAplikasi.utama,
                  fontWeight: FontWeight.bold,
                  fontSize: ukuranFont,
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  teks,
                  softWrap: true,
                  style: TextStyle(
                    color: WarnaAplikasi.utama,
                    fontWeight: FontWeight.w600,
                    fontSize: ukuranFont,
                    height: 1.35,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
