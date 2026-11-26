import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/services/api_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/item_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/widgets/header_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/widgets/kartu_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/widgets/modal_filter_tanggal.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/penyimpan_file.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/screens/pratinjau_sertifikat_screen.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/screens/upload_sertifikat_screen.dart';
import 'package:pretest_posttest_app/fitur/beranda/screens/beranda_screen.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/screens/leaderboard_screen.dart';
import 'package:pretest_posttest_app/fitur/profil/screens/profil_screen.dart';

/// Halaman daftar sertifikat & portofolio.
class SertifikatScreen extends StatefulWidget {
  /// Daftar sertifikat awal — bisa dioper dari UploadSertifikatScreen
  /// setelah operasi CRUD agar data tetap sinkron.
  final List<ItemSertifikat>? daftarAwal;
  final LayananSertifikat? layananSertifikat;

  const SertifikatScreen({
    super.key,
    this.daftarAwal,
    this.layananSertifikat,
  });

  @override
  State<SertifikatScreen> createState() => _SertifikatScreenState();
}

class _SertifikatScreenState extends State<SertifikatScreen> {
  // Daftar sertifikat — diisi dari daftarAwal jika ada, atau dari API.
  late List<ItemSertifikat> _daftarSertifikat;
  late final LayananSertifikat _layananSertifikat;
  bool _memuat = false;
  String? _pesanGalat;
  bool _dariCache = false;

  @override
  void initState() {
    super.initState();
    _layananSertifikat = widget.layananSertifikat ?? LayananSertifikat();
    _daftarSertifikat = widget.daftarAwal ?? [];
    if (widget.daftarAwal == null) _muatSertifikat();
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

  /// Tampilkan modal bottom sheet untuk filter tanggal (Dari - Sampai)
  void _tampilkanModalFilter() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => ModalFilterTanggal(
        tanggalMulai: _tanggalMulai,
        tanggalAkhir: _tanggalAkhir,
        onTerapkan: (mulai, akhir) {
          setState(() {
            _tanggalMulai = mulai;
            _tanggalAkhir = akhir;
          });
        },
        onReset: () {
          setState(() {
            _tanggalMulai = null;
            _tanggalAkhir = null;
          });
        },
      ),
    );
  }

  void _saatFooterDiketuk(int idx) {
    if (idx == 2) {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const ProfilScreen()),
      );
    } else if (idx == 0) {
      Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(builder: (_) => const BerandaScreen()),
        (route) => false,
      );
    } else if (idx == 1) {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const LeaderboardScreen()),
      );
    }
  }

  Future<void> _muatSertifikat() async {
    setState(() {
      _memuat = true;
      _pesanGalat = null;
    });
    try {
      final hasil = await _layananSertifikat.ambilDaftarSertifikat();
      if (!mounted) return;
      setState(() {
        _daftarSertifikat = hasil.daftar;
        _dariCache = hasil.dariCache;
        _memuat = false;
      });
    } on KesalahanApi catch (error) {
      if (!mounted) return;
      setState(() {
        _pesanGalat = error.pesan;
        _memuat = false;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _pesanGalat = 'Gagal memuat sertifikat.';
        _memuat = false;
      });
    }
  }

  Future<void> _unduhSertifikat(ItemSertifikat item) async {
    if (item.id == null) {
      _tampilkanPesan('ID sertifikat tidak tersedia.');
      return;
    }
    try {
      final bytes = await _layananSertifikat.unduh(item.id!);
      await simpanFile(bytes, 'sertifikat_${item.id}.pdf');
    } on KesalahanApi catch (error) {
      _tampilkanPesan(error.pesan);
    } catch (_) {
      _tampilkanPesan('Sertifikat gagal diunduh.');
    }
  }

  void _bukaPratinjau(ItemSertifikat item) {
    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (_) => PratinjauSertifikatScreen(
          item: item,
          layananSertifikat: _layananSertifikat,
        ),
      ),
    );
  }

  Future<void> _hapusSertifikat(ItemSertifikat item) async {
    if (item.id == null) {
      _tampilkanPesan('ID sertifikat tidak tersedia.');
      return;
    }
    final konfirmasi = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: Colors.white,
        surfaceTintColor: Colors.transparent,
        shape: const RoundedRectangleBorder(
            borderRadius: BorderRadius.all(Radius.circular(20))),
        title: const Text('Hapus Sertifikat?',
            style: TextStyle(
                color: WarnaAplikasi.utama,
                fontSize: 16,
                fontWeight: FontWeight.bold)),
        content: Text(
            'Sertifikat "${item.judul}" akan dihapus secara permanen.',
            style: const TextStyle(fontSize: 13)),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: Text('Batal', style: TextStyle(color: Colors.grey.shade600)),
          ),
          TextButton(
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text(
              'Hapus',
              style: TextStyle(
                  color: WarnaAplikasi.utama, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
    if (konfirmasi != true) return;

    try {
      await _layananSertifikat.hapus(item.id!);
      if (!mounted) return;
      setState(() {
        _daftarSertifikat.removeWhere((s) => s.id == item.id);
      });
      _tampilkanPesan('Sertifikat berhasil dihapus.');
    } on KesalahanApi catch (error) {
      if (!mounted) return;
      _tampilkanPesan(error.pesan);
    } catch (_) {
      if (!mounted) return;
      _tampilkanPesan('Sertifikat gagal dihapus.');
    }
  }

  void _tampilkanPesan(String pesan) {
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(SnackBar(content: Text(pesan)));
  }

  void _navigasiUpload() {
    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (_) => UploadSertifikatScreen(
          daftarSertifikat: List.from(_daftarSertifikat),
          saatDaftarBerubah: (updated) {
            setState(() => _daftarSertifikat = updated);
          },
        ),
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
              const HeaderSertifikat(),

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
                                      builder: (_) => const BerandaScreen()),
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

                          // ── 2. Tombol "Upload Sertifikat Baru" ───────────────
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

                          const SizedBox(height: 16),

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
                          if (_memuat)
                            const Center(
                              child: Padding(
                                padding: EdgeInsets.all(24),
                                child: CircularProgressIndicator(),
                              ),
                            )
                          else if (_pesanGalat != null)
                            Center(
                              child: Column(
                                children: [
                                  Text(_pesanGalat!),
                                  TextButton(
                                    onPressed: _muatSertifikat,
                                    child: const Text('Coba lagi'),
                                  ),
                                ],
                              ),
                            )
                          else if (_dariCache)
                            Padding(
                              padding: const EdgeInsets.only(bottom: 8),
                              child: Row(
                                children: [
                                  const Icon(
                                    Icons.cloud_off_outlined,
                                    size: 16,
                                    color: WarnaAplikasi.utama,
                                  ),
                                  const SizedBox(width: 6),
                                  Text(
                                    'Menampilkan data offline (cache terakhir)',
                                    style: TextStyle(
                                      color: WarnaAplikasi.utama
                                          .withValues(alpha: 0.7),
                                      fontSize: 12,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          if (!_memuat && _pesanGalat == null && items.isEmpty)
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
                              return KartuSertifikat(
                                item: item,
                                onTapKartu: () => _bukaPratinjau(item),
                                onUnduh: () => _unduhSertifikat(item),
                                onHapus: () => _hapusSertifikat(item),
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
