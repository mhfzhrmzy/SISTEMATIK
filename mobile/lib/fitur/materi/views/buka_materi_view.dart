import 'dart:async';

import 'package:flutter/material.dart';
import 'package:pdfx/pdfx.dart';
import 'package:pretest_posttest_app/fitur/materi/data/layanan_materi.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/header_tes.dart';
import 'package:pretest_posttest_app/fitur/materi/models/model_materi.dart';
import 'package:pretest_posttest_app/fitur/profil/views/profil_view.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/penyimpan_file.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/views/leaderboard_view.dart';

/// Halaman Buka Materi.
///
/// Fitur & Desain:
/// - Header maroon menggunakan [HeaderTes] yang sama persis dengan Pre-Test dan Post-Test:
///   full-bleed hingga status bar (ikon jam, baterai, wifi berwarna putih), posisi judul,
///   radius lengkungan 36px, dan bayangan yang selaras.
/// - Tombol Back (kembali ke beranda) FIXED di bawah header.
/// - Yang di-scroll HANYA dari "Deskripsi singkat" sampai kotak materi PDF.
/// - Kotak isi materi menampilkan file yang diunggah guru: PDF dirender
///   langsung di dalam aplikasi (bisa digeser atau memakai tombol halaman),
///   sedangkan PPT/PPTX/DOC/DOCX dibuka lewat aplikasi eksternal.
/// - Kotak deskripsi otomatis menyesuaikan tinggi teks (auto-height) tanpa terpotong.
/// - Tombol navigasi halaman (< Back | Halaman X/Y | Next >) dan tombol Selesai FIXED di bawah.
/// - NavigasiBawah fixed di bawah.
/// - Responsif penuh di mobile & tablet (portrait maupun landscape).
class BukaMateriView extends StatefulWidget {
  final ModelMateri materi;
  final LayananMateri? layananMateri;

  const BukaMateriView({
    super.key,
    required this.materi,
    this.layananMateri,
  });

  @override
  State<BukaMateriView> createState() => _BukaMateriViewState();
}

class _BukaMateriViewState extends State<BukaMateriView> {
  int _halamanSaatIni = 0;
  int _indeksFooter = 0;
  late final LayananMateri _layananMateri;
  late ModelMateri _materi;
  bool _memuatDetail = true;
  String? _pesanGalat;
  bool _sedangMenyimpan = false;

  PdfController? _pengendaliPdf;
  PdfDocument? _dokumenPdf;
  int _totalHalamanPdf = 0;
  bool _memuatFile = false;
  String? _pesanFile;

  bool get _punyaFile => (_materi.urlFile ?? '').trim().isNotEmpty;

  String get _namaFile {
    final uri = Uri.tryParse(_materi.urlFile ?? '');
    if (uri != null && uri.pathSegments.isNotEmpty) {
      return uri.pathSegments.last;
    }
    return 'materi_${_materi.id}';
  }

  String get _ekstensiFile {
    final nama = _namaFile.toLowerCase();
    final titik = nama.lastIndexOf('.');
    return titik == -1 ? '' : nama.substring(titik + 1);
  }

  bool get _filePdf => _ekstensiFile == 'pdf';

  String get _labelFile {
    switch (_ekstensiFile) {
      case 'pdf':
        return 'Dokumen PDF';
      case 'ppt':
      case 'pptx':
        return 'Presentasi PowerPoint';
      case 'doc':
      case 'docx':
        return 'Dokumen Word';
      default:
        return 'File materi';
    }
  }

  bool get _modeTeks => !_punyaFile && _materi.halamanKonten.isNotEmpty;

  int get _totalHalaman {
    if (_filePdf && _totalHalamanPdf > 0) return _totalHalamanPdf;
    if (_modeTeks) return _materi.halamanKonten.length;
    return 1;
  }

  bool get _tampilkanNavigasi =>
      _modeTeks || (_filePdf && _pengendaliPdf != null);

  bool get _adalahPertama => _halamanSaatIni <= 0;
  bool get _adalahTerakhir => _halamanSaatIni >= _totalHalaman - 1;

  String get _kontenHalamanIni {
    if (_halamanSaatIni >= 0 &&
        _halamanSaatIni < _materi.halamanKonten.length) {
      return _materi.halamanKonten[_halamanSaatIni];
    }
    return '';
  }

