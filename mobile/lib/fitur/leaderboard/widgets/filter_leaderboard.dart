import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/materi/models/model_materi.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Toggle tab "Keseluruhan" / "Permateri".
class ToggleModeLeaderboard extends StatelessWidget {
  final int tabTerpilih;
  final ValueChanged<int> onPilihTab;

  const ToggleModeLeaderboard({
    super.key,
    required this.tabTerpilih,
    required this.onPilihTab,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 44,
      padding: const EdgeInsets.all(3),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: WarnaAplikasi.utama, width: 1.5),
      ),
      child: Row(
        children: [
          _tombolTab('Keseluruhan', 0),
          _tombolTab('Permateri', 1),
        ],
      ),
    );
  }

  Widget _tombolTab(String judul, int indeks) {
    final aktif = tabTerpilih == indeks;
    return Expanded(
      child: InkWell(
        onTap: () => onPilihTab(indeks),
        borderRadius: BorderRadius.circular(10),
        child: Container(
          decoration: BoxDecoration(
            color: aktif ? WarnaAplikasi.utama : Colors.transparent,
            borderRadius: BorderRadius.circular(10),
          ),
          alignment: Alignment.center,
          child: Text(
            judul,
            style: TextStyle(
              color: aktif ? Colors.white : WarnaAplikasi.utama,
              fontWeight: FontWeight.w600,
              fontSize: 13.5,
            ),
          ),
        ),
      ),
    );
  }
}

/// Horizontal scroll pilihan materi (hanya tampil saat tab "Permateri").
class PillsMateriLeaderboard extends StatelessWidget {
  final bool sedangMemuat;
  final List<ModelMateri> daftarMateri;
  final int indeksTerpilih;
  final ValueChanged<int> onPilihMateri;

  const PillsMateriLeaderboard({
    super.key,
    required this.sedangMemuat,
    required this.daftarMateri,
    required this.indeksTerpilih,
    required this.onPilihMateri,
  });

  @override
  Widget build(BuildContext context) {
    if (sedangMemuat) {
      return const SizedBox(
        height: 32,
        child: Center(
          child: SizedBox(
            width: 16,
            height: 16,
            child: CircularProgressIndicator(strokeWidth: 2),
          ),
        ),
      );
    }

    if (daftarMateri.isEmpty) {
      return const SizedBox(
        height: 32,
        child: Center(
          child: Text(
            'Tidak ada materi',
            style: TextStyle(color: WarnaAplikasi.utama, fontSize: 12),
          ),
        ),
      );
    }

    return SizedBox(
      height: 32,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        itemCount: daftarMateri.length,
        separatorBuilder: (_, __) => const SizedBox(width: 8),
        itemBuilder: (_, idx) {
          final terpilih = idx == indeksTerpilih;
          return InkWell(
            onTap: () => onPilihMateri(idx),
            borderRadius: BorderRadius.circular(8),
            child: Container(
              padding: const EdgeInsets.symmetric(
                horizontal: 14,
                vertical: 6,
              ),
              decoration: BoxDecoration(
                color: terpilih
                    ? WarnaAplikasi.utama
                    : const Color(0xFFF1EFF1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                daftarMateri[idx].judul,
                style: TextStyle(
                  color: terpilih
                      ? Colors.white
                      : WarnaAplikasi.utama.withValues(alpha: 0.65),
                  fontSize: 12,
                  fontWeight:
                      terpilih ? FontWeight.bold : FontWeight.w600,
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}
