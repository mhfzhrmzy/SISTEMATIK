import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Modal bottom sheet untuk filter rentang tanggal.
class ModalFilterTanggal extends StatefulWidget {
  const ModalFilterTanggal({
    super.key,
    required this.tanggalMulai,
    required this.tanggalAkhir,
    required this.onTerapkan,
    required this.onReset,
  });

  final DateTime? tanggalMulai;
  final DateTime? tanggalAkhir;
  final void Function(DateTime?, DateTime?) onTerapkan;
  final VoidCallback onReset;

  @override
  State<ModalFilterTanggal> createState() => _ModalFilterTanggalState();
}

class _ModalFilterTanggalState extends State<ModalFilterTanggal> {
  late DateTime? tempMulai = widget.tanggalMulai;
  late DateTime? tempAkhir = widget.tanggalAkhir;

  static String _formatTanggalPendek(DateTime d) {
    const bulan = [
      'Jan',
      'Feb',
      'Mar',
      'Apr',
      'Mei',
      'Jun',
      'Jul',
      'Agt',
      'Sep',
      'Okt',
      'Nov',
      'Des'
    ];
    final hari = d.day.toString().padLeft(2, '0');
    final bln = bulan[d.month - 1];
    return '$hari $bln ${d.year}';
  }

  Widget _temaPemilihTanggal(BuildContext context, Widget? child) {
    return Theme(
      data: Theme.of(context).copyWith(
        colorScheme: const ColorScheme.light(
          primary: WarnaAplikasi.utama,
          onPrimary: Colors.white,
          surface: Colors.white,
          onSurface: WarnaAplikasi.utama,
        ),
      ),
      child: child!,
    );
  }