  @override
  void initState() {
    super.initState();
    _materi = widget.materi;
    _layananMateri = widget.layananMateri ?? LayananMateri();
    _muatDetail();
  }

  @override
  void dispose() {
    _pengendaliPdf?.dispose();
    unawaited(_dokumenPdf?.close());
    super.dispose();
  }

  Future<void> _muatFileMateri() async {
    if (!_filePdf) return;

    setState(() {
      _memuatFile = true;
      _pesanFile = null;
    });

    try {
      final bytes = await _layananMateri.unduhFile(_materi.id);
      final dokumen = await PdfDocument.openData(bytes);

      if (!mounted) {
        await dokumen.close();
        return;
      }

      final pengendaliLama = _pengendaliPdf;
      final dokumenLama = _dokumenPdf;
      setState(() {
        _dokumenPdf = dokumen;
        _pengendaliPdf = PdfController(
          document: Future<PdfDocument>.value(dokumen),
        );
        _totalHalamanPdf = dokumen.pagesCount;
        _halamanSaatIni = 0;
        _memuatFile = false;
      });

      // Bersihkan dokumen lama setelah frame baru selesai dibangun.
      WidgetsBinding.instance.addPostFrameCallback((_) {
        pengendaliLama?.dispose();
        if (dokumenLama != null && !identical(dokumenLama, dokumen)) {
          unawaited(dokumenLama.close());
        }
      });
    } on KesalahanApi catch (error) {
      if (!mounted) return;
      setState(() {
        _pesanFile = error.pesan;
        _memuatFile = false;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _pesanFile = 'File PDF gagal dibuka. Pastikan file tidak rusak.';
        _memuatFile = false;
      });
    }
  }

  Future<void> _muatDetail() async {
    setState(() {
      _memuatDetail = true;
      _pesanGalat = null;
    });
    try {
      final detail = await _layananMateri.detail(widget.materi.id);
      if (!mounted) return;
      if (detail.hasPretest && !detail.pretestCompleted) {
        setState(() {
          _pesanGalat =
              'Selesaikan Pre-Test terlebih dahulu sebelum dapat membuka bahan ajar ini.';
          _memuatDetail = false;
        });
        return;
      }
      setState(() {
        _materi = detail;
        _halamanSaatIni = 0;
        _memuatDetail = false;
      });
      if (_filePdf) {
        await _muatFileMateri();
      }
    } on KesalahanApi catch (error) {
      if (!mounted) return;
      setState(() {
        _pesanGalat = error.pesan;
        _memuatDetail = false;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _pesanGalat = 'Gagal memuat detail materi.';
        _memuatDetail = false;
      });
    }
  }

  Future<void> _unduhFileMateri() async {
    try {
      final bytes = await _layananMateri.unduhFile(_materi.id);
      final url = Uri.tryParse(_materi.urlFile ?? '');
      final namaFile = url != null && url.pathSegments.isNotEmpty
          ? url.pathSegments.last
          : 'materi_${_materi.id}';
      await simpanFile(bytes, namaFile);
    } on KesalahanApi catch (error) {
      if (mounted) _tampilkanPesan(error.pesan);
    } catch (_) {
      if (mounted) _tampilkanPesan('File materi gagal diunduh.');
    }
  }

  void _tampilkanPesan(String pesan) {
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(SnackBar(content: Text(pesan)));
  }

  Widget _bangunKontenMateri(double tinggi) {
    if (_punyaFile && _filePdf) {
      return _KotakViewerPdf(
        tinggi: tinggi,
        memuat: _memuatFile,
        pesan: _pesanFile,
        pengendali: _pengendaliPdf,
        kunciDokumen: _dokumenPdf,
        saatUlangi: _muatFileMateri,
        saatHalamanBerubah: (halaman) {
          final indeks = halaman - 1;
          if (!mounted || indeks == _halamanSaatIni) return;
          setState(() => _halamanSaatIni = indeks);
        },
      );
    }

    if (_punyaFile) {
      return _KartuFileEksternal(
        tinggi: tinggi,
        labelFile: _labelFile,
        namaFile: _namaFile,
        saatBuka: _unduhFileMateri,
      );
    }

    if (_modeTeks) {
      return _KotakKontenTeks(tinggi: tinggi, konten: _kontenHalamanIni);
    }

    return _KotakInfoFile(tinggi: tinggi);
  }

