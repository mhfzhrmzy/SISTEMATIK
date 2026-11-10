import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/header_tes.dart';
import 'package:pretest_posttest_app/fitur/materi/models/model_materi.dart';
import 'package:pretest_posttest_app/fitur/profil/views/profil_view.dart';

/// Halaman Buka Materi.
///
/// Fitur & Desain:
/// - Header maroon menggunakan [HeaderTes] yang sama persis dengan Pre-Test dan Post-Test:
///   full-bleed hingga status bar (ikon jam, baterai, wifi berwarna putih), posisi judul,
///   radius lengkungan 36px, dan bayangan yang selaras.
/// - Tombol Back (kembali ke beranda) FIXED di bawah header.
/// - Yang di-scroll HANYA dari "Deskripsi singkat" sampai kotak materi PDF.
/// - Kotak isi materi berukuran FIX (proporsional A4: 210 x 297 mm, tanpa scroll di dalam kotaknya).
/// - Kotak deskripsi otomatis menyesuaikan tinggi teks (auto-height) tanpa terpotong.
/// - Tombol navigasi halaman (< Back | Halaman X/Y | Next >) dan tombol Selesai FIXED di bawah.
/// - NavigasiBawah fixed di bawah.
/// - Responsif penuh di mobile & tablet (portrait maupun landscape).
class BukaMateriView extends StatefulWidget {
  final ModelMateri materi;

  const BukaMateriView({super.key, required this.materi});

  @override
  State<BukaMateriView> createState() => _BukaMateriViewState();
}

class _BukaMateriViewState extends State<BukaMateriView> {
  int _halamanSaatIni = 0;
  int _indeksFooter = 0;

  int get _totalHalaman => widget.materi.totalHalaman;
  bool get _adalahPertama => _halamanSaatIni == 0;
  bool get _adalahTerakhir => _halamanSaatIni == _totalHalaman - 1;

  void _halamanSebelumnya() {
    if (_adalahPertama) return;
    setState(() => _halamanSaatIni--);
  }

  void _halamanBerikutnya() {
    if (_adalahTerakhir) return;
    setState(() => _halamanSaatIni++);
  }

