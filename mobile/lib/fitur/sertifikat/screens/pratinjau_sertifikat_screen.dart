import 'dart:typed_data';

import 'package:flutter/material.dart';
import 'package:pdfx/pdfx.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/services/api_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/item_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/jenis_file_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/widgets/dialog_konfirmasi_sertifikat.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/penyimpan_file.dart';

/// Halaman pratinjau sertifikat (PDF atau gambar) di dalam aplikasi.
///
/// File diunduh lebih dulu lewat API agar tetap memakai token siswa,
/// lalu dirender langsung dari byte: [PdfView] (pdfx) untuk PDF dan
/// [Image.memory] untuk gambar PNG/JPG.
class PratinjauSertifikatScreen extends StatefulWidget {
  const PratinjauSertifikatScreen({
    super.key,
    required this.item,
    this.layananSertifikat,
  });

  final ItemSertifikat item;
  final LayananSertifikat? layananSertifikat;

  @override
  State<PratinjauSertifikatScreen> createState() =>
      _PratinjauSertifikatScreenState();
}

class _PratinjauSertifikatScreenState extends State<PratinjauSertifikatScreen> {
  late final LayananSertifikat _layananSertifikat;

  Uint8List? _dataFile;
  JenisFileSertifikat _jenisFile = JenisFileSertifikat.tidakDiketahui;
  String? _pesanGalat;
  bool _memuat = true;
  bool _sedangMengunduh = false;

  PdfDocument? _dokumenPdf;
  PdfController? _pengendaliPdf;
  bool _memuatPdf = true;
  String? _galatPdf;

  @override
  void initState() {
    super.initState();
    _layananSertifikat = widget.layananSertifikat ?? LayananSertifikat();
    _muatPratinjau();
  }

  @override
  void dispose() {
    _pengendaliPdf?.dispose();
    _dokumenPdf?.close();
    super.dispose();
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
      final jenis = deteksiJenisFile(bytes, namaFile: widget.item.urlFile);
      if (!mounted) return;
      if (!jenis.didukung) {
        setState(() {
          _memuat = false;
          _pesanGalat = 'Format file sertifikat tidak didukung.';
        });
        return;
      }
      setState(() {
        _dataFile = bytes;
        _jenisFile = jenis;
        _memuat = false;
      });

      if (jenis == JenisFileSertifikat.pdf) {
        await _muatDokumenPdf(bytes);
      }
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

  Future<void> _muatDokumenPdf(Uint8List bytes) async {
    setState(() {
      _memuatPdf = true;
      _galatPdf = null;
    });

    try {
      final dokumen = await PdfDocument.openData(bytes);
      if (!mounted) {
        await dokumen.close();
        return;
      }
      _dokumenPdf?.close();
      _pengendaliPdf?.dispose();
      setState(() {
        _dokumenPdf = dokumen;
        _pengendaliPdf = PdfController(document: Future.value(dokumen));
        _memuatPdf = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _galatPdf = 'Gagal membuka dokumen PDF: $e';
        _memuatPdf = false;
      });
    }
  }

  Future<void> _unduhSertifikat() async {
    final id = widget.item.id;
    if (id == null) {
      _tampilkanPesan('ID sertifikat tidak tersedia.');
      return;
    }
    final konfirmasi = await konfirmasiAksiSertifikat(
      context,
      judulDialog: 'Unduh Sertifikat?',
      pesan: 'Sertifikat "${widget.item.judul}" akan diunduh ke perangkat Anda.',
      labelAksi: 'Unduh',
    );
    if (!konfirmasi || !mounted) return;

    setState(() => _sedangMengunduh = true);
    try {
      final bytes = _dataFile ?? await _layananSertifikat.unduh(id);
      final jenis = _jenisFile.didukung
          ? _jenisFile
          : deteksiJenisFile(bytes, namaFile: widget.item.urlFile);
      await simpanFile(bytes, 'sertifikat_$id.${jenis.ekstensi}');
      if (mounted) _tampilkanPesan('Sertifikat berhasil diunduh.');
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
      ),
      body: _bangunIsi(),
    );
  }

  Widget _bangunIsi() {
    if (_memuat) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_pesanGalat != null || _dataFile == null) {
      return _layarGalat(_pesanGalat ?? 'Pratinjau sertifikat tidak tersedia.');
    }

    if (_jenisFile.adalahGambar) {
      return InteractiveViewer(
        minScale: 1,
        maxScale: 5,
        child: Center(
          child: Image.memory(
            _dataFile!,
            fit: BoxFit.contain,
            errorBuilder: (context, error, stackTrace) => const Center(
              child: Text('Gambar sertifikat gagal ditampilkan.'),
            ),
          ),
        ),
      );
    }

    if (_memuatPdf) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_galatPdf != null) {
      return _layarGalat(_galatPdf!);
    }

    if (_pengendaliPdf == null) {
      return const Center(
        child: Text(
          'Tidak ada halaman PDF yang dapat ditampilkan.',
          style: TextStyle(fontSize: 13, color: Colors.grey),
        ),
      );
    }

    return PdfView(
      controller: _pengendaliPdf!,
      key: ObjectKey(_dokumenPdf),
      scrollDirection: Axis.vertical,
      builders: PdfViewBuilders<DefaultBuilderOptions>(
        options: const DefaultBuilderOptions(),
        documentLoaderBuilder: (_) =>
            const Center(child: CircularProgressIndicator(strokeWidth: 2.5)),
        pageLoaderBuilder: (_) =>
            const Center(child: CircularProgressIndicator(strokeWidth: 2.5)),
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

  Widget _layarGalat(String pesan) {
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
              pesan,
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
                        color:
                            WarnaAplikasi.utama.withValues(alpha: 0.35)),
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
                            valueColor: AlwaysStoppedAnimation<Color>(
                                Colors.white),
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
}
