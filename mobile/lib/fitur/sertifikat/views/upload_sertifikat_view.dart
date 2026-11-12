import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';
import 'package:pretest_posttest_app/fitur/beranda/views/beranda_view.dart';
import 'package:pretest_posttest_app/fitur/papan_peringkat/views/papan_peringkat_view.dart';
import 'package:pretest_posttest_app/fitur/profil/views/profil_view.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/item_sertifikat.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/views/sertifikat_view.dart';

// ─────────────────────────────────────────────────────────────────────────────
// Halaman Upload Sertifikat Baru – Form + CRUD terhubung ke SertifikatView
// ─────────────────────────────────────────────────────────────────────────────
class UploadSertifikatView extends StatefulWidget {
  /// Daftar sertifikat existing yang akan di-update setelah CRUD
  final List<ItemSertifikat> daftarSertifikat;

  /// Callback saat daftar berubah (add / edit / delete)
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
    with SingleTickerProviderStateMixin {
  // ── State form ────────────────────────────────────────────────────────────
  final _kunciForm = GlobalKey<FormState>();
  final _kontrolerJudul = TextEditingController();
  final _kontrolerPenerbit = TextEditingController();
  DateTime? _tanggalTerpilih;
  String? _namaFileTerpilih;

  // ── Daftar sertifikat lokal (salinan dari parent) ─────────────────────────
  late List<ItemSertifikat> _daftarLokal;

  // ── Animasi fade masuk ────────────────────────────────────────────────────
  late AnimationController _kontrolerAnim;
  late Animation<double> _animFade;

  // ── Index sertifikat yang sedang di-edit (-1 = mode tambah baru) ──────────
  int _indeksEdit = -1;

  // ── Warna tema ────────────────────────────────────────────────────────────
  static const Color _maroon = WarnaAplikasi.utama;
  static const Color _maroonTerang = Color(0xFFF6EEF1);
  static const Color _maroonBorder = Color(0xFFD4A0B0);

  // ── Nama bulan Indonesia ──────────────────────────────────────────────────
  static const List<String> _bulan = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember',
  ];