  void _selesai() {
    Navigator.of(context).pop();
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('${widget.materi.judul} selesai dipelajari!'),
        backgroundColor: WarnaAplikasi.utama,
      ),
    );
  }

  Future<void> _saatFooterDiketuk(int idx) async {
    if (idx == 0) {
      Navigator.of(context).pop();
    } else if (idx == 1) {
      setState(() => _indeksFooter = idx);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Halaman Peringkat (coming soon)')),
      );
    } else if (idx == 2) {
      setState(() => _indeksFooter = idx);
      await Navigator.of(context).push(
        MaterialPageRoute(builder: (_) => const ProfilView()),
      );
      if (!mounted) return;
      setState(() => _indeksFooter = 0);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      bottomNavigationBar: NavigasiBawah(
        indeksSaatIni: _indeksFooter,
        saatDiketuk: _saatFooterDiketuk,
      ),
      // SafeArea top: false agar HeaderTes full-bleed ke atas layar di belakang status bar
      body: SafeArea(
        top: false,
        bottom: false,
        child: LayoutBuilder(
          builder: (context, constraints) {
            // Padding horizontal proporsional (7.5% lebar layar, clamp 20–44)
            final double hp = (constraints.maxWidth * 0.075).clamp(20.0, 44.0);

            final double lebarKontainer = constraints.maxWidth - (hp * 2);

            // Tinggi FIX kotak materi proporsional rasio kertas A4 (210 x 297 mm, 1 : 1.4142)
            final double tinggiA4 =
                (lebarKontainer * (297.0 / 210.0)).clamp(360.0, 560.0);

            return Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // ── 1. Header Materi (Sama persis dengan Pre-Test & Post-Test) ──
                HeaderTes(judul: widget.materi.judul),

                // ── 2. Tombol Back ke Beranda (FIXED, tidak ikut ter-scroll) ─
                Padding(
                  padding: EdgeInsets.fromLTRB(hp, 14, hp, 6),
                  child: Align(
                    alignment: Alignment.centerLeft,
                    child: _TombolKembali(
                      saatDiketuk: () => Navigator.of(context).pop(),
                    ),
                  ),
                ),

                // ── 3. Konten Scrollable (HANYA Deskripsi Singkat & Kolom Materi) ──
                Expanded(
                  child: SingleChildScrollView(
                    physics: const BouncingScrollPhysics(),
                    padding: EdgeInsets.fromLTRB(hp, 6, hp, 10),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Label Deskripsi singkat
                        const _LabelDeskripsi(),
                        const SizedBox(height: 6),

                        // Container deskripsi (auto-height, responsif terhadap panjang teks)
                        _KotakDeskripsi(deskripsi: widget.materi.deskripsi),
                        const SizedBox(height: 14),

                        // Kotak Materi PDF (FIXED ukuran A4, TANPA scroll di dalamnya)
                        _KotakKontainerPdf(tinggi: tinggiA4),
                      ],
                    ),
                  ),
                ),

                // ── 4. Kontrol Bawah (FIXED di bawah sebelum Footer) ─────────
                Padding(
                  padding: EdgeInsets.fromLTRB(hp, 8, hp, 12),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      // Navigasi halaman: < Back | Halaman X/Y | Next >
                      _BarisKontrolNavigasi(
                        halamanSaatIni: _halamanSaatIni,
                        totalHalaman: _totalHalaman,
                        adalahPertama: _adalahPertama,
                        adalahTerakhir: _adalahTerakhir,
                        saatSebelumnya: _halamanSebelumnya,
                        saatBerikutnya: _halamanBerikutnya,
                      ),
                      const SizedBox(height: 10),

                      // Tombol Selesai maroon
                      _TombolSelesai(saatDitekan: _selesai),
                    ],
                  ),
                ),
              ],
            );
          },
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Tombol Back kecil (Pill Shape)
// ─────────────────────────────────────────────────────────────────────────────
class _TombolKembali extends StatelessWidget {
  final VoidCallback saatDiketuk;
  const _TombolKembali({required this.saatDiketuk});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: saatDiketuk,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: WarnaAplikasi.utama.withValues(alpha: 0.35),
            width: 1.2,
          ),
        ),
        child: const Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              Icons.arrow_circle_left_outlined,
              size: 18,
              color: WarnaAplikasi.utama,
            ),
            SizedBox(width: 6),
            Text(
              'Back',
              style: TextStyle(
                fontSize: 13,
                color: WarnaAplikasi.utama,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Label "Deskripsi singkat"
// ─────────────────────────────────────────────────────────────────────────────
class _LabelDeskripsi extends StatelessWidget {
  const _LabelDeskripsi();

  @override
  Widget build(BuildContext context) {
    return const Text(
      'Deskripsi singkat',
      style: TextStyle(
        fontSize: 13.5,
        fontWeight: FontWeight.bold,
        color: WarnaAplikasi.utama,
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Container Deskripsi Singkat (Auto-Height, Responsive)
// ─────────────────────────────────────────────────────────────────────────────
class _KotakDeskripsi extends StatelessWidget {
  final String deskripsi;

  const _KotakDeskripsi({required this.deskripsi});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      constraints: const BoxConstraints(minHeight: 48),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade300, width: 1.2),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Text(
        deskripsi,
        style: TextStyle(
          fontSize: 13,
          color: Colors.grey.shade800,
          height: 1.45,
        ),
        softWrap: true,
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Container Materi PDF (FIX Ukuran A4, TANPA Scroll di Dalamnya)
// ─────────────────────────────────────────────────────────────────────────────
class _KotakKontainerPdf extends StatelessWidget {
  final double tinggi;

  const _KotakKontainerPdf({required this.tinggi});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      height: tinggi,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade300, width: 1.2),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Bar Navigasi Halaman (< Back | Halaman X/Y | Next >)
// ─────────────────────────────────────────────────────────────────────────────
class _BarisKontrolNavigasi extends StatelessWidget {
  final int halamanSaatIni;
  final int totalHalaman;
  final bool adalahPertama;
  final bool adalahTerakhir;
  final VoidCallback saatSebelumnya;
  final VoidCallback saatBerikutnya;

  const _BarisKontrolNavigasi({
    required this.halamanSaatIni,
    required this.totalHalaman,
    required this.adalahPertama,
    required this.adalahTerakhir,
    required this.saatSebelumnya,
    required this.saatBerikutnya,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        // Tombol < Back
        _TombolNavigasiHalaman(
          label: 'Back',
          adalahBack: true,
          nonaktif: adalahPertama,
          saatDiketuk: adalahPertama ? null : saatSebelumnya,
        ),

        // Indikator Halaman maroon
        Text(
          'Halaman ${halamanSaatIni + 1}/$totalHalaman',
          style: const TextStyle(
            fontSize: 13,
            color: WarnaAplikasi.utama,
            fontWeight: FontWeight.w600,
          ),
        ),

        // Tombol Next >
        _TombolNavigasiHalaman(
          label: 'Next',
          adalahBack: false,
          nonaktif: adalahTerakhir,
          saatDiketuk: adalahTerakhir ? null : saatBerikutnya,
        ),
      ],
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Tombol Navigasi Halaman Pill (< Back / Next >)
// ─────────────────────────────────────────────────────────────────────────────
class _TombolNavigasiHalaman extends StatelessWidget {
  final String label;
  final bool adalahBack;
  final bool nonaktif;
  final VoidCallback? saatDiketuk;

  const _TombolNavigasiHalaman({
    required this.label,
    required this.adalahBack,
    required this.nonaktif,
    this.saatDiketuk,
  });

  @override
  Widget build(BuildContext context) {
    const Color warnaAktif = WarnaAplikasi.utama;
    final Color warnaNonaktif = Colors.grey.shade400;

    final Color warnaEfektif = nonaktif ? warnaNonaktif : warnaAktif;
    final Color borderEfektif =
        nonaktif ? Colors.grey.shade300 : warnaAktif.withValues(alpha: 0.4);

    return GestureDetector(
      onTap: saatDiketuk,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 7),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: borderEfektif, width: 1.2),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            if (adalahBack) ...[
              Icon(
                Icons.arrow_circle_left_outlined,
                size: 16,
                color: warnaEfektif,
              ),
              const SizedBox(width: 4),
            ],
            Text(
              label,
              style: TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.bold,
                color: warnaEfektif,
              ),
            ),
            if (!adalahBack) ...[
              const SizedBox(width: 4),
              Icon(
                Icons.arrow_circle_right_outlined,
                size: 16,
                color: warnaEfektif,
              ),
            ],
          ],
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Tombol Selesai (Maroon)
// ─────────────────────────────────────────────────────────────────────────────
class _TombolSelesai extends StatelessWidget {
  final VoidCallback saatDitekan;
  const _TombolSelesai({required this.saatDitekan});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: double.infinity,
      height: 48,
      child: ElevatedButton(
        onPressed: saatDitekan,
        style: ElevatedButton.styleFrom(
          backgroundColor: WarnaAplikasi.utama,
          foregroundColor: Colors.white,
          elevation: 2,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
        ),
        child: const Text(
          'Selesai',
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            letterSpacing: 0.3,
          ),
        ),
      ),
    );
  }
}
