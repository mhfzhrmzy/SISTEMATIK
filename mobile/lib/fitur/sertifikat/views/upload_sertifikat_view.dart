import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:image_picker/image_picker.dart';
import 'package:pretest_posttest_app/fitur/beranda/views/beranda_view.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/views/leaderboard_view.dart';
import 'package:pretest_posttest_app/fitur/profil/views/profil_view.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/data/layanan_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/item_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/tema_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/views/sertifikat_view.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/views/upload_sertifikat_form.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';

/// Halaman form upload sertifikat.
///
/// Berperan sebagai [UploadSertifikatFormController] untuk widget form.
class UploadSertifikatView extends StatefulWidget {
  final List<ItemSertifikat> daftarSertifikat;
  final ValueChanged<List<ItemSertifikat>>? saatDaftarBerubah;

  const UploadSertifikatView({
    super.key,
    required this.daftarSertifikat,
    this.saatDaftarBerubah,
  });

  @override
  State<UploadSertifikatView> createState() => _UploadSertifikatViewState();
}

class _UploadSertifikatViewState extends State<UploadSertifikatView>
    with SingleTickerProviderStateMixin
    implements UploadSertifikatFormController {
  final _kunciForm = GlobalKey<FormState>();
  final _kontrolerJudul = TextEditingController();
  final _kontrolerPenerbit = TextEditingController();
  final LayananSertifikat _layanan = LayananSertifikat();
  late List<ItemSertifikat> _daftarLokal;
  late final AnimationController _kontrolerAnim;
  late final Animation<double> _animFade;

  @override
  DateTime? tanggalTerpilih;
  @override
  String? namaFileTerpilih;
  Uint8List? _bytesFile;
  @override
  bool sedangUnggah = false;
  int _indeksEdit = -1;

  @override
  GlobalKey<FormState> get kunciForm => _kunciForm;
  @override
  TextEditingController get kontrolerJudul => _kontrolerJudul;
  @override
  TextEditingController get kontrolerPenerbit => _kontrolerPenerbit;
  @override
  bool get sedangEdit => _indeksEdit >= 0;

  @override
  void initState() {
    super.initState();
    _daftarLokal = List.from(widget.daftarSertifikat);
    _kontrolerAnim = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 350),
    );
    _animFade = CurvedAnimation(
      parent: _kontrolerAnim,
      curve: Curves.easeInOut,
    );
    _kontrolerAnim.forward();
  }

  @override
  void dispose() {
    _kontrolerJudul.dispose();
    _kontrolerPenerbit.dispose();
    _kontrolerAnim.dispose();
    super.dispose();
  }

  // ── UploadSertifikatFormController ────────────────────────────────────────

  @override
  Future<void> pilihTanggal() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: tanggalTerpilih ?? DateTime.now(),
      firstDate: DateTime(2015),
      lastDate: DateTime(2030),
      builder: _temaTanggal,
    );
    if (picked != null) setState(() => tanggalTerpilih = picked);
  }

  @override
  Future<void> pilihFile() async {
    try {
      final file = await ImagePicker().pickImage(source: ImageSource.gallery);
      if (file == null) return;

      final bytes = await file.readAsBytes();
      if (!mounted) return;

      // Hanya PDF yang boleh: cek ekstensi + tanda "magic" PDF (%PDF).
      if (!_apakahPdf(file.name, bytes)) {
        _snackBar(
          'Format tidak valid. Unggah sertifikat berformat PDF.',
          warna: Colors.red.shade700,
        );
        return;
      }

      setState(() {
        _bytesFile = bytes;
        namaFileTerpilih = file.name;
      });
      _snackBar('File "${file.name}" berhasil dipilih');
    } catch (e) {
      if (!mounted) return;
      _snackBar('Gagal memilih file: $e', warna: Colors.red.shade700);
    }
  }

  /// Validasi client-side: file harus PDF (ekstensi + magic bytes `%PDF`).
  bool _apakahPdf(String nama, Uint8List bytes) {
    final akhiran = nama.toLowerCase().split('.').last;
    if (akhiran != 'pdf') return false;
    // Magic bytes PDF: 0x25 0x50 0x44 0x46 ("%PDF").
    if (bytes.length < 4) return false;
    return bytes[0] == 0x25 &&
        bytes[1] == 0x50 &&
        bytes[2] == 0x44 &&
        bytes[3] == 0x46;
  }

  @override
  void hapusTanggal() => setState(() => tanggalTerpilih = null);

  @override
  void hapusFile() =>
      setState(() {
        namaFileTerpilih = null;
        _bytesFile = null;
      });

  @override
  void batalEdit() {
    _kunciForm.currentState?.reset();
    _kontrolerJudul.clear();
    _kontrolerPenerbit.clear();
    setState(() {
      tanggalTerpilih = null;
      namaFileTerpilih = null;
      _bytesFile = null;
      _indeksEdit = -1;
    });
  }

  @override
  Future<void> simpan() => _simpan();

  // ── Logika form ───────────────────────────────────────────────────────────

  Future<void> _simpan() async {
    if (!(_kunciForm.currentState?.validate() ?? false)) return;
    if (tanggalTerpilih == null) {
      _snackBar('Tanggal diterbitkan wajib diisi.', warna: Colors.red.shade700);
      return;
    }
    if (_bytesFile == null) {
      _snackBar('File sertifikat wajib diunggah.', warna: Colors.red.shade700);
      return;
    }

    setState(() => sedangUnggah = true);
    try {
      final itemBaru = await _layanan.unggah(
        judul: _kontrolerJudul.text.trim(),
        penerbit: _kontrolerPenerbit.text.trim(),
        tanggalTerbit: tanggalTerpilih,
        namaFile: namaFileTerpilih ?? 'sertifikat.pdf',
        bytes: _bytesFile!,
      );
      setState(() {
        _daftarLokal.insert(0, itemBaru);
        sedangUnggah = false;
      });

      widget.saatDaftarBerubah?.call(List.from(_daftarLokal));
      _kunciForm.currentState?.reset();
      _kontrolerJudul.clear();
      _kontrolerPenerbit.clear();
      setState(() {
        tanggalTerpilih = null;
        namaFileTerpilih = null;
        _bytesFile = null;
      });
      if (!mounted) return;
      _snackBar('Sertifikat berhasil diunggah ke server!');
    } catch (e) {
      if (!mounted) return;
      setState(() => sedangUnggah = false);
      _snackBar('Gagal mengunggah: $e', warna: Colors.red.shade700);
    }
  }

  // ── Navigasi & utilitas ───────────────────────────────────────────────────

  void _kembali() {
    Navigator.of(context).pushReplacement(
      MaterialPageRoute(
        builder: (_) => SertifikatView(
          daftarAwal: List.from(_daftarLokal),
          layananSertifikat: _layanan,
        ),
      ),
    );
  }

  void _saatFooterDiketuk(int idx) {
    if (idx == 2) {
      Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (_) => const ProfilView()));
    } else if (idx == 0) {
      Navigator.of(context)
          .pushAndRemoveUntil(
        MaterialPageRoute(builder: (_) => const BerandaView()),
        (r) => false,
      );
    } else if (idx == 1) {
      Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (_) => const LeaderboardView()));
    }
  }

  void _snackBar(String pesan, {Color? warna}) {
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(
        SnackBar(
          content: Text(pesan),
          backgroundColor: warna,
          duration: const Duration(seconds: 2),
        ),
      );
  }

  Widget _temaTanggal(BuildContext context, Widget? child) {
    return Theme(
      data: Theme.of(context).copyWith(
        colorScheme: const ColorScheme.light(
          primary: TemaSertifikat.maroon,
          onPrimary: Colors.white,
          surface: Colors.white,
          onSurface: TemaSertifikat.maroon,
        ),
      ),
      child: child!,
    );
  }

  // ── Build ─────────────────────────────────────────────────────────────────

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      bottomNavigationBar:
          NavigasiBawah(indeksSaatIni: 2, saatDiketuk: _saatFooterDiketuk),
      body: AnnotatedRegion<SystemUiOverlayStyle>(
        value: SystemUiOverlayStyle.light
            .copyWith(statusBarColor: Colors.transparent),
        child: SafeArea(
          top: false,
          bottom: false,
          child: Column(
            children: [
              const _HeaderUpload(),
              Expanded(
                child: FadeTransition(
                  opacity: _animFade,
                  child: LayoutBuilder(
                    builder: (ctx, c) {
                      final hp = (c.maxWidth * 0.075).clamp(22.0, 40.0);
                      return SingleChildScrollView(
                        physics: const BouncingScrollPhysics(),
                        padding: EdgeInsets.fromLTRB(hp, 16, hp, 28),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            _pillKembali(),
                            const SizedBox(height: 18),
                            _judulSeksiForm(),
                            const SizedBox(height: 12),
                            UploadSertifikatForm(c: this),
                          ],
                        ),
                      );
                    },
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _pillKembali() {
    return InkWell(
      onTap: _kembali,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding:
            const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
        decoration: TemaSertifikat.kotak(
          warnaGaris: TemaSertifikat.maroon.withValues(alpha: 0.35),
          radius: 20,
        ),
        child: const Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.arrow_back_rounded, size: 14, color: TemaSertifikat.maroon),
            SizedBox(width: 4),
            Text(
              'Back',
              style: TextStyle(
                color: TemaSertifikat.maroon,
                fontSize: 12,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _judulSeksiForm() {
    return Row(
      children: [
        const Icon(
            Icons.workspace_premium_rounded, color: TemaSertifikat.maroon, size: 18),
        const SizedBox(width: 8),
        Text(
          sedangEdit ? 'Edit Sertifikat' : 'Form Upload Sertifikat',
          style: const TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.bold,
            color: TemaSertifikat.maroon,
          ),
        ),
      ],
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Header
// ─────────────────────────────────────────────────────────────────────────────
class _HeaderUpload extends StatelessWidget {
  const _HeaderUpload();

  @override
  Widget build(BuildContext context) {
    final mq = MediaQuery.of(context);
    final hp = (mq.size.width * 0.075).clamp(22.0, 40.0);
    return Material(
      elevation: 4,
      shadowColor: WarnaAplikasi.bayanganLembut,
      color: WarnaAplikasi.utama,
      borderRadius: const BorderRadius.only(
        bottomLeft: Radius.circular(32),
        bottomRight: Radius.circular(32),
      ),
      child: Stack(
        children: [
          Positioned(top: -50, right: -40, child: _lingkaran(180)),
          Positioned(bottom: -60, left: -40, child: _lingkaran(150)),
          Padding(
            padding: EdgeInsets.only(
              top: mq.viewPadding.top + 20,
              bottom: 22,
              left: hp,
              right: hp,
            ),
            child: const Center(
              child: Text(
                'Upload Sertifikat Baru',
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
        ],
      ),
    );
  }

  static Widget _lingkaran(double s) => Container(
        width: s,
        height: s,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: Colors.white.withValues(alpha: 0.05),
        ),
      );
}
