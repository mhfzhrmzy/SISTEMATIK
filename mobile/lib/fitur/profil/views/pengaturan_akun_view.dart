import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:image_picker/image_picker.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/sesi_pengguna.dart';
import 'package:pretest_posttest_app/fitur/profil/data/layanan_profil.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';
import 'package:pretest_posttest_app/fitur/beranda/views/beranda_view.dart';
import 'package:pretest_posttest_app/fitur/papan_peringkat/views/papan_peringkat_view.dart';

/// Halaman Pengaturan Akun Siswa.
///
/// Berisi formulir data profil siswa (NISN, Nama Lengkap, Kelas,
/// Jurusan, Password Saat Ini, dan Password Baru) serta foto avatar
/// dengan badge kamera sesuai desain UI mockup.
class PengaturanAkunView extends StatefulWidget {
  /// Nama lengkap awal siswa.
  final String? namaAwal;

  /// NISN siswa.
  final String nisn;

  /// Kelas siswa (misal: "10").
  final String kelas;

  /// Jurusan siswa (misal: "TKJ").
  final String jurusan;
  final LayananProfil? layananProfil;

  const PengaturanAkunView({
    super.key,
    this.namaAwal,
    this.nisn = '0084729103',
    this.kelas = '10',
    this.jurusan = 'TKJ',
    this.layananProfil,
  });

  @override
  State<PengaturanAkunView> createState() => _PengaturanAkunViewState();
}

class _PengaturanAkunViewState extends State<PengaturanAkunView> {
  // ── Warna tema form ────────────────────────────────────────────────────────
  static const Color _warnaBorderPudar = Color(0xFFC795A4);

  // ── Kontroller kolom teks ──────────────────────────────────────────────────
  late final TextEditingController _kontrolerNisn;
  late final TextEditingController _kontrolerNama;
  late final TextEditingController _kontrolerKelas;
  late final TextEditingController _kontrolerJurusan;
  final TextEditingController _kontrolerPasswordSaatIni =
      TextEditingController();
  final TextEditingController _kontrolerPasswordBaru = TextEditingController();

  // ── State visibilitas password ─────────────────────────────────────────────
  bool _sembunyikanPasswordSaatIni = true;
  bool _sembunyikanPasswordBaru = true;
  bool _sedangMenyimpan = false;
  String? _fotoProfil;
  late final LayananProfil _layananProfil;

  @override
  void initState() {
    super.initState();
    _layananProfil = widget.layananProfil ?? LayananProfil();
    _kontrolerNisn =
        TextEditingController(text: SesiPengguna.nisn ?? widget.nisn);
    _kontrolerNama =
        TextEditingController(text: SesiPengguna.nama ?? widget.namaAwal ?? '');
    _kontrolerKelas =
        TextEditingController(text: SesiPengguna.kelas ?? widget.kelas);
    _kontrolerJurusan =
        TextEditingController(text: SesiPengguna.jurusan ?? widget.jurusan);
    _fotoProfil = SesiPengguna.fotoProfil;
    _muatProfil();
  }

  @override
  void dispose() {
    _kontrolerNisn.dispose();
    _kontrolerNama.dispose();
    _kontrolerKelas.dispose();
    _kontrolerJurusan.dispose();
    _kontrolerPasswordSaatIni.dispose();
    _kontrolerPasswordBaru.dispose();
    super.dispose();
  }

