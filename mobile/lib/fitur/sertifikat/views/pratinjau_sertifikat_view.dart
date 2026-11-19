import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_pdfview/flutter_pdfview.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/data/layanan_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/item_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/widgets/pratinjau_pdf.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/penyimpan_file.dart';

/// Halaman pratinjau PDF sertifikat di dalam aplikasi.
///
/// File diunduh lebih dulu lewat API agar tetap memakai token siswa,
/// lalu dirender langsung dari byte PDF memakai [PDFView].
class PratinjauSertifikatView extends StatefulWidget {
  const PratinjauSertifikatView({
    super.key,
    required this.item,
    this.layananSertifikat,
  });

  final ItemSertifikat item;
  final LayananSertifikat? layananSertifikat;

  @override
  State<PratinjauSertifikatView> createState() =>
      _PratinjauSertifikatViewState();
}

class _PratinjauSertifikatViewState extends State<PratinjauSertifikatView> {
  late final LayananSertifikat _layananSertifikat;

  Uint8List? _dataPdf;
  String? _pesanGalat;
  bool _memuat = true;
  bool _sedangMengunduh = false;
  int _halamanSaatIni = 1;
  int _jumlahHalaman = 0;

  @override
  void initState() {
    super.initState();
    _layananSertifikat = widget.layananSertifikat ?? LayananSertifikat();
    _muatPratinjau();
  }

  Future<void> _muatPratinjau() async {
    setState(() {
      _memuat = true;
      _pesanGalat = null;
    });

    final id = widget.item.id;
    if (id == null) {
      setState(() {
        _memuat = false;
        _pesanGalat = 'ID sertifikat tidak tersedia.';
      });
      return;
    }

    try {
      final bytes = await _layananSertifikat.unduh(id);
      if (!mounted) return;
      setState(() {
        _dataPdf = bytes;
        _memuat = false;
      });
    } on KesalahanApi catch (error) {
      if (!mounted) return;
      setState(() {
        _memuat = false;
        _pesanGalat = error.pesan;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _memuat = false;
        _pesanGalat = 'Gagal memuat pratinjau sertifikat.';
      });
    }
  }

  Future<void> _unduhSertifikat() async {
    final id = widget.item.id;
    if (id == null) {
      _tampilkanPesan('ID sertifikat tidak tersedia.');
      return;
    }
    setState(() => _sedangMengunduh = true);
    try {
      final bytes = await _layananSertifikat.unduh(id);
      await simpanFile(bytes, 'sertifikat_$id.pdf');
    } on KesalahanApi catch (error) {
      if (mounted) _tampilkanPesan(error.pesan);
    } catch (_) {
      if (mounted) _tampilkanPesan('Sertifikat gagal diunduh.');
    } finally {
      if (mounted) setState(() => _sedangMengunduh = false);
    }
  }

  void _tampilkanPesan(String pesan) {
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(SnackBar(content: Text(pesan)));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: WarnaAplikasi.utama,
        foregroundColor: Colors.white,
        elevation: 0,
        title: Text(
          widget.item.judul,
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
          style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
        ),
        actions: [
          if (_jumlahHalaman > 1)
            Center(
              child: Padding(
                padding: const EdgeInsets.only(right: 16),
                child: Text(
                  '$_halamanSaatIni / $_jumlahHalaman',
                  style: const TextStyle(
                      fontSize: 12.5, fontWeight: FontWeight.w600),
                ),
              ),
            ),
        ],
      ),
      body: _bangunIsi(),
    );
  }

  Widget _bangunIsi() {
    if (_memuat) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_pesanGalat != null || _dataPdf == null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(Icons.picture_as_pdf_outlined,
                  size: 48, color: WarnaAplikasi.utama),
              const SizedBox(height: 12),
              Text(
                _pesanGalat ?? 'Pratinjau sertifikat tidak tersedia.',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 13,
                  color: WarnaAplikasi.utama.withValues(alpha: 0.75),
                ),
              ),
              const SizedBox(height: 16),
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  OutlinedButton(
                    onPressed: _muatPratinjau,
                    style: OutlinedButton.styleFrom(
                      foregroundColor: WarnaAplikasi.utama,
                      side: BorderSide(
                          color: WarnaAplikasi.utama.withValues(alpha: 0.35)),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10)),
                    ),
                    child: const Text('Coba lagi'),
                  ),
                  const SizedBox(width: 10),
                  ElevatedButton(
                    onPressed: _sedangMengunduh ? null : _unduhSertifikat,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: WarnaAplikasi.utama,
                      foregroundColor: Colors.white,
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10)),
                    ),
                    child: _sedangMengunduh
                        ? const SizedBox(
                            width: 16,
                            height: 16,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              valueColor:
                                  AlwaysStoppedAnimation<Color>(Colors.white),
                            ),
                          )
                        : const Text('Unduh'),
                  ),
                ],
              ),
            ],
          ),
        ),
      );
    }

    if (kIsWeb) {
      return PratinjauPdfWeb(data: _dataPdf!);
    }

    return PDFView(
      pdfData: _dataPdf,
      enableSwipe: true,
      swipeHorizontal: false,
      autoSpacing: true,
      pageFling: true,
      pageSnap: true,
      fitEachPage: true,
      onRender: (jumlah) {
        if (!mounted) return;
        setState(() => _jumlahHalaman = jumlah ?? 0);
      },
      onPageChanged: (halaman, jumlah) {
        if (!mounted) return;
        setState(() {
          _halamanSaatIni = (halaman ?? 0) + 1;
          _jumlahHalaman = jumlah ?? _jumlahHalaman;
        });
      },
      onError: (galat) {
        if (!mounted) return;
        setState(() => _pesanGalat = 'Gagal menampilkan PDF: $galat');
      },
    );
  }
}