  void halamanSebelumnya() {
    if (_adalahPertama) return;
    if (_filePdf && _pengendaliPdf != null) {
      _pengendaliPdf!.animateToPage(
        _halamanSaatIni,
        duration: const Duration(milliseconds: 220),
        curve: Curves.easeOut,
      );
      return;
    }
    setState(() => _halamanSaatIni--);
  }

  void halamanBerikutnya() {
    if (_adalahTerakhir) return;
    if (_filePdf && _pengendaliPdf != null) {
      _pengendaliPdf!.animateToPage(
        _halamanSaatIni + 2,
        duration: const Duration(milliseconds: 220),
        curve: Curves.easeOut,
      );
      return;
    }
    setState(() => _halamanSaatIni++);
  }

  Future<void> selesai() async {
    if (_sedangMenyimpan) return;
    setState(() => _sedangMenyimpan = true);

    try {
      final isSelesai = await _layananMateri.tandaiSelesai(_materi.id);
      if (!mounted) return;
      Navigator.of(context).pop(isSelesai);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            isSelesai
                ? '${_materi.judul} selesai dipelajari!'
                : 'Status selesai pada ${_materi.judul} dibatalkan.',
          ),
          backgroundColor: WarnaAplikasi.utama,
        ),
      );
    } on KesalahanApi catch (error) {
      if (!mounted) return;
      setState(() => _sedangMenyimpan = false);
      _tampilkanPesan(error.pesan);
    } catch (_) {
      if (!mounted) return;
      setState(() => _sedangMenyimpan = false);
      _tampilkanPesan('Gagal memperbarui status materi.');
    }
  }

  Future<void> saatFooterDiketuk(int idx) async {
    if (idx == 0) {
      Navigator.of(context).pop();
    } else if (idx == 1) {
      setState(() => _indeksFooter = idx);
      await Navigator.of(context).push(
        MaterialPageRoute(builder: (_) => const LeaderboardView()),
      );
      if (!mounted) return;
      setState(() => _indeksFooter = 0);
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
        saatDiketuk: saatFooterDiketuk,
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
                HeaderTes(judul: _materi.judul),

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
                        if (_pesanGalat != null)
                          Card(
                            child: ListTile(
                              title: Text(_pesanGalat!),
                              trailing: IconButton(
                                onPressed: _muatDetail,
                                icon: const Icon(Icons.refresh),
                              ),
                            ),
                          ),
                        if (_memuatDetail) const LinearProgressIndicator(),
                        // Label Deskripsi singkat
                        const _LabelDeskripsi(),
                        const SizedBox(height: 6),

                        // Container deskripsi (auto-height, responsif terhadap panjang teks)
                        _KotakDeskripsi(deskripsi: _materi.deskripsi),
                        if (_punyaFile && _filePdf) ...[
                          const SizedBox(height: 6),
                          Align(
                            alignment: Alignment.centerRight,
                            child: TextButton.icon(
                              onPressed: _unduhFileMateri,
                              icon: const Icon(Icons.download, size: 18),
                              label: const Text('Unduh materi'),
                            ),
                          ),
                        ],
                        const SizedBox(height: 6),

                        _bangunKontenMateri(tinggiA4),
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
                      if (_tampilkanNavigasi) ...[
                        _BarisKontrolNavigasi(
                          halamanSaatIni: _halamanSaatIni,
                          totalHalaman: _totalHalaman,
                          adalahPertama: _adalahPertama,
                          adalahTerakhir: _adalahTerakhir,
                          saatSebelumnya: halamanSebelumnya,
                          saatBerikutnya: halamanBerikutnya,
                        ),
                        const SizedBox(height: 10),
                      ],

                      // Tombol Selesai maroon
                      _TombolSelesai(saatDitekan: selesai),
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
// Viewer PDF di dalam aplikasi (ukuran A4)
// ─────────────────────────────────────────────────────────────────────────────
class _KotakViewerPdf extends StatelessWidget {
  final double tinggi;
  final bool memuat;
  final String? pesan;
  final PdfController? pengendali;
  final PdfDocument? kunciDokumen;
  final VoidCallback saatUlangi;
  final ValueChanged<int> saatHalamanBerubah;

  const _KotakViewerPdf({
    required this.tinggi,
    required this.memuat,
    required this.pesan,
    required this.pengendali,
    required this.kunciDokumen,
    required this.saatUlangi,
    required this.saatHalamanBerubah,
  });

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
      clipBehavior: Clip.antiAlias,
      child: _isi(),
    );
  }

  Widget _isi() {
    if (memuat) {
      return const Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            SizedBox(
              width: 28,
              height: 28,
              child: CircularProgressIndicator(strokeWidth: 2.5),
            ),
            SizedBox(height: 12),
            Text('Memuat file PDF...'),
          ],
        ),
      );
    }

    if (pesan != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(
                Icons.description_outlined,
                size: 40,
                color: Colors.grey.shade500,
              ),
              const SizedBox(height: 10),
              Text(
                pesan!,
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 13,
                  color: Colors.grey.shade700,
                  height: 1.4,
                ),
              ),
              const SizedBox(height: 12),
              OutlinedButton.icon(
                onPressed: saatUlangi,
                icon: const Icon(Icons.refresh),
                label: const Text('Coba lagi'),
              ),
            ],
          ),
        ),
      );
    }

    if (pengendali == null) {
      return const Center(child: CircularProgressIndicator(strokeWidth: 2.5));
    }

    return PdfView(
      key: ObjectKey(kunciDokumen),
      controller: pengendali!,
      onPageChanged: saatHalamanBerubah,
      builders: PdfViewBuilders<DefaultBuilderOptions>(
        options: const DefaultBuilderOptions(),
        documentLoaderBuilder: (_) => const Center(
          child: CircularProgressIndicator(strokeWidth: 2.5),
        ),
        pageLoaderBuilder: (_) => const Center(
          child: CircularProgressIndicator(strokeWidth: 2.5),
        ),
        errorBuilder: (_, error) => Center(
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Text(
              'Gagal menampilkan PDF: $error',
              textAlign: TextAlign.center,
            ),
          ),
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Kartu file non-PDF (PPT/DOC) — dibuka lewat aplikasi eksternal
// ─────────────────────────────────────────────────────────────────────────────
class _KartuFileEksternal extends StatelessWidget {
  final double tinggi;
  final String labelFile;
  final String namaFile;
  final VoidCallback saatBuka;

  const _KartuFileEksternal({
    required this.tinggi,
    required this.labelFile,
    required this.namaFile,
    required this.saatBuka,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      height: tinggi,
      padding: const EdgeInsets.all(20),
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
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(
            Icons.insert_drive_file_outlined,
            size: 56,
            color: WarnaAplikasi.utama,
          ),
          const SizedBox(height: 12),
          Text(
            labelFile,
            textAlign: TextAlign.center,
            style: const TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.bold,
              color: WarnaAplikasi.utama,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            namaFile,
            textAlign: TextAlign.center,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
            style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
          ),
          const SizedBox(height: 16),
          FilledButton.icon(
            onPressed: saatBuka,
            icon: const Icon(Icons.open_in_new),
            label: const Text('Buka File Materi'),
            style: FilledButton.styleFrom(
              backgroundColor: WarnaAplikasi.utama,
              foregroundColor: Colors.white,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'File dibuka dengan aplikasi yang terpasang di perangkat.',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 11.5, color: Colors.grey.shade600),
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Kotak teks cadangan (hanya untuk data dummy/lama tanpa file)
// ─────────────────────────────────────────────────────────────────────────────
class _KotakKontenTeks extends StatelessWidget {
  final double tinggi;
  final String konten;

  const _KotakKontenTeks({required this.tinggi, required this.konten});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      height: tinggi,
      padding: const EdgeInsets.all(16),
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
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        physics: const BouncingScrollPhysics(),
        child: SelectableText(
          konten.isEmpty ? 'Konten materi tidak tersedia.' : konten,
          style: TextStyle(
            fontSize: 13.5,
            color: Colors.grey.shade800,
            height: 1.6,
          ),
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Kotak informasi saat guru belum mengunggah file
// ─────────────────────────────────────────────────────────────────────────────
class _KotakInfoFile extends StatelessWidget {
  final double tinggi;

  const _KotakInfoFile({required this.tinggi});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      height: tinggi,
      padding: const EdgeInsets.all(20),
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
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.cloud_off_outlined,
            size: 48,
            color: Colors.grey.shade500,
          ),
          const SizedBox(height: 12),
          Text(
            'Guru belum mengunggah file materi untuk modul ini.',
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 13.5,
              fontWeight: FontWeight.w600,
              color: Colors.grey.shade700,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            'Silakan hubungi guru pengampu untuk mendapatkan file materinya.',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
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
