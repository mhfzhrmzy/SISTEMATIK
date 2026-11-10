import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';
import 'package:pretest_posttest_app/fitur/beranda/views/beranda_view.dart';
import 'package:pretest_posttest_app/fitur/papan_peringkat/views/papan_peringkat_view.dart';
import 'package:pretest_posttest_app/fitur/profil/views/profil_view.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/item_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/views/upload_sertifikat_view.dart';

/// Halaman daftar sertifikat & portofolio.
class SertifikatView extends StatefulWidget {
  /// Daftar sertifikat awal — bisa dioper dari UploadSertifikatView
  /// setelah operasi CRUD agar data tetap sinkron.
  final List<ItemSertifikat>? daftarAwal;

  const SertifikatView({super.key, this.daftarAwal});

  @override
  State<SertifikatView> createState() => _SertifikatViewState();
}

class _SertifikatViewState extends State<SertifikatView> {
  // Daftar sertifikat — diisi dari daftarAwal jika ada, atau dummy data.
  late List<ItemSertifikat> _daftarSertifikat;

  @override
  void initState() {
    super.initState();
    _daftarSertifikat = widget.daftarAwal ??
        [
          ItemSertifikat(
            label: 'Sertifikat',
            judul: 'Dasar Phyton',
            tanggalTerbit: 'Diterbitkan pada: 12 Mar 2026',
            tanggal: DateTime(2026, 3, 12),
          ),
          ItemSertifikat(
            label: 'Sertifikat',
            judul: 'Dasar Phyton',
            tanggalTerbit: 'Diterbitkan pada: 01 Mar 2026',
            tanggal: DateTime(2026, 3, 1),
          ),
        ];
  }

  // State rentang tanggal terpilih (dari tanggal X sampai tanggal Y)
  DateTime? _tanggalMulai;
  DateTime? _tanggalAkhir;

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

  List<ItemSertifikat> get _daftarTerfilter {
    if (_tanggalMulai == null || _tanggalAkhir == null) {
      return _daftarSertifikat;
    }
    return _daftarSertifikat.where((item) {
      final tglItem =
          DateTime(item.tanggal.year, item.tanggal.month, item.tanggal.day);
      final mulai = DateTime(
          _tanggalMulai!.year, _tanggalMulai!.month, _tanggalMulai!.day);
      final akhir = DateTime(
          _tanggalAkhir!.year, _tanggalAkhir!.month, _tanggalAkhir!.day);
      return (tglItem.isAfter(mulai) || tglItem.isAtSameMomentAs(mulai)) &&
          (tglItem.isBefore(akhir) || tglItem.isAtSameMomentAs(akhir));
    }).toList();
  }

