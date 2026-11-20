import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/api_tts.dart';

/// Baris yang menampilkan tombol "Play Audio" (TTS ElevenLabs) di kiri
/// dan indikator sisa waktu di kanan untuk Pre-Test & Post-Test.
///
/// Timer hanya ditampilkan jika [sisaDetik] tidak null, yaitu saat server
/// mengirim batas waktu (post-test yang punya jadwal). Pre-Test tidak
/// dibatasi waktu di Laravel, jadi chip timer disembunyikan.
class BarisTimerAudio extends StatelessWidget {
  final VoidCallback? saatPutarAudio;
  final int? sisaDetik;

  const BarisTimerAudio({
    super.key,
    this.saatPutarAudio,
    this.sisaDetik,
  });

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(
      builder: (context, constraints) {
        return Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Flexible(
              child: ValueListenableBuilder<StatusTts>(
                valueListenable: LayananTts.instance.status,
                builder: (context, status, _) {
                  final bool isMemuat = status == StatusTts.memuat;
                  final bool isMemutar = status == StatusTts.memutar;
                  final bool isJeda = status == StatusTts.jeda;

                  IconData ikon = Icons.volume_up;
                  String label = 'Play Audio';

                  if (isMemuat) {
                    ikon = Icons.hourglass_top_rounded;
                    label = 'Memuat Audio...';
                  } else if (isMemutar) {
                    ikon = Icons.pause_circle_filled;
                    label = 'Jeda Audio';
                  } else if (isJeda) {
                    ikon = Icons.play_circle_fill;
                    label = 'Lanjut Audio';
                  }

                  return InkWell(
                    onTap: saatPutarAudio ?? () {},
                    borderRadius: BorderRadius.circular(8),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(
                        vertical: 6,
                        horizontal: 4,
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          if (isMemuat) ...[
                            const SizedBox(
                              width: 16,
                              height: 16,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                color: WarnaAplikasi.utama,
                              ),
                            ),
                            const SizedBox(width: 6),
                          ] else ...[
                            Icon(
                              ikon,
                              color: WarnaAplikasi.utama,
                              size: 20,
                            ),
                            const SizedBox(width: 6),
                          ],
                          Flexible(
                            child: Text(
                              label,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                color: WarnaAplikasi.utama,
                                fontWeight: FontWeight.w700,
                                fontSize: 14,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
            if (sisaDetik != null) ...[
              const SizedBox(width: 12),
              Flexible(child: _ChipSisaWaktu(sisaDetik: sisaDetik!)),
            ],
          ],
        );
      },
    );
  }
}

/// Chip hitung mundur `HH:MM:SS` dengan warna peringatan seperti di web:
/// amber saat sisa <= 5 menit, merah saat sisa <= 1 menit.
class _ChipSisaWaktu extends StatelessWidget {
  const _ChipSisaWaktu({required this.sisaDetik});

  final int sisaDetik;

  String get _teks {
    final sisa = sisaDetik < 0 ? 0 : sisaDetik;
    final jam = (sisa ~/ 3600).toString().padLeft(2, '0');
    final menit = ((sisa % 3600) ~/ 60).toString().padLeft(2, '0');
    final detik = (sisa % 60).toString().padLeft(2, '0');
    return '$jam:$menit:$detik';
  }

  @override
  Widget build(BuildContext context) {
    final bool kritis = sisaDetik <= 60;
    final bool mendesak = !kritis && sisaDetik <= 300;
    final Color warna = kritis
        ? const Color(0xFFB91C1C)
        : mendesak
            ? const Color(0xFFB45309)
            : WarnaAplikasi.utama;
    final Color latar = kritis
        ? const Color(0xFFFEF2F2)
        : mendesak
            ? const Color(0xFFFFFBEB)
            : Colors.white;

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: latar,
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: warna.withValues(alpha: 0.35), width: 1.2),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(Icons.timer_outlined, color: warna, size: 18),
          const SizedBox(width: 6),
          Flexible(
            child: Text(
              _teks,
              overflow: TextOverflow.ellipsis,
              style: TextStyle(
                color: warna,
                fontWeight: FontWeight.w700,
                fontSize: 14,
                fontFeatures: const [FontFeature.tabularFigures()],
              ),
            ),
          ),
          const SizedBox(width: 4),
          Text(
            'sisa',
            style: TextStyle(
              color: warna.withValues(alpha: 0.8),
              fontWeight: FontWeight.w600,
              fontSize: 11,
            ),
          ),
        ],
      ),
    );
  }
}