  // ── Navigasi footer ────────────────────────────────────────────────────────
  void _saatFooterDiketuk(int idx) {
    switch (idx) {
      case 0:
        Navigator.of(context).pushAndRemoveUntil(
          MaterialPageRoute(builder: (_) => const BerandaView()),
          (route) => false,
        );
        break;
      case 1:
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (_) => const PapanPeringkatView()),
        );
        break;
      case 2:
        Navigator.of(context).pop();
        break;
    }
  }

  // ── Aksi simpan perubahan ──────────────────────────────────────────────────
  Future<void> _muatProfil() async {
    try {
      final pengguna = await _layananProfil.ambil();
      if (!mounted) return;
      setState(() {
        _kontrolerNisn.text = pengguna.nisn;
        _kontrolerNama.text = pengguna.namaLengkap;
        _kontrolerKelas.text = pengguna.kelas ?? '';
        _kontrolerJurusan.text = pengguna.jurusan ?? '';
        _fotoProfil = pengguna.fotoProfil;
      });
      SesiPengguna.perbaruiProfil(
        nama: pengguna.namaLengkap,
        kelas: pengguna.labelKelas,
        jurusan: pengguna.jurusan,
        fotoProfil: pengguna.fotoProfil,
      );
    } on KesalahanApi catch (error) {
      if (mounted) _tampilkanPesan(error.pesan);
    } catch (_) {
      if (mounted) _tampilkanPesan('Gagal memuat profil dari server.');
    }
  }

  Future<void> _simpanPerubahan() async {
    FocusScope.of(context).unfocus();

    final nama = _kontrolerNama.text.trim();
    final passwordLama = _kontrolerPasswordSaatIni.text;
    final passwordBaru = _kontrolerPasswordBaru.text;
    if (nama.isEmpty) {
      _tampilkanPesan('Nama lengkap wajib diisi.');
      return;
    }
    if (passwordLama.isNotEmpty != passwordBaru.isNotEmpty) {
      _tampilkanPesan(
          'Isi password lama dan password baru untuk menggantinya.');
      return;
    }

    setState(() => _sedangMenyimpan = true);
    try {
      if (passwordLama.isNotEmpty) {
        await _layananProfil.ubahPassword(
          lama: passwordLama,
          baru: passwordBaru,
        );
      }
      final pengguna = await _layananProfil.ubahNama(nama);
      if (!mounted) return;
      SesiPengguna.perbaruiProfil(
        nama: pengguna.namaLengkap,
        kelas: pengguna.labelKelas,
        jurusan: pengguna.jurusan,
      );
      _kontrolerPasswordSaatIni.clear();
      _kontrolerPasswordBaru.clear();
      setState(() => _sedangMenyimpan = false);
      _tampilkanPesan('Perubahan data akun berhasil disimpan.');
    } on KesalahanApi catch (error) {
      if (!mounted) return;
      setState(() => _sedangMenyimpan = false);
      _tampilkanPesan(error.pesan);
    } catch (_) {
      if (!mounted) return;
      setState(() => _sedangMenyimpan = false);
      _tampilkanPesan('Gagal menyimpan perubahan akun.');
    }
  }

  Future<void> _pilihFotoAvatar() async {
    try {
      final foto = await ImagePicker().pickImage(
        source: ImageSource.gallery,
        imageQuality: 80,
        maxWidth: 1200,
      );
      if (foto == null) return;
      final Uint8List bytes = await foto.readAsBytes();
      final url = await _layananProfil.unggahFoto(
        namaFile: foto.name,
        bytes: bytes,
      );
      if (!mounted) return;
      setState(() => _fotoProfil = url);
      SesiPengguna.perbaruiProfil(fotoProfil: url);
      _tampilkanPesan('Foto profil berhasil diperbarui.');
    } on KesalahanApi catch (error) {
      if (mounted) _tampilkanPesan(error.pesan);
    } catch (_) {
      if (mounted) _tampilkanPesan('Foto profil gagal diperbarui.');
    }
  }

  void _tampilkanPesan(String pesan) {
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(
        SnackBar(
          content: Text(pesan),
          backgroundColor: WarnaAplikasi.utama,
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
        value: SystemUiOverlayStyle.light.copyWith(
          statusBarColor: Colors.transparent,
        ),
        child: SafeArea(
          top: false,
          bottom: false,
          child: Column(
            children: [
              // ── Header Maroon melengkung ───────────────────────────────────
              const _HeaderPengaturanAkun(),

              // ── Konten Formulir Scrollable ─────────────────────────────────
              Expanded(
                child: LayoutBuilder(
                  builder: (ctx, constraints) {
                    final double hp =
                        (constraints.maxWidth * 0.075).clamp(20.0, 36.0);

                    return SingleChildScrollView(
                      physics: const BouncingScrollPhysics(),
                      padding: EdgeInsets.fromLTRB(hp, 14, hp, 24),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // ── 1. Tombol Back Pill ────────────────────────────
                          _TombolKembali(
                            saatDiketuk: () => Navigator.of(context).pop(),
                          ),
                          const SizedBox(height: 12),

                          // ── 2. Avatar Tengah dengan Badge Kamera ───────────
                          Center(
                            child: _AvatarSiswa(
                              fotoUrl: _fotoProfil,
                              saatDiketuk: _pilihFotoAvatar,
                            ),
                          ),
                          const SizedBox(height: 18),

                          // ── 3. Kartu Putih Border Formulir ─────────────────
                          Container(
                            width: double.infinity,
                            padding: const EdgeInsets.symmetric(
                              horizontal: 18,
                              vertical: 20,
                            ),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(22),
                              border: Border.all(
                                color: _warnaBorderPudar,
                                width: 1.4,
                              ),
                              boxShadow: [
                                BoxShadow(
                                  color: WarnaAplikasi.bayanganLembut,
                                  blurRadius: 10,
                                  offset: const Offset(0, 4),
                                ),
                              ],
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.stretch,
                              children: [
                                _KolomInput(
                                  label: 'NISN',
                                  kontroler: _kontrolerNisn,
                                  tipeKeyboard: TextInputType.number,
                                ),
                                const SizedBox(height: 12),
                                _KolomInput(
                                  label: 'Nama Lengkap',
                                  kontroler: _kontrolerNama,
                                  petunjuk: 'Masukkan nama lengkap baru',
                                ),
                                const SizedBox(height: 12),
                                _KolomInput(
                                  label: 'Kelas',
                                  kontroler: _kontrolerKelas,
                                ),
                                const SizedBox(height: 12),
                                _KolomInput(
                                  label: 'Jurusan',
                                  kontroler: _kontrolerJurusan,
                                ),
                                const SizedBox(height: 12),
                                _KolomInput(
                                  label: 'Password saat ini',
                                  kontroler: _kontrolerPasswordSaatIni,
                                  petunjuk: 'Ganti Password',
                                  sembunyikanTeks: _sembunyikanPasswordSaatIni,
                                  saatIkonDiketuk: () {
                                    setState(() {
                                      _sembunyikanPasswordSaatIni =
                                          !_sembunyikanPasswordSaatIni;
                                    });
                                  },
                                ),
                                const SizedBox(height: 12),
                                _KolomInput(
                                  label: 'Password baru',
                                  kontroler: _kontrolerPasswordBaru,
                                  petunjuk: 'Ganti Password',
                                  sembunyikanTeks: _sembunyikanPasswordBaru,
                                  saatIkonDiketuk: () {
                                    setState(() {
                                      _sembunyikanPasswordBaru =
                                          !_sembunyikanPasswordBaru;
                                    });
                                  },
                                ),
                                const SizedBox(height: 22),

                                // ── Tombol Simpan Perubahan ──────────────────
                                SizedBox(
                                  height: 44,
                                  child: ElevatedButton(
                                    onPressed: _sedangMenyimpan
                                        ? null
                                        : _simpanPerubahan,
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: WarnaAplikasi.utama,
                                      foregroundColor: Colors.white,
                                      elevation: 0,
                                      shape: RoundedRectangleBorder(
                                        borderRadius: BorderRadius.circular(22),
                                      ),
                                    ),
                                    child: _sedangMenyimpan
                                        ? const SizedBox(
                                            width: 18,
                                            height: 18,
                                            child: CircularProgressIndicator(
                                              strokeWidth: 2,
                                              color: Colors.white,
                                            ),
                                          )
                                        : const Text(
                                            'Simpan Perubahan',
                                            style: TextStyle(
                                              fontSize: 13.5,
                                              fontWeight: FontWeight.bold,
                                              letterSpacing: -0.2,
                                            ),
                                          ),
                                  ),
                                ),
                              ],
                            ),
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

// ─────────────────────────────────────────────────────────────────────────────
// Header Maroon Melengkung "Pengaturan akun siswa"
// ─────────────────────────────────────────────────────────────────────────────
class _HeaderPengaturanAkun extends StatelessWidget {
  const _HeaderPengaturanAkun();

  @override
  Widget build(BuildContext context) {
    final mediaQuery = MediaQuery.of(context);
    final double tinggiStatusBar = mediaQuery.viewPadding.top;
    final double hp = (mediaQuery.size.width * 0.075).clamp(20.0, 36.0);

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
            'Pengaturan akun siswa',
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

// ─────────────────────────────────────────────────────────────────────────────
// Tombol Back berbentuk Pill di sudut kiri atas
// ─────────────────────────────────────────────────────────────────────────────
class _TombolKembali extends StatelessWidget {
  final VoidCallback saatDiketuk;

  const _TombolKembali({required this.saatDiketuk});

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: saatDiketuk,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: const Color(0xFFC795A4),
            width: 1.2,
          ),
        ),
        child: const Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              Icons.arrow_circle_left_outlined,
              size: 16,
              color: WarnaAplikasi.utama,
            ),
            SizedBox(width: 5),
            Text(
              'Back',
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.bold,
                color: WarnaAplikasi.utama,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Avatar Siswa dengan Badge Kamera di sudut kanan bawah
// ─────────────────────────────────────────────────────────────────────────────
class _AvatarSiswa extends StatelessWidget {
  final VoidCallback saatDiketuk;
  final String? fotoUrl;

  const _AvatarSiswa({required this.saatDiketuk, this.fotoUrl});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: saatDiketuk,
      child: Stack(
        clipBehavior: Clip.none,
        alignment: Alignment.center,
        children: [
          // Lingkaran Avatar Utama
          Container(
            width: 86,
            height: 86,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: Colors.white,
              border: Border.all(
                color: WarnaAplikasi.utama,
                width: 3.5,
              ),
            ),
            child: fotoUrl == null
                ? const Icon(
                    Icons.person,
                    size: 58,
                    color: WarnaAplikasi.utama,
                  )
                : ClipOval(
                    child: Image.network(
                      fotoUrl!,
                      width: 86,
                      height: 86,
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => const Icon(
                        Icons.person,
                        size: 58,
                        color: WarnaAplikasi.utama,
                      ),
                    ),
                  ),
          ),

          // Badge Ikon Kamera di Kanan Bawah
          Positioned(
            right: -2,
            bottom: 2,
            child: Container(
              padding: const EdgeInsets.all(5),
              decoration: BoxDecoration(
                color: WarnaAplikasi.utama,
                shape: BoxShape.circle,
                border: Border.all(
                  color: Colors.white,
                  width: 1.5,
                ),
              ),
              child: const Icon(
                Icons.camera_alt,
                size: 13,
                color: Colors.white,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Widget Kolom Input Formulir (Label + TextField Border Rounded Pill)
// ─────────────────────────────────────────────────────────────────────────────
class _KolomInput extends StatelessWidget {
  final String label;
  final TextEditingController kontroler;
  final String? petunjuk;
  final bool sembunyikanTeks;
  final VoidCallback? saatIkonDiketuk;
  final TextInputType tipeKeyboard;

  const _KolomInput({
    required this.label,
    required this.kontroler,
    this.petunjuk,
    this.sembunyikanTeks = false,
    this.saatIkonDiketuk,
    this.tipeKeyboard = TextInputType.text,
  });

  @override
  Widget build(BuildContext context) {
    const borderStyle = OutlineInputBorder(
      borderRadius: BorderRadius.all(Radius.circular(20)),
      borderSide: BorderSide(
        color: Color(0xFFC795A4),
        width: 1.3,
      ),
    );

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            color: WarnaAplikasi.utama,
            fontSize: 12.5,
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 5),
        SizedBox(
          height: 38,
          child: TextFormField(
            controller: kontroler,
            obscureText: sembunyikanTeks,
            keyboardType: tipeKeyboard,
            style: const TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.w500,
              color: Colors.black87,
            ),
            decoration: InputDecoration(
              isDense: true,
              hintText: petunjuk,
              hintStyle: TextStyle(
                color: Colors.grey.shade400,
                fontSize: 12.5,
                fontWeight: FontWeight.normal,
              ),
              contentPadding: const EdgeInsets.symmetric(
                horizontal: 16,
                vertical: 9,
              ),
              enabledBorder: borderStyle,
              focusedBorder: borderStyle.copyWith(
                borderSide: const BorderSide(
                  color: WarnaAplikasi.utama,
                  width: 1.6,
                ),
              ),
              suffixIcon: saatIkonDiketuk != null
                  ? IconButton(
                      icon: Icon(
                        sembunyikanTeks
                            ? Icons.visibility_off_outlined
                            : Icons.visibility_outlined,
                        size: 16,
                        color: WarnaAplikasi.utama,
                      ),
                      padding: EdgeInsets.zero,
                      constraints: const BoxConstraints(),
                      onPressed: saatIkonDiketuk,
                    )
                  : null,
            ),
          ),
        ),
      ],
    );
  }
}
