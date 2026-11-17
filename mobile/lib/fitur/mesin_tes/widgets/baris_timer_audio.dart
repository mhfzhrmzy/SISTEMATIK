import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/layanan_tts.dart';

/// Baris yang menampilkan tombol "Play Audio" (TTS ElevenLabs) di kiri
/// dan indikator timer di kanan untuk Pre-Test & Post-Test.
class BarisTimerAudio extends StatelessWidget {
  final VoidCallback? saatPutarAudio;
  final String labelTimer;

  const BarisTimerAudio({
    super.key,
    this.saatPutarAudio,
    this.labelTimer = '30 Detik',
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
            const SizedBox(width: 12),
            Flexible(
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Icon(
                    Icons.timer_outlined,
                    color: WarnaAplikasi.utama,
                    size: 20,
                  ),
                  const SizedBox(width: 6),
                  Flexible(
                    child: Text(
                      labelTimer,
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
          ],
        );
      },
    );
  }
}
