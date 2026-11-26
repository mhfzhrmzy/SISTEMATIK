import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/models/model_leaderboard.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/widgets/batang_podium.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Satu baris kartu siswa (peringkat 4–10, placeholder, atau kartu user
/// yang di-pin di bagian bawah).
class KartuPeringkat extends StatelessWidget {
  final ModelLeaderboard item;
  final bool kartuSayaDiBawah;

  const KartuPeringkat({
    super.key,
    required this.item,
    this.kartuSayaDiBawah = false,
  });

  @override
  Widget build(BuildContext context) {
    final isPlaceholder = item.nisn.isEmpty && !kartuSayaDiBawah;

    final warnaLatar = kartuSayaDiBawah
        ? WarnaAplikasi.utama
        : (item.saya
            ? WarnaAplikasi.utama.withValues(alpha: 0.08)
            : Colors.white);

    final warnaTeks = kartuSayaDiBawah
        ? Colors.white
        : (isPlaceholder
            ? WarnaAplikasi.utama.withValues(alpha: 0.4)
            : WarnaAplikasi.utama);

    return Container(
      height: kartuSayaDiBawah ? 58 : 50,
      margin: kartuSayaDiBawah
          ? const EdgeInsets.only(bottom: 12, top: 4)
          : null,
      padding: const EdgeInsets.symmetric(horizontal: 14),
      decoration: BoxDecoration(
        color: warnaLatar,
        borderRadius: BorderRadius.circular(kartuSayaDiBawah ? 16 : 12),
        border: kartuSayaDiBawah
            ? null
            : Border.all(color: WarnaAplikasi.utama, width: 1.2),
        boxShadow: kartuSayaDiBawah
            ? [
                BoxShadow(
                  color: WarnaAplikasi.utama.withValues(alpha: 0.3),
                  blurRadius: 8,
                  offset: const Offset(0, 3),
                ),
              ]
            : null,
      ),
      child: Row(
        children: [
          SizedBox(
            width: 28,
            child: Text(
              '${item.peringkat}',
              textAlign: TextAlign.center,
              style: TextStyle(
                color: warnaTeks,
                fontSize: 15,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              item.nama,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: TextStyle(
                color: warnaTeks,
                fontSize: 15,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
          const SizedBox(width: 10),
          AvatarSiswa(
            pengguna: item,
            ukuran: 36,
            placeholder: isPlaceholder,
          ),
        ],
      ),
    );
  }
}