  @override
  void initState() {
    super.initState();
    _daftarLokal = List.from(widget.daftarSertifikat);

    _kontrolerAnim = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 350),
    );
    _animFade =
        CurvedAnimation(parent: _kontrolerAnim, curve: Curves.easeInOut);
    _kontrolerAnim.forward();
  }

  @override
  void dispose() {
    _kontrolerJudul.dispose();
    _kontrolerPenerbit.dispose();
    _kontrolerAnim.dispose();
    super.dispose();
  }

  // ── Format tanggal panjang: "01 Januari 2026" ─────────────────────────────
  String _formatTanggal(DateTime d) =>
      '${d.day.toString().padLeft(2, '0')} ${_bulan[d.month - 1]} ${d.year}';

  // ── Format tanggal pendek untuk label kartu: "01 Jan 2026" ───────────────
  String _formatTanggalPendek(DateTime d) {
    const bln = [
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
    return '${d.day.toString().padLeft(2, '0')} ${bln[d.month - 1]} ${d.year}';
  }

  // ── Footer navigation ─────────────────────────────────────────────────────
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

  // ── Date Picker ───────────────────────────────────────────────────────────
  Future<void> _pilihTanggal() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _tanggalTerpilih ?? DateTime.now(),
      firstDate: DateTime(2015),
      lastDate: DateTime(2030),
      builder: (ctx, child) => Theme(
        data: Theme.of(ctx).copyWith(
          colorScheme: const ColorScheme.light(
            primary: _maroon,
            onPrimary: Colors.white,
            surface: Colors.white,
            onSurface: _maroon,
          ),
        ),
        child: child!,
      ),
    );
    if (picked != null) setState(() => _tanggalTerpilih = picked);
  }

  // ── Simulasi pilih file PDF ───────────────────────────────────────────────
  void _pilihFile() {
    // TODO: ganti dengan file_picker package saat backend siap
    setState(() {
      _namaFileTerpilih =
          'sertifikat_${DateTime.now().millisecondsSinceEpoch}.pdf';
    });
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(
        SnackBar(
          content: Text('File "$_namaFileTerpilih" berhasil dipilih'),
          backgroundColor: _maroon,
          duration: const Duration(seconds: 2),
        ),
      );
  }

  void _hapusFile() => setState(() => _namaFileTerpilih = null);

  // ── Reset form ke mode tambah ─────────────────────────────────────────────
  void _resetForm() {
    _kunciForm.currentState?.reset();
    _kontrolerJudul.clear();
    _kontrolerPenerbit.clear();
    setState(() {
      _tanggalTerpilih = null;
      _namaFileTerpilih = null;
      _indeksEdit = -1;
    });
  }

  // ── Load data sertifikat ke form (mode edit) ──────────────────────────────
  void _muatKeForm(int index) {
    final item = _daftarLokal[index];
    _kontrolerJudul.text = item.judul;
    _kontrolerPenerbit.text = item.label;
    setState(() {
      _tanggalTerpilih = item.tanggal;
      _namaFileTerpilih = item.urlFile;
      _indeksEdit = index;
    });
  }

  // ── Simpan (tambah atau update) ───────────────────────────────────────────
  void _simpan() {
    if (!(_kunciForm.currentState?.validate() ?? false)) return;
    if (_tanggalTerpilih == null) {
      ScaffoldMessenger.of(context)
        ..hideCurrentSnackBar()
        ..showSnackBar(
          SnackBar(
            content: const Text('Tanggal diterbitkan wajib diisi.'),
            backgroundColor: Colors.red.shade700,
            duration: const Duration(seconds: 2),
          ),
        );
      return;
    }

    final itemBaru = ItemSertifikat(
      label: _kontrolerPenerbit.text.trim(),
      judul: _kontrolerJudul.text.trim(),
      tanggalTerbit:
          'Diterbitkan pada: ${_formatTanggalPendek(_tanggalTerpilih!)}',
      tanggal: _tanggalTerpilih!,
      urlFile: _namaFileTerpilih,
    );

    final bool adalahEdit =
        _indeksEdit >= 0 && _indeksEdit < _daftarLokal.length;
    setState(() {
      if (adalahEdit) {
        _daftarLokal[_indeksEdit] = itemBaru;
      } else {
        _daftarLokal.insert(0, itemBaru);
      }
    });

    widget.saatDaftarBerubah?.call(List.from(_daftarLokal));
    _resetForm();

    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(
        SnackBar(
          content: Text(
            adalahEdit
                ? 'Sertifikat berhasil diperbarui!'
                : 'Sertifikat berhasil ditambahkan!',
          ),
          backgroundColor: _maroon,
          duration: const Duration(seconds: 2),
        ),
      );
  }

  // ── Hapus sertifikat dengan konfirmasi dialog ─────────────────────────────
  Future<void> _hapus(int index) async {
    final item = _daftarLokal[index];
    final bool? konfirmasi = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: Colors.white,
        surfaceTintColor: Colors.transparent,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text(
          'Hapus Sertifikat?',
          style: TextStyle(
            color: _maroon,
            fontSize: 16,
            fontWeight: FontWeight.bold,
          ),
        ),
        content: Text(
          'Sertifikat "${item.judul}" akan dihapus secara permanen.',
          style: const TextStyle(fontSize: 13),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(false),
            child: Text('Batal', style: TextStyle(color: Colors.grey.shade600)),
          ),
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(true),
            child: const Text(
              'Hapus',
              style: TextStyle(color: _maroon, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );

    if (konfirmasi != true) return;

    setState(() {
      _daftarLokal.removeAt(index);
      if (_indeksEdit == index) {
        _resetForm();
      } else if (_indeksEdit > index) {
        _indeksEdit--;
      }
    });
    widget.saatDaftarBerubah?.call(List.from(_daftarLokal));

    if (!mounted) return;
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(
        const SnackBar(
          content: Text('Sertifikat berhasil dihapus.'),
          backgroundColor: _maroon,
          duration: Duration(seconds: 2),
        ),
      );
  }

  // ── Kembali ke halaman Sertifikat & Portofolio ────────────────────────────
  void _kembali() {
    Navigator.of(context).pushReplacement(
      MaterialPageRoute(
        builder: (_) => SertifikatView(daftarAwal: _daftarLokal),
      ),
    );
  }

  // ─────────────────────────────────────────────────────────────────────────
  // BUILD
  // ─────────────────────────────────────────────────────────────────────────
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      bottomNavigationBar: NavigasiBawah(
        indeksSaatIni: 2,
        saatDiketuk: _saatFooterDiketuk,
      ),
      body: AnnotatedRegion<SystemUiOverlayStyle>(
        value: SystemUiOverlayStyle.light.copyWith(
          statusBarColor: Colors.transparent,
        ),
        child: SafeArea(
          top: false,
          bottom: false,
          child: Column(
            children: [
              // ── Header Maroon ─────────────────────────────────────────────
              const _HeaderUpload(),

              // ── Konten Scrollable ─────────────────────────────────────────
              Expanded(
                child: FadeTransition(
                  opacity: _animFade,
                  child: LayoutBuilder(
                    builder: (ctx, constraints) {
                      final double hp =
                          (constraints.maxWidth * 0.075).clamp(22.0, 40.0);

                      return SingleChildScrollView(
                        physics: const BouncingScrollPhysics(),
                        padding: EdgeInsets.fromLTRB(hp, 16, hp, 28),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // ── Tombol Back Pill ──────────────────────────────
                            InkWell(
                              onTap: _kembali,
                              borderRadius: BorderRadius.circular(20),
                              child: Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 12, vertical: 5),
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  borderRadius: BorderRadius.circular(20),
                                  border: Border.all(
                                    color: _maroon.withValues(alpha: 0.35),
                                    width: 1.2,
                                  ),
                                ),
                                child: const Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Icon(Icons.arrow_back_rounded,
                                        size: 14, color: _maroon),
                                    SizedBox(width: 4),
                                    Text(
                                      'Back',
                                      style: TextStyle(
                                        color: _maroon,
                                        fontSize: 12,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),

                            const SizedBox(height: 18),

                            // ── Judul Seksi Form ──────────────────────────────
                            Row(
                              children: [
                                const Icon(Icons.workspace_premium_rounded,
                                    color: _maroon, size: 18),
                                const SizedBox(width: 8),
                                Text(
                                  _indeksEdit >= 0
                                      ? 'Edit Sertifikat'
                                      : 'Form Upload Sertifikat',
                                  style: const TextStyle(
                                    fontSize: 15,
                                    fontWeight: FontWeight.bold,
                                    color: _maroon,
                                  ),
                                ),
                              ],
                            ),

                            const SizedBox(height: 12),

                            // ── FORM CARD ─────────────────────────────────────
                            Container(
                              padding: const EdgeInsets.all(18),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(
                                    color: _maroonBorder, width: 1.2),
                                boxShadow: [
                                  BoxShadow(
                                    color: _maroon.withValues(alpha: 0.07),
                                    blurRadius: 12,
                                    offset: const Offset(0, 4),
                                  ),
                                ],
                              ),
                              child: Form(
                                key: _kunciForm,
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    _bangunLabel('Judul Sertifikat',
                                        wajib: true),
                                    const SizedBox(height: 6),
                                    _bangunKolomTeks(
                                      kontroler: _kontrolerJudul,
                                      petunjuk: 'Masukkan judul sertifikat',
                                      validator: (v) =>
                                          (v == null || v.trim().isEmpty)
                                              ? 'Judul sertifikat wajib diisi'
                                              : null,
                                    ),

                                    const SizedBox(height: 16),

                                    _bangunLabel('Penerbit', wajib: true),
                                    const SizedBox(height: 6),
                                    _bangunKolomTeks(
                                      kontroler: _kontrolerPenerbit,
                                      petunjuk:
                                          'Contoh: Coursera, Dicoding, dll.',
                                      validator: (v) =>
                                          (v == null || v.trim().isEmpty)
                                              ? 'Penerbit wajib diisi'
                                              : null,
                                    ),

                                    const SizedBox(height: 16),

                                    _bangunLabel('Diterbitkan Pada',
                                        wajib: true),
                                    const SizedBox(height: 6),
                                    _bangunKolomTanggal(),

                                    const SizedBox(height: 20),

                                    _bangunLabel('Upload File Sertifikat (PDF)',
                                        wajib: false),
                                    const SizedBox(height: 8),
                                    _bangunAreaUpload(),

                                    const SizedBox(height: 24),

                                    // ── Tombol Simpan & Batal ───────────────
                                    Row(
                                      children: [
                                        if (_indeksEdit >= 0) ...[
                                          Expanded(
                                            child: OutlinedButton(
                                              onPressed: _resetForm,
                                              style: OutlinedButton.styleFrom(
                                                foregroundColor: _maroon,
                                                side: const BorderSide(
                                                    color: _maroon, width: 1.5),
                                                padding:
                                                    const EdgeInsets.symmetric(
                                                        vertical: 13),
                                                shape: RoundedRectangleBorder(
                                                  borderRadius:
                                                      BorderRadius.circular(12),
                                                ),
                                              ),
                                              child: const Text(
                                                'Batal Edit',
                                                style: TextStyle(
                                                    fontWeight:
                                                        FontWeight.bold),
                                              ),
                                            ),
                                          ),
                                          const SizedBox(width: 12),
                                        ],
                                        Expanded(
                                          flex: 2,
                                          child: ElevatedButton.icon(
                                            onPressed: _simpan,
                                            icon: Icon(
                                              _indeksEdit >= 0
                                                  ? Icons.save_rounded
                                                  : Icons
                                                      .add_circle_outline_rounded,
                                              size: 18,
                                            ),
                                            label: Text(
                                              _indeksEdit >= 0
                                                  ? 'Perbarui Sertifikat'
                                                  : 'Tambah Sertifikat',
                                              style: const TextStyle(
                                                  fontWeight: FontWeight.bold),
                                            ),
                                            style: ElevatedButton.styleFrom(
                                              backgroundColor: _maroon,
                                              foregroundColor: Colors.white,
                                              elevation: 0,
                                              padding:
                                                  const EdgeInsets.symmetric(
                                                      vertical: 13),
                                              shape: RoundedRectangleBorder(
                                                borderRadius:
                                                    BorderRadius.circular(12),
                                              ),
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ],
                                ),
                              ),
                            ),

                            const SizedBox(height: 28),

                            // ── Heading Daftar Sertifikat ─────────────────────
                            Row(
                              children: [
                                const Icon(Icons.list_alt_rounded,
                                    color: _maroon, size: 18),
                                const SizedBox(width: 8),
                                const Text(
                                  'Daftar Sertifikat',
                                  style: TextStyle(
                                    fontSize: 15,
                                    fontWeight: FontWeight.bold,
                                    color: _maroon,
                                  ),
                                ),
                                const SizedBox(width: 8),
                                Container(
                                  padding: const EdgeInsets.symmetric(
                                      horizontal: 8, vertical: 2),
                                  decoration: BoxDecoration(
                                    color: _maroon,
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                  child: Text(
                                    '${_daftarLokal.length}',
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontSize: 11,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                              ],
                            ),

                            const SizedBox(height: 12),

                            // ── Daftar CRUD ───────────────────────────────────
                            if (_daftarLokal.isEmpty)
                              _bangunStatusKosong()
                            else
                              ListView.separated(
                                shrinkWrap: true,
                                physics: const NeverScrollableScrollPhysics(),
                                itemCount: _daftarLokal.length,
                                separatorBuilder: (_, __) =>
                                    const SizedBox(height: 12),
                                itemBuilder: (_, idx) {
                                  final item = _daftarLokal[idx];
                                  final bool sedangDiedit = _indeksEdit == idx;
                                  return _KartuSertifikat(
                                    item: item,
                                    sedangDiedit: sedangDiedit,
                                    saatEdit: () => _muatKeForm(idx),
                                    saatHapus: () => _hapus(idx),
                                  );
                                },
                              ),

                            const SizedBox(height: 28),

                            // ── Tombol Kembali ke Sertifikat & Portofolio ─────
                            SizedBox(
                              width: double.infinity,
                              height: 50,
                              child: ElevatedButton.icon(
                                onPressed: _kembali,
                                icon: const Icon(Icons.arrow_back_rounded,
                                    size: 18),
                                label: const Text(
                                  'Kembali ke Sertifikat & Portofolio',
                                  style: TextStyle(
                                      fontSize: 13,
                                      fontWeight: FontWeight.bold),
                                ),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: _maroon,
                                  foregroundColor: Colors.white,
                                  elevation: 0,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(14),
                                  ),
                                ),
                              ),
                            ),
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

  // ── Builder helpers ───────────────────────────────────────────────────────

  Widget _bangunLabel(String teks, {bool wajib = false}) {
    return Row(
      children: [
        Text(
          teks,
          style: const TextStyle(
            color: _maroon,
            fontSize: 13,
            fontWeight: FontWeight.w600,
          ),
        ),
        if (wajib)
          const Text(
            ' *',
            style: TextStyle(
                color: Colors.red, fontSize: 13, fontWeight: FontWeight.bold),
          ),
      ],
    );
  }

  Widget _bangunKolomTeks({
    required TextEditingController kontroler,
    required String petunjuk,
    String? Function(String?)? validator,
    int maxLines = 1,
  }) {
    return TextFormField(
      controller: kontroler,
      validator: validator,
      maxLines: maxLines,
      style: const TextStyle(
          color: _maroon, fontSize: 13.5, fontWeight: FontWeight.w500),
      cursorColor: _maroon,
      decoration: InputDecoration(
        hintText: petunjuk,
        hintStyle: TextStyle(
            color: Colors.grey.shade400,
            fontSize: 13,
            fontWeight: FontWeight.normal),
        filled: true,
        fillColor: _maroonTerang,
        contentPadding:
            const EdgeInsets.symmetric(horizontal: 14, vertical: 13),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: _maroonBorder, width: 1.2),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: _maroonBorder, width: 1.2),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: _maroon, width: 1.8),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: Colors.red.shade400, width: 1.5),
        ),
        focusedErrorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: Colors.red.shade400, width: 1.8),
        ),
        errorStyle: const TextStyle(fontSize: 11),
      ),
    );
  }

  Widget _bangunKolomTanggal() {
    return InkWell(
      onTap: _pilihTanggal,
      borderRadius: BorderRadius.circular(12),
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 13),
        decoration: BoxDecoration(
          color: _maroonTerang,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: _tanggalTerpilih != null ? _maroon : _maroonBorder,
            width: _tanggalTerpilih != null ? 1.8 : 1.2,
          ),
        ),
        child: Row(
          children: [
            const Icon(Icons.calendar_month_rounded, color: _maroon, size: 18),
            const SizedBox(width: 10),
            Expanded(
              child: Text(
                _tanggalTerpilih != null
                    ? _formatTanggal(_tanggalTerpilih!)
                    : 'Pilih tanggal, bulan, dan tahun',
                style: TextStyle(
                  fontSize: 13,
                  fontWeight: _tanggalTerpilih != null
                      ? FontWeight.w600
                      : FontWeight.normal,
                  color:
                      _tanggalTerpilih != null ? _maroon : Colors.grey.shade500,
                ),
              ),
            ),
            if (_tanggalTerpilih != null)
              InkWell(
                onTap: () => setState(() => _tanggalTerpilih = null),
                borderRadius: BorderRadius.circular(10),
                child: const Icon(Icons.close, size: 16, color: _maroon),
              ),
          ],
        ),
      ),
    );
  }

  Widget _bangunAreaUpload() {
    if (_namaFileTerpilih == null) {
      return GestureDetector(
        onTap: _pilihFile,
        child: Container(
          width: double.infinity,
          padding: const EdgeInsets.symmetric(vertical: 30),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: _maroon, width: 1.5),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 64,
                height: 64,
                decoration: const BoxDecoration(
                  color: _maroonTerang,
                  shape: BoxShape.circle,
                ),
                child: const Icon(Icons.cloud_upload_rounded,
                    color: _maroon, size: 34),
              ),
              const SizedBox(height: 12),
              const Text(
                'Upload File Disini',
                style: TextStyle(
                    color: _maroon, fontSize: 14, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 4),
              Text(
                'Format PDF, maksimal 10 MB',
                style: TextStyle(color: Colors.grey.shade500, fontSize: 11.5),
              ),
            ],
          ),
        ),
      );
    } else {
      return Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        decoration: BoxDecoration(
          color: _maroonTerang,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: _maroon, width: 1.2),
        ),
        child: Row(
          children: [
            Container(
              width: 40,
              height: 40,
              decoration: BoxDecoration(
                color: _maroon,
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Icon(Icons.picture_as_pdf_rounded,
                  color: Colors.white, size: 22),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    _namaFileTerpilih!,
                    style: const TextStyle(
                        color: _maroon,
                        fontSize: 12.5,
                        fontWeight: FontWeight.bold),
                    overflow: TextOverflow.ellipsis,
                  ),
                  const Text(
                    'File PDF siap diunggah',
                    style: TextStyle(color: Colors.grey, fontSize: 11),
                  ),
                ],
              ),
            ),
            IconButton(
              onPressed: _hapusFile,
              icon: const Icon(Icons.delete_outline_rounded,
                  color: _maroon, size: 20),
              tooltip: 'Hapus file',
            ),
          ],
        ),
      );
    }
  }

  Widget _bangunStatusKosong() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 36),
      decoration: BoxDecoration(
        color: _maroonTerang,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: _maroonBorder, width: 1.0),
      ),
      child: Column(
        children: [
          Icon(Icons.inbox_outlined,
              color: _maroon.withValues(alpha: 0.35), size: 42),
          const SizedBox(height: 10),
          Text(
            'Belum ada sertifikat.',
            style: TextStyle(
                color: _maroon.withValues(alpha: 0.6),
                fontSize: 13,
                fontWeight: FontWeight.w500),
          ),
          const SizedBox(height: 4),
          Text(
            'Isi form di atas untuk menambahkan sertifikat.',
            style: TextStyle(
                color: _maroon.withValues(alpha: 0.45), fontSize: 11.5),
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Header maroon melengkung "Upload Sertifikat Baru"
// ─────────────────────────────────────────────────────────────────────────────
class _HeaderUpload extends StatelessWidget {
  const _HeaderUpload();

  @override
  Widget build(BuildContext context) {
    final mq = MediaQuery.of(context);
    final double sbh = mq.viewPadding.top;
    final double hp = (mq.size.width * 0.075).clamp(22.0, 40.0);

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
          Positioned(
            top: -50,
            right: -40,
            child: _lingkaran(180),
          ),
          Positioned(
            bottom: -60,
            left: -40,
            child: _lingkaran(150),
          ),
          Padding(
            padding:
                EdgeInsets.only(top: sbh + 20, bottom: 22, left: hp, right: hp),
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

  static Widget _lingkaran(double ukuran) => Container(
        width: ukuran,
        height: ukuran,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: Colors.white.withValues(alpha: 0.05),
        ),
      );
}

// ─────────────────────────────────────────────────────────────────────────────
// Card sertifikat di daftar dengan tombol Edit & Hapus
// ─────────────────────────────────────────────────────────────────────────────
class _KartuSertifikat extends StatelessWidget {
  final ItemSertifikat item;
  final bool sedangDiedit;
  final VoidCallback saatEdit;
  final VoidCallback saatHapus;

  static const Color _maroon = WarnaAplikasi.utama;
  static const Color _maroonTerang = Color(0xFFF6EEF1);
  static const Color _maroonBorder = Color(0xFFD4A0B0);

  const _KartuSertifikat({
    required this.item,
    required this.sedangDiedit,
    required this.saatEdit,
    required this.saatHapus,
  });

  @override
  Widget build(BuildContext context) {
    return AnimatedContainer(
      duration: const Duration(milliseconds: 250),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: sedangDiedit ? _maroon : _maroonBorder,
          width: sedangDiedit ? 2 : 1.2,
        ),
        boxShadow: [
          BoxShadow(
            color: _maroon.withValues(alpha: sedangDiedit ? 0.12 : 0.05),
            blurRadius: sedangDiedit ? 12 : 6,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.fromLTRB(14, 14, 8, 12),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Ikon
            Container(
              width: 42,
              height: 42,
              decoration: BoxDecoration(
                color: _maroonTerang,
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(Icons.workspace_premium_rounded,
                  color: _maroon, size: 22),
            ),
            const SizedBox(width: 10),
            // Teks
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (sedangDiedit)
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 8, vertical: 2),
                      margin: const EdgeInsets.only(bottom: 4),
                      decoration: BoxDecoration(
                        color: _maroon,
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: const Text(
                        'Sedang diedit',
                        style: TextStyle(
                            color: Colors.white,
                            fontSize: 10,
                            fontWeight: FontWeight.bold),
                      ),
                    ),
                  Text(
                    item.judul,
                    style: const TextStyle(
                        color: _maroon,
                        fontSize: 14,
                        fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    item.label,
                    style: TextStyle(
                        color: _maroon.withValues(alpha: 0.7),
                        fontSize: 12,
                        fontWeight: FontWeight.w500),
                  ),
                  const SizedBox(height: 2),
                  Row(
                    children: [
                      Icon(Icons.calendar_today_rounded,
                          size: 11, color: _maroon.withValues(alpha: 0.6)),
                      const SizedBox(width: 4),
                      Text(
                        item.tanggalTerbit,
                        style: TextStyle(
                            color: _maroon.withValues(alpha: 0.6),
                            fontSize: 11,
                            fontWeight: FontWeight.w500),
                      ),
                    ],
                  ),
                  if (item.urlFile != null) ...[
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        const Icon(Icons.picture_as_pdf_rounded,
                            size: 13, color: _maroon),
                        const SizedBox(width: 4),
                        Expanded(
                          child: Text(
                            item.urlFile!,
                            style: const TextStyle(
                                color: _maroon,
                                fontSize: 10.5,
                                fontStyle: FontStyle.italic),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                  ],
                ],
              ),
            ),
            // Tombol Edit & Hapus
            Column(
              children: [
                IconButton(
                  onPressed: saatEdit,
                  icon:
                      const Icon(Icons.edit_rounded, color: _maroon, size: 18),
                  tooltip: 'Edit',
                  padding: const EdgeInsets.all(6),
                  constraints: const BoxConstraints(),
                ),
                const SizedBox(height: 4),
                IconButton(
                  onPressed: saatHapus,
                  icon: Icon(Icons.delete_outline_rounded,
                      color: Colors.red.shade400, size: 18),
                  tooltip: 'Hapus',
                  padding: const EdgeInsets.all(6),
                  constraints: const BoxConstraints(),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
