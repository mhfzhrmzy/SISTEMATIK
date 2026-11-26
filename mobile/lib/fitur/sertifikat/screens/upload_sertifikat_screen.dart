import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:pretest_posttest_app/fitur/beranda/screens/beranda_screen.dart';
import 'package:pretest_posttest_app/inti/layanan/pemilih_file.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/screens/leaderboard_screen.dart';
import 'package:pretest_posttest_app/fitur/profil/screens/profil_screen.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/services/api_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/item_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/jenis_file_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/screens/sertifikat_screen.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/screens/upload_sertifikat_form_screen.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';

/// Halaman form upload sertifikat.
class UploadSertifikatScreen extends StatefulWidget {
  final List<ItemSertifikat> daftarSertifikat;
  final ValueChanged<List<ItemSertifikat>>? saatDaftarBerubah;

  const UploadSertifikatScreen({
    super.key,
    required this.daftarSertifikat,
    this.saatDaftarBerubah,
  });

  @override
  State<UploadSertifikatScreen> createState() => _UploadSertifikatScreenState();
}

class _UploadSertifikatScreenState extends State<UploadSertifikatScreen>
    with SingleTickerProviderStateMixin {
  final _kunciForm = GlobalKey<FormState>();
  final _kontrolerJudul = TextEditingController();
  final _kontrolerPenerbit = TextEditingController();
  final LayananSertifikat _layanan = LayananSertifikat();
  late List<ItemSertifikat> _daftarLokal;
  late final AnimationController _kontrolerAnim;
  late final Animation<double> _animFade;

  DateTime? _tanggalTerpilih;
  String? _namaFileTerpilih;
  Uint8List? _bytesFile;
  bool _sedangUnggah = false;

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

  // ── Aksi form ──────────────────────────────────────────────────────────────

  void _pilihTanggal() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _tanggalTerpilih ?? DateTime.now(),
      firstDate: DateTime(2015),
      lastDate: DateTime.now(),
      builder: _temaTanggal,
    );
    if (picked != null) setState(() => _tanggalTerpilih = picked);
  }

  void _pilihFile() async {
    try {
      final hasil = await pilihFileSertifikat();
      if (hasil == null) return;

      final nama = hasil['name'] as String;
      final bytes = hasil['bytes'] as Uint8List?;
      if (!mounted) return;

      if (bytes == null || bytes.isEmpty) {
        _snackBar('Gagal membaca file yang dipilih.',
            warna: Colors.red.shade700);
        return;
      }

      final jenis = deteksiJenisFile(bytes, namaFile: nama);
      if (!jenis.didukung) {
        _snackBar(
            'Format tidak valid. Unggah sertifikat PDF atau gambar PNG/JPG.',
            warna: Colors.red.shade700);
        return;
      }

      setState(() {
        _bytesFile = bytes;
        _namaFileTerpilih = nama;
      });
      _snackBar('File "$nama" berhasil dipilih');
    } catch (e) {
      if (!mounted) return;
      _snackBar('Gagal memilih file: $e', warna: Colors.red.shade700);
    }
  }

  void _hapusTanggal() => setState(() => _tanggalTerpilih = null);

  void _hapusFile() =>
      setState(() {
        _namaFileTerpilih = null;
        _bytesFile = null;
      });

  void _simpan() async {
    if (!(_kunciForm.currentState?.validate() ?? false)) return;
    if (_tanggalTerpilih == null) {
      _snackBar('Tanggal diterbitkan wajib diisi.',
          warna: Colors.red.shade700);
      return;
    }
    if (_bytesFile == null) {
      _snackBar('File sertifikat wajib diunggah.',
          warna: Colors.red.shade700);
      return;
    }

    setState(() => _sedangUnggah = true);
    try {
      final itemBaru = await _layanan.unggah(
        judul: _kontrolerJudul.text.trim(),
        penerbit: _kontrolerPenerbit.text.trim(),
        tanggalTerbit: _tanggalTerpilih,
        namaFile: _namaFileTerpilih ?? 'sertifikat.pdf',
        bytes: _bytesFile!,
      );
      setState(() {
        _daftarLokal.insert(0, itemBaru);
        _sedangUnggah = false;
      });

      widget.saatDaftarBerubah?.call(List.from(_daftarLokal));
      _kunciForm.currentState?.reset();
      _kontrolerJudul.clear();
      _kontrolerPenerbit.clear();
      setState(() {
        _tanggalTerpilih = null;
        _namaFileTerpilih = null;
        _bytesFile = null;
      });
      if (!mounted) return;
      _snackBar('Sertifikat berhasil diunggah ke server!');
    } catch (e) {
      if (!mounted) return;
      setState(() => _sedangUnggah = false);
      _snackBar('Gagal mengunggah: $e', warna: Colors.red.shade700);
    }
  }

  // ── Navigasi & utilitas ────────────────────────────────────────────────────

  void _kembali() {
    Navigator.of(context).pushReplacement(
      MaterialPageRoute(
        builder: (_) => SertifikatScreen(
          daftarAwal: List.from(_daftarLokal),
          layananSertifikat: _layanan,
        ),
      ),
    );
  }

  void _saatFooterDiketuk(int idx) {
    if (idx == 2) {
      Navigator.of(context)
          .pushReplacement(MaterialPageRoute(builder: (_) => const ProfilScreen()));
    } else if (idx == 0) {
      Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(builder: (_) => const BerandaScreen()),
        (r) => false,
      );
    } else if (idx == 1) {
      Navigator.of(context)
          .pushReplacement(
              MaterialPageRoute(builder: (_) => const LeaderboardScreen()));
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
          primary: WarnaAplikasi.utama,
          onPrimary: Colors.white,
          surface: Colors.white,
          onSurface: WarnaAplikasi.utama,
        ),
      ),
      child: child!,
    );
  }

  // ── Build ─────────────────────────────────────────────────────────────────

  @override
  Widget build(BuildContext context) {
    final lebarLayar = MediaQuery.of(context).size.width;
    final hp = (lebarLayar * 0.075).clamp(22.0, 40.0);

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
                  child: SingleChildScrollView(
                    physics: const BouncingScrollPhysics(),
                    padding: EdgeInsets.fromLTRB(hp, 16, hp, 28),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _pillKembali(),
                        const SizedBox(height: 18),
                        _judulSeksiForm(),
                        const SizedBox(height: 12),
                        UploadSertifikatForm(
                          kunciForm: _kunciForm,
                          kontrolerJudul: _kontrolerJudul,
                          kontrolerPenerbit: _kontrolerPenerbit,
                          tanggalTerpilih: _tanggalTerpilih,
                          namaFileTerpilih: _namaFileTerpilih,
                          sedangUnggah: _sedangUnggah,
                          onPilihTanggal: _pilihTanggal,
                          onPilihFile: _pilihFile,
                          onHapusTanggal: _hapusTanggal,
                          onHapusFile: _hapusFile,
                          onSimpan: _simpan,
                        ),
                      ],
                    ),
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
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
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
            Icon(Icons.arrow_back_rounded,
                size: 14, color: WarnaAplikasi.utama),
            SizedBox(width: 4),
            Text(
              'Back',
              style: TextStyle(
                color: WarnaAplikasi.utama,
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
    return const Row(
      children: [
        Icon(Icons.workspace_premium_rounded,
            color: WarnaAplikasi.utama, size: 18),
        SizedBox(width: 8),
        Text(
          'Form Upload Sertifikat',
          style: TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.bold,
            color: WarnaAplikasi.utama,
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