  @override
  Widget build(BuildContext context) {
    final now = DateTime.now();
    final hariIni = DateTime(now.year, now.month, now.day);
    final satuTahunLalu = DateTime(now.year - 1, now.month, now.day);
    final duaTahunLalu = DateTime(now.year - 2, now.month, now.day);

    bool hariSama(DateTime? a, DateTime b) {
      if (a == null) return false;
      return a.year == b.year && a.month == b.month && a.day == b.day;
    }

    final bool semuaDipilih = tempMulai == null && tempAkhir == null;
    final bool satuTahunDipilih =
        hariSama(tempMulai, satuTahunLalu) && hariSama(tempAkhir, hariIni);
    final bool duaTahunDipilih =
        hariSama(tempMulai, duaTahunLalu) && hariSama(tempAkhir, hariIni);

    return SafeArea(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 20),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // ── Header Modal ─────────────────────────────────────────
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Filter Rentang Tanggal',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    color: WarnaAplikasi.utama,
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.close,
                      color: WarnaAplikasi.utama, size: 20),
                  onPressed: () => Navigator.pop(context),
                ),
              ],
            ),
            const SizedBox(height: 4),
            const Text(
              'Pilih tanggal mulai sampai tanggal selesai sertifikat diterbitkan.',
              style: TextStyle(
                fontSize: 12,
                color: Color(0xFFD4A0B0),
              ),
            ),
            const SizedBox(height: 18),

            // ── Input Tanggal: Dari Tanggal & Sampai Tanggal ─────────
            Row(
              children: [
                Expanded(
                  child: InkWell(
                    onTap: () async {
                      final picked = await showDatePicker(
                        context: context,
                        initialDate: tempMulai ?? hariIni,
                        firstDate: DateTime(2015),
                        lastDate: hariIni,
                        builder: _temaPemilihTanggal,
                      );
                      if (picked != null) {
                        setState(() {
                          tempMulai = picked;
                          if (tempAkhir != null &&
                              tempAkhir!.isBefore(picked)) {
                            tempAkhir = picked;
                          }
                        });
                      }
                    },
                    borderRadius: BorderRadius.circular(12),
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 12, vertical: 10),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: WarnaAplikasi.utama.withValues(alpha: 0.35),
                          width: 1.2,
                        ),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Dari Tanggal',
                            style: TextStyle(
                                fontSize: 11,
                                color: Colors.grey,
                                fontWeight: FontWeight.w500),
                          ),
                          const SizedBox(height: 4),
                          Row(
                            children: [
                              const Icon(Icons.calendar_today_rounded,
                                  size: 14, color: WarnaAplikasi.utama),
                              const SizedBox(width: 6),
                              Expanded(
                                child: Text(
                                  tempMulai != null
                                      ? _formatTanggalPendek(tempMulai!)
                                      : 'Pilih...',
                                  style: TextStyle(
                                    fontSize: 13,
                                    fontWeight: FontWeight.bold,
                                    color: tempMulai != null
                                        ? WarnaAplikasi.utama
                                        : Colors.grey.shade400,
                                  ),
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: InkWell(
                    onTap: () async {
                      final picked = await showDatePicker(
                        context: context,
                        initialDate: tempAkhir ?? tempMulai ?? hariIni,
                        firstDate: tempMulai ?? DateTime(2015),
                        lastDate: hariIni,
                        builder: _temaPemilihTanggal,
                      );
                      if (picked != null) {
                        if (tempMulai != null && picked.isBefore(tempMulai!))
                          return;
                        setState(() {
                          tempAkhir = picked;
                        });
                      }
                    },
                    borderRadius: BorderRadius.circular(12),
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 12, vertical: 10),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: WarnaAplikasi.utama.withValues(alpha: 0.35),
                          width: 1.2,
                        ),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Sampai Tanggal',
                            style: TextStyle(
                                fontSize: 11,
                                color: Colors.grey,
                                fontWeight: FontWeight.w500),
                          ),
                          const SizedBox(height: 4),
                          Row(
                            children: [
                              const Icon(Icons.event_available_rounded,
                                  size: 15, color: WarnaAplikasi.utama),
                              const SizedBox(width: 6),
                              Expanded(
                                child: Text(
                                  tempAkhir != null
                                      ? _formatTanggalPendek(tempAkhir!)
                                      : 'Pilih...',
                                  style: TextStyle(
                                    fontSize: 13,
                                    fontWeight: FontWeight.bold,
                                    color: tempAkhir != null
                                        ? WarnaAplikasi.utama
                                        : Colors.grey.shade400,
                                  ),
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ],
            ),

            const SizedBox(height: 12),

            // ── Preset Cepat ─────────────────────────────────────────
            Wrap(
              spacing: 8,
              runSpacing: 6,
              children: [
                ActionChip(
                  label: const Text('Semua'),
                  backgroundColor: semuaDipilih
                      ? WarnaAplikasi.utama
                      : const Color(0xFFF6EFF1),
                  labelStyle: TextStyle(
                    color: semuaDipilih ? Colors.white : WarnaAplikasi.utama,
                    fontSize: 11.5,
                    fontWeight: FontWeight.bold,
                  ),
                  onPressed: () {
                    setState(() {
                      tempMulai = null;
                      tempAkhir = null;
                    });
                  },
                ),
                ActionChip(
                  label: const Text('1 Tahun'),
                  backgroundColor: satuTahunDipilih
                      ? WarnaAplikasi.utama
                      : const Color(0xFFF6EFF1),
                  labelStyle: TextStyle(
                    color:
                        satuTahunDipilih ? Colors.white : WarnaAplikasi.utama,
                    fontSize: 11.5,
                    fontWeight: FontWeight.bold,
                  ),
                  onPressed: () {
                    setState(() {
                      tempMulai = satuTahunLalu;
                      tempAkhir = hariIni;
                    });
                  },
                ),
                ActionChip(
                  label: const Text('2 Tahun'),
                  backgroundColor: duaTahunDipilih
                      ? WarnaAplikasi.utama
                      : const Color(0xFFF6EFF1),
                  labelStyle: TextStyle(
                    color: duaTahunDipilih ? Colors.white : WarnaAplikasi.utama,
                    fontSize: 11.5,
                    fontWeight: FontWeight.bold,
                  ),
                  onPressed: () {
                    setState(() {
                      tempMulai = duaTahunLalu;
                      tempAkhir = hariIni;
                    });
                  },
                ),
              ],
            ),

            const SizedBox(height: 20),

            // ── Tombol Terapkan & Reset ──────────────────────────────
            Row(
              children: [
                Expanded(
                  child: TextButton(
                    onPressed: () {
                      widget.onReset();
                      Navigator.pop(context);
                    },
                    style: TextButton.styleFrom(
                      foregroundColor: WarnaAplikasi.utama,
                      padding: const EdgeInsets.symmetric(vertical: 12),
                    ),
                    child: const Text('Reset Filter',
                        style: TextStyle(fontWeight: FontWeight.bold)),
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  flex: 2,
                  child: ElevatedButton(
                    onPressed: () {
                      widget.onTerapkan(tempMulai, tempAkhir);
                      Navigator.pop(context);
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: WarnaAplikasi.utama,
                      foregroundColor: Colors.white,
                      elevation: 0,
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10)),
                    ),
                    child: const Text('Terapkan Filter',
                        style: TextStyle(fontWeight: FontWeight.bold)),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
