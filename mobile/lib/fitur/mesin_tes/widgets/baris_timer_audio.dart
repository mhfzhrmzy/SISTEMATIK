import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Baris yang menampilkan tombol "Play Audio" di kiri dan indikator
/// timer "30 Detik" di kanan.
///
/// Keduanya bersifat visual saja (frontend-only): tidak ada pemutaran
/// audio nyata maupun logika hitung mundur yang diimplementasikan.
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
              child: InkWell(
                onTap: saatPutarAudio ?? () {},
                borderRadius: BorderRadius.circular(8),
                child: const Padding(
                  padding: EdgeInsets.symmetric(
                    vertical: 6,
                    horizontal: 4,
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(
                        Icons.volume_up,
                        color: WarnaAplikasi.utama,
                        size: 20,
                      ),
                      SizedBox(width: 6),
                      Flexible(
                        child: Text(
                          'Play Audio',
                          overflow: TextOverflow.ellipsis,
                          style: TextStyle(
                            color: WarnaAplikasi.utama,
                            fontWeight: FontWeight.w700,
                            fontSize: 14,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
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