  void _saatFooterDiketuk(int idx) {
    if (idx == 2) {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const ProfilView()),
      );
    } else if (idx == 0) {
      Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(builder: (_) => const BerandaView()),
        (route) => false,
      );
    } else if (idx == 1) {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const PapanPeringkatView()),
      );
    }
  }

  /// Navigasi ke halaman Form Upload Sertifikat Baru (CRUD)
  void _navigasiUpload() {
    Navigator.of(context).pushReplacement(
      MaterialPageRoute(
        builder: (_) => UploadSertifikatView(
          daftarSertifikat: List.from(_daftarSertifikat),
          saatDaftarBerubah: (updated) {},
        ),
      ),
    );
  }

  void _unduhSertifikat(ItemSertifikat item) {
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(
        SnackBar(
          content: Text('Mengunduh sertifikat ${item.judul}...'),
          backgroundColor: WarnaAplikasi.utama,
          duration: const Duration(seconds: 2),
        ),
      );
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

  /// Tampilkan modal bottom sheet untuk filter tanggal (Dari - Sampai)
  void _tampilkanModalFilter() {
    DateTime? tempMulai = _tanggalMulai;
    DateTime? tempAkhir = _tanggalAkhir;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => StatefulBuilder(
        builder: (modalCtx, setModalState) {
          final now = DateTime.now();
          final hariIni = DateTime(now.year, now.month, now.day);
          final satuTahunLalu = DateTime(now.year - 1, now.month, now.day);
          final duaTahunLalu = DateTime(now.year - 2, now.month, now.day);

          bool hariSama(DateTime? a, DateTime b) {
            if (a == null) return false;
            return a.year == b.year && a.month == b.month && a.day == b.day;
          }

          final bool semuaDipilih = tempMulai == null && tempAkhir == null;
          final bool satuTahunDipilih = hariSama(tempMulai, satuTahunLalu) &&
              hariSama(tempAkhir, hariIni);
          final bool duaTahunDipilih =
              hariSama(tempMulai, duaTahunLalu) && hariSama(tempAkhir, hariIni);

          return SafeArea(
            child: Padding(
              padding: EdgeInsets.only(
                left: 20,
                right: 20,
                top: 18,
                bottom: MediaQuery.of(modalCtx).viewInsets.bottom + 18,
              ),
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
                        onPressed: () => Navigator.pop(ctx),
                      ),
                    ],
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Pilih tanggal mulai sampai tanggal selesai sertifikat diterbitkan.',
                    style: TextStyle(
                      fontSize: 12,
                      color: WarnaAplikasi.utama.withValues(alpha: 0.65),
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
                              context: modalCtx,
                              initialDate: tempMulai ?? hariIni,
                              firstDate: DateTime(2020),
                              lastDate: DateTime(2030),
                              builder: _temaPemilihTanggal,
                            );
                            if (picked != null) {
                              setModalState(() {
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
                                color:
                                    WarnaAplikasi.utama.withValues(alpha: 0.35),
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
                              context: modalCtx,
                              initialDate: tempAkhir ?? tempMulai ?? hariIni,
                              firstDate: tempMulai ?? DateTime(2020),
                              lastDate: DateTime(2030),
                              builder: _temaPemilihTanggal,
                            );
                            if (picked != null) {
                              setModalState(() {
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
                                color:
                                    WarnaAplikasi.utama.withValues(alpha: 0.35),
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

                  // ── Tombol Buka Kalender Rentang Lengkap ───────────────────
                  OutlinedButton.icon(
                    onPressed: () async {
                      final picked = await showDateRangePicker(
                        context: modalCtx,
                        firstDate: DateTime(2020),
                        lastDate: DateTime(2030),
                        initialDateRange: (tempMulai != null &&
                                tempAkhir != null)
                            ? DateTimeRange(start: tempMulai!, end: tempAkhir!)
                            : DateTimeRange(start: satuTahunLalu, end: hariIni),
                        builder: _temaPemilihTanggal,
                      );
                      if (picked != null) {
                        setModalState(() {
                          tempMulai = picked.start;
                          tempAkhir = picked.end;
                        });
                      }
                    },
                    style: OutlinedButton.styleFrom(
                      foregroundColor: WarnaAplikasi.utama,
                      side: BorderSide(
                          color: WarnaAplikasi.utama.withValues(alpha: 0.35)),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10)),
                      padding: const EdgeInsets.symmetric(vertical: 10),
                    ),
                    icon: const Icon(Icons.date_range_rounded, size: 16),
                    label: const Text(
                      'Pilih Rentang dari Kalender',
                      style: TextStyle(
                          fontSize: 12.5, fontWeight: FontWeight.bold),
                    ),
                  ),

                  const SizedBox(height: 14),

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
                          color:
                              semuaDipilih ? Colors.white : WarnaAplikasi.utama,
                          fontSize: 11.5,
                          fontWeight: FontWeight.bold,
                        ),
                        onPressed: () {
                          setModalState(() {
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
                          color: satuTahunDipilih
                              ? Colors.white
                              : WarnaAplikasi.utama,
                          fontSize: 11.5,
                          fontWeight: FontWeight.bold,
                        ),
                        onPressed: () {
                          setModalState(() {
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
                          color: duaTahunDipilih
                              ? Colors.white
                              : WarnaAplikasi.utama,
                          fontSize: 11.5,
                          fontWeight: FontWeight.bold,
                        ),
                        onPressed: () {
                          setModalState(() {
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
                            setState(() {
                              _tanggalMulai = null;
                              _tanggalAkhir = null;
                            });
                            Navigator.pop(ctx);
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
                            setState(() {
                              _tanggalMulai = tempMulai;
                              _tanggalAkhir = tempAkhir;
                            });
                            Navigator.pop(ctx);
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
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      bottomNavigationBar: NavigasiBawah(
        indeksSaatIni: 2,
        saatDiketuk: _saatFooterDiketuk,
      ),
      body: AnnotatedRegion<SystemUiOverlayStyle>(
        value: SystemUiOverlayStyle.light
            .copyWith(statusBarColor: Colors.transparent),
        child: SafeArea(
          top: false,
          bottom: false,
          child: Column(
            children: [
              // ── Header Maroon "Sertifikat & Portofolio" ─────────────────
              const _HeaderSertifikat(),

              // ── Konten Scrollable ────────────────────────────────────────
              Expanded(
                child: LayoutBuilder(
                  builder: (ctx, constraints) {
                    final double hp =
                        (constraints.maxWidth * 0.075).clamp(22.0, 40.0);
                    final items = _daftarTerfilter;
                    final bool filterAktif =
                        _tanggalMulai != null && _tanggalAkhir != null;

                    return SingleChildScrollView(
                      physics: const BouncingScrollPhysics(),
                      padding: EdgeInsets.fromLTRB(hp, 14, hp, 24),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // ── 1. Tombol Back Pill ────────────────────────────
                          InkWell(
                            onTap: () {
                              if (Navigator.of(context).canPop()) {
                                Navigator.of(context).pop();
                              } else {
                                Navigator.of(context).pushReplacement(
                                  MaterialPageRoute(
                                      builder: (_) => const BerandaView()),
                                );
                              }
                            },
                            borderRadius: BorderRadius.circular(20),
                            child: Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 12, vertical: 5),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(
                                  color: WarnaAplikasi.utama
                                      .withValues(alpha: 0.35),
                                  width: 1.2,
                                ),
                              ),
                              child: const Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Icon(Icons.arrow_back_rounded,
                                      size: 14, color: WarnaAplikasi.utama),
                                  SizedBox(width: 4),
                                  Text('Back',
                                      style: TextStyle(
                                          color: WarnaAplikasi.utama,
                                          fontSize: 12,
                                          fontWeight: FontWeight.bold)),
                                ],
                              ),
                            ),
                          ),

                          const SizedBox(height: 14),

                          // ── 2. Tombol "Upload Sertifikat Baru" ─
                          SizedBox(
                            width: double.infinity,
                            height: 46,
                            child: ElevatedButton(
                              onPressed: _navigasiUpload,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: WarnaAplikasi.utama,
                                foregroundColor: Colors.white,
                                elevation: 0,
                                shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12)),
                              ),
                              child: const Text('Upload Sertifikat Baru',
                                  style: TextStyle(
                                      fontSize: 14,
                                      fontWeight: FontWeight.bold)),
                            ),
                          ),

                          const SizedBox(height: 20),

                          // ── 3. Heading "Daftar sertifikat" + Tombol Filter ──
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text('Daftar sertifikat',
                                  style: TextStyle(
                                      fontSize: 15,
                                      fontWeight: FontWeight.bold,
                                      color: WarnaAplikasi.utama)),
                              InkWell(
                                onTap: _tampilkanModalFilter,
                                borderRadius: BorderRadius.circular(8),
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 4, vertical: 4),
                                  child: Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      const Icon(Icons.tune_rounded,
                                          size: 18, color: WarnaAplikasi.utama),
                                      const SizedBox(width: 4),
                                      Text(
                                        filterAktif
                                            ? 'Filter (Aktif)'
                                            : 'Filter',
                                        style: const TextStyle(
                                            fontSize: 13.5,
                                            fontWeight: FontWeight.bold,
                                            color: WarnaAplikasi.utama),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),

                          // ── Badge Filter Aktif ─
                          if (filterAktif) ...[
                            const SizedBox(height: 8),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 10, vertical: 4),
                              decoration: BoxDecoration(
                                color:
                                    WarnaAplikasi.utama.withValues(alpha: 0.08),
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(
                                    color: WarnaAplikasi.utama
                                        .withValues(alpha: 0.3)),
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  const Icon(Icons.date_range_rounded,
                                      size: 13, color: WarnaAplikasi.utama),
                                  const SizedBox(width: 6),
                                  Text(
                                    '${_formatTanggalPendek(_tanggalMulai!)} - ${_formatTanggalPendek(_tanggalAkhir!)}',
                                    style: const TextStyle(
                                        fontSize: 11.5,
                                        fontWeight: FontWeight.bold,
                                        color: WarnaAplikasi.utama),
                                  ),
                                  const SizedBox(width: 6),
                                  InkWell(
                                    onTap: () {
                                      setState(() {
                                        _tanggalMulai = null;
                                        _tanggalAkhir = null;
                                      });
                                    },
                                    borderRadius: BorderRadius.circular(10),
                                    child: const Icon(Icons.close,
                                        size: 14, color: WarnaAplikasi.utama),
                                  ),
                                ],
                              ),
                            ),
                          ],

                          const SizedBox(height: 12),

                          // ── 4. Card List Sertifikat ──────
                          if (items.isEmpty)
                            Padding(
                              padding: const EdgeInsets.symmetric(vertical: 36),
                              child: Center(
                                child: Text(
                                  'Tidak ada sertifikat pada rentang tanggal ini.',
                                  style: TextStyle(
                                      color: WarnaAplikasi.utama
                                          .withValues(alpha: 0.6),
                                      fontSize: 13),
                                ),
                              ),
                            )
                          else
                            ListView.separated(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              itemCount: items.length,
                              separatorBuilder: (_, __) =>
                                  const SizedBox(height: 14),
                              itemBuilder: (ctx, idx) {
                                final item = items[idx];
                                return Container(
                                  padding:
                                      const EdgeInsets.fromLTRB(16, 14, 16, 14),
                                  decoration: BoxDecoration(
                                    color: Colors.white,
                                    borderRadius: BorderRadius.circular(12),
                                    border: Border.all(
                                        color: WarnaAplikasi.utama, width: 1.0),
                                  ),
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(item.label,
                                          style: const TextStyle(
                                              color: WarnaAplikasi.utama,
                                              fontSize: 12,
                                              fontWeight: FontWeight.bold)),
                                      const SizedBox(height: 3),
                                      Text(item.judul,
                                          style: const TextStyle(
                                              color: WarnaAplikasi.utama,
                                              fontSize: 16,
                                              fontWeight: FontWeight.bold)),
                                      const SizedBox(height: 3),
                                      Text(item.tanggalTerbit,
                                          style: TextStyle(
                                              color: WarnaAplikasi.utama
                                                  .withValues(alpha: 0.7),
                                              fontSize: 11,
                                              fontWeight: FontWeight.w500)),
                                      const SizedBox(height: 12),
                                      SizedBox(
                                        width: double.infinity,
                                        height: 38,
                                        child: ElevatedButton(
                                          onPressed: () =>
                                              _unduhSertifikat(item),
                                          style: ElevatedButton.styleFrom(
                                            backgroundColor:
                                                WarnaAplikasi.utama,
                                            foregroundColor: Colors.white,
                                            elevation: 0,
                                            shape: RoundedRectangleBorder(
                                                borderRadius:
                                                    BorderRadius.circular(8)),
                                          ),
                                          child: const Text('Unduh Sertifikat',
                                              style: TextStyle(
                                                  fontSize: 13,
                                                  fontWeight: FontWeight.bold)),
                                        ),
                                      ),
                                    ],
                                  ),
                                );
                              },
                            ),
                        ],
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

/// Header Maroon melengkung untuk halaman Sertifikat & Portofolio
class _HeaderSertifikat extends StatelessWidget {
  const _HeaderSertifikat();

  @override
  Widget build(BuildContext context) {
    final mediaQuery = MediaQuery.of(context);
    final double tinggiStatusBar = mediaQuery.viewPadding.top;
    final double hp = (mediaQuery.size.width * 0.075).clamp(22.0, 40.0);

    return Material(
      elevation: 4,
      shadowColor: WarnaAplikasi.bayanganLembut,
      color: WarnaAplikasi.utama,
      borderRadius: const BorderRadius.only(
        bottomLeft: Radius.circular(32),
        bottomRight: Radius.circular(32),
      ),
      child: Padding(
        padding: EdgeInsets.only(
          top: tinggiStatusBar + 20,
          bottom: 22,
          left: hp,
          right: hp,
        ),
        child: const Center(
          child: Text(
            'Sertifikat & Portofolio',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: Colors.white,
              fontSize: 20,
              fontWeight: FontWeight.bold,
              letterSpacing: -0.2,
            ),
          ),
        ),
      ),
    );
  }
}
