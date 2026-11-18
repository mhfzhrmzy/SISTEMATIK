import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/sesi_pengguna.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';
import 'package:pretest_posttest_app/fitur/materi/data/layanan_materi.dart';
import 'package:pretest_posttest_app/fitur/materi/models/model_materi.dart';
import 'package:pretest_posttest_app/fitur/materi/views/buka_materi_view.dart';
import 'package:pretest_posttest_app/fitur/pretest/views/pretest_view.dart';
import 'package:pretest_posttest_app/fitur/posttest/views/posttest_view.dart';
import 'package:pretest_posttest_app/fitur/profil/views/profil_view.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/views/leaderboard_view.dart';

/// Halaman Beranda / Dashboard utama setelah pengguna login.
///
/// Struktur:
/// - Header profil pengguna (fixed, maroon)
/// - Card ringkasan statistik (jumlah materi & quiz)
/// - Grid materi yang scrollable
/// - [NavigasiBawah] fixed di bawah
class BerandaView extends StatefulWidget {
  /// Nama pengguna yang login. Jika kosong, diambil dari sesi.
  final String? nama;

  /// Label kelas pengguna yang login. Jika kosong, diambil dari sesi.
  final String? kelas;

  const BerandaView({
    super.key,
    this.nama,
    this.kelas,
  });

  @override
  State<BerandaView> createState() => _BerandaViewState();
}

class _BerandaViewState extends State<BerandaView> {
  int _indeksFooter = 0;
  final LayananMateri _layananMateri = const LayananMateri();
  List<ModelMateri> _daftarMateri = const [];
  bool _sedangMemuat = false;

  @override
  void initState() {
    super.initState();
    _muatDaftarMateri();
  }

  Future<void> _muatDaftarMateri() async {
    setState(() => _sedangMemuat = true);
    try {
      final hasil = await _layananMateri.ambilDaftarMateri();
      if (!mounted) return;
      setState(() {
        // Gunakan data murni dari backend. Jika belum ada materi di database, list tetap kosong.
        _daftarMateri = hasil.daftar;
        _sedangMemuat = false;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() => _sedangMemuat = false);
    }
  }

  // ── Profil pengguna dari sesi login ─────────────────────────────────────
  String get _namaPengguna => widget.nama ?? SesiPengguna.nama ?? 'Siswa';

  String get _kelasPengguna => widget.kelas ?? SesiPengguna.kelas ?? 'Kelas -';

  int get _totalMateri => _daftarMateri.length;

  int get _totalQuiz => _daftarMateri.fold<int>(
        0,
        (sum, m) => sum + (m.hasPretest ? 1 : 0) + (m.hasPosttest ? 1 : 0),
      );

  Future<void> _saatFooterDiketuk(int idx) async {
    if (idx == 0) return;

    setState(() => _indeksFooter = idx);
    final Widget tujuan = idx == 1
        ? const LeaderboardView()
        : ProfilView(nama: _namaPengguna, kelas: _kelasPengguna);

    await Navigator.of(context).push(
      MaterialPageRoute(builder: (_) => tujuan),
    );
    if (!mounted) return;
    setState(() => _indeksFooter = 0);
  }

  /// Tampilkan modal pilihan aksi saat kartu materi di-tap.
  /// Navigasi dilakukan dari parent context agar tidak crash setelah pop.
  void _tampilkanLembarAksiMateri(BuildContext ctx, ModelMateri materi) {
    showModalBottomSheet(
      context: ctx,
      backgroundColor: Colors.transparent,
      builder: (_) => _LembarAksiMateri(
        materi: materi,
        saatPreTest: () {
          Navigator.of(ctx).push(
            MaterialPageRoute(builder: (_) => const PretestView()),
          );
        },
        saatBukaMateri: () async {
          await Navigator.of(ctx).push(
            MaterialPageRoute(builder: (_) => BukaMateriView(materi: materi)),
          );
          _muatDaftarMateri();
        },
        saatPostTest: () {
          Navigator.of(ctx).push(
            MaterialPageRoute(builder: (_) => const PosttestView()),
          );
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F7),
      // ── Footer fixed di bawah ────────────────────────────────────────────
      bottomNavigationBar: NavigasiBawah(
        indeksSaatIni: _indeksFooter,
        saatDiketuk: _saatFooterDiketuk,
      ),
      body: SafeArea(
        // top: false agar header merah maroon full-bleed ke atas layar di belakang
        // status bar (ikon baterai, jam, sinyal), sama seperti pada Pre-Test & Post-Test
        top: false,
        bottom: false, // footer menangani safe area bawah sendiri
        child: Column(
          children: [
            // ── Header Profil — hp dan status bar dihitung sendiri di dalam widget ──
            _HeaderProfil(nama: _namaPengguna, kelas: _kelasPengguna),

            // ── Konten Scrollable ─────────────────────────────────────────
            Expanded(
              child: LayoutBuilder(
                builder: (ctx, constraints) {
                  // hp dihitung dari lebar AKTUAL widget (bukan MediaQuery parent)
                  // agar selalu fresh saat navigasi masuk/keluar
                  final double hp =
                      (constraints.maxWidth * 0.075).clamp(22.0, 40.0);
                  return RefreshIndicator(
                    onRefresh: _muatDaftarMateri,
                    color: WarnaAplikasi.utama,
                    child: SingleChildScrollView(
                      clipBehavior: Clip.none,
                      physics: const AlwaysScrollableScrollPhysics(
                        parent: BouncingScrollPhysics(),
                      ),
                      child: Padding(
                        padding: EdgeInsets.fromLTRB(hp, 0, hp, 24),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Card statistik — berada persis di tengah antara header maroon dan konten
                            SizedBox(
                              height: 36,
                              child: OverflowBox(
                                minHeight: 72,
                                maxHeight: 72,
                                alignment: Alignment.bottomCenter,
                                child: _KartuStatistik(
                                  totalMateri: _totalMateri,
                                  totalQuiz: _totalQuiz,
                                ),
                              ),
                            ),
                            const SizedBox(height: 14),

                            // Label section materi
                            const Text(
                              'Materi Tersedia',
                              style: TextStyle(
                                fontSize: 15,
                                fontWeight: FontWeight.bold,
                                color: WarnaAplikasi.utama,
                              ),
                            ),
                            const SizedBox(height: 10),

                            if (_sedangMemuat)
                              const Padding(
                                padding: EdgeInsets.only(bottom: 12),
                                child: LinearProgressIndicator(
                                  color: WarnaAplikasi.utama,
                                  backgroundColor: Colors.white,
                                ),
                              ),

                            // Grid materi atau tampilan kosong jika belum di-upload guru
                            if (_sedangMemuat && _daftarMateri.isEmpty)
                              const Padding(
                                padding: EdgeInsets.symmetric(vertical: 36),
                                child: Center(
                                  child: CircularProgressIndicator(
                                    color: WarnaAplikasi.utama,
                                  ),
                                ),
                              )
                            else if (_daftarMateri.isEmpty)
                              const _KotakMateriKosong()
                            else
                              GridView.builder(
                                shrinkWrap: true,
                                physics: const NeverScrollableScrollPhysics(),
                                padding: EdgeInsets.zero,
                                itemCount: _daftarMateri.length,
                                gridDelegate:
                                    SliverGridDelegateWithFixedCrossAxisCount(
                                  crossAxisCount:
                                      constraints.maxWidth > 600 ? 4 : 2,
                                  mainAxisSpacing: 14,
                                  crossAxisSpacing: 14,
                                  childAspectRatio: 0.84,
                                ),
                                itemBuilder: (gridCtx, i) {
                                  final materi = _daftarMateri[i];
                                  return _KartuMateri(
                                    materi: materi,
                                    saatDiketuk: () =>
                                        _tampilkanLembarAksiMateri(
                                            gridCtx, materi),
                                  );
                                },
                              ),
                          ],
                        ),
                      ),
                    ),
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ────────────────────────────────────────────────────────────────────────────
// Header Profil (Full bleed sampai atas status bar, sama seperti HeaderTes)
// ────────────────────────────────────────────────────────────────────────────
class _HeaderProfil extends StatelessWidget {
  final String nama;
  final String kelas;

  const _HeaderProfil({required this.nama, required this.kelas});

  @override
  Widget build(BuildContext context) {
    final mediaQuery = MediaQuery.of(context);
    // Tinggi riil status bar (tempat ikon baterai, sinyal, jam berada)
    final double tinggiStatusBar = mediaQuery.viewPadding.top;
    final double hp = (mediaQuery.size.width * 0.075).clamp(22.0, 40.0);

    return AnnotatedRegion<SystemUiOverlayStyle>(
      // Ikon status bar (baterai, jam, sinyal) putih terang agar kontras
      // jelas di atas warna merah maroon, dengan statusBar transparan
      value: SystemUiOverlayStyle.light.copyWith(
        statusBarColor: Colors.transparent,
      ),
      child: Material(
        elevation: 6,
        shadowColor: WarnaAplikasi.bayanganLembut,
        color: WarnaAplikasi.utama,
        clipBehavior: Clip.antiAlias,
        borderRadius: const BorderRadius.only(
          bottomLeft: Radius.circular(36),
          bottomRight: Radius.circular(36),
        ),
        child: Padding(
          padding: EdgeInsets.only(
            // Bersihkan baris status bar (ikon baterai dsb) lalu beri jarak aman
            // agar teks dan konten tidak bertabrakan dengan ikon baterai
            top: tinggiStatusBar + 18,
            bottom:
                52, // 16dp jarak ke kartu + 36dp setengah tinggi kartu statistik
            left: hp,
            right: hp,
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Salam singkat
              Text(
                'Halo, ${nama.split(' ').first} 👋',
                style: const TextStyle(
                  color: Colors.white70,
                  fontSize: 14,
                  fontWeight: FontWeight.w500,
                ),
              ),
              const SizedBox(height: 12),

              // Baris avatar + nama + kelas
              Row(
                children: [
                  // Avatar profil rounded kotak berlatar dusty rose lembut & inisial dark maroon
                  Container(
                    width: 56,
                    height: 56,
                    decoration: BoxDecoration(
                      color: const Color(0xFFBA7D8E),
                      borderRadius: BorderRadius.circular(18),
                    ),
                    child: Center(
                      child: Text(
                        nama.isNotEmpty ? nama[0].toUpperCase() : 'U',
                        style: const TextStyle(
                          color: WarnaAplikasi.utamaGelap,
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 14),

                  // Nama & kelas
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          nama,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 17,
                            fontWeight: FontWeight.bold,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 4),
                        Text(
                          kelas,
                          style: const TextStyle(
                            color: Colors.white70,
                            fontSize: 12,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// ────────────────────────────────────────────────────────────────────────────
// Card Statistik (Materi & Quiz)
// ────────────────────────────────────────────────────────────────────────────
class _KartuStatistik extends StatelessWidget {
  final int totalMateri;
  final int totalQuiz;

  const _KartuStatistik({required this.totalMateri, required this.totalQuiz});

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 72,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.08),
            blurRadius: 16,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          _ItemStatistik(nilai: '$totalMateri', label: 'Materi'),
          VerticalDivider(
            width: 1,
            thickness: 1,
            color: Colors.grey.shade200,
            indent: 14,
            endIndent: 14,
          ),
          _ItemStatistik(nilai: '$totalQuiz', label: 'Total Quiz'),
        ],
      ),
    );
  }
}

class _ItemStatistik extends StatelessWidget {
  final String nilai;
  final String label;

  const _ItemStatistik({required this.nilai, required this.label});

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Text(
            nilai,
            style: const TextStyle(
              fontSize: 22,
              fontWeight: FontWeight.bold,
              color: WarnaAplikasi.utama,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w500,
              color: Colors.grey.shade500,
            ),
          ),
        ],
      ),
    );
  }
}

// ────────────────────────────────────────────────────────────────────────────
// Card Materi
// ────────────────────────────────────────────────────────────────────────────
class _KartuMateri extends StatelessWidget {
  final ModelMateri materi;
  final VoidCallback saatDiketuk;

  const _KartuMateri({required this.materi, required this.saatDiketuk});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: saatDiketuk,
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(14),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.06),
              blurRadius: 10,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Header maroon berisi kode kelas
            Container(
              padding: const EdgeInsets.fromLTRB(10, 9, 10, 13),
              decoration: const BoxDecoration(
                color: WarnaAplikasi.utama,
                borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(14),
                  topRight: Radius.circular(14),
                ),
              ),
              child: Text(
                materi.labelTampilanKelas,
                textAlign: TextAlign.center,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 11.5,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),

            // Body card
            Expanded(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(10, 10, 10, 8),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Judul di-center dalam ruang tersisa
                    Expanded(
                      child: Center(
                        child: Text(
                          materi.judul,
                          textAlign: TextAlign.center,
                          style: const TextStyle(
                            fontSize: 12.5,
                            fontWeight: FontWeight.bold,
                            color: WarnaAplikasi.utama,
                            height: 1.25,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ),
                    const Divider(height: 1),
                    const SizedBox(height: 4),
                    Text(
                      'Guru Mata Pelajaran:',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 9,
                        color: Colors.grey.shade500,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      materi.guruMapel,
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                        fontSize: 9,
                        fontWeight: FontWeight.w600,
                        color: WarnaAplikasi.utama,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ────────────────────────────────────────────────────────────────────────────
// Modal Bottom Sheet: Pilihan Aksi Materi
// ────────────────────────────────────────────────────────────────────────────
class _LembarAksiMateri extends StatelessWidget {
  final ModelMateri materi;

  /// Callback navigasi dilakukan dari parent agar context tidak invalid.
  final VoidCallback saatPreTest;
  final VoidCallback saatBukaMateri;
  final VoidCallback saatPostTest;

  const _LembarAksiMateri({
    required this.materi,
    required this.saatPreTest,
    required this.saatBukaMateri,
    required this.saatPostTest,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.fromLTRB(12, 0, 12, 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          // Handle
          Container(
            margin: const EdgeInsets.only(top: 12),
            width: 40,
            height: 4,
            decoration: BoxDecoration(
              color: Colors.grey.shade300,
              borderRadius: BorderRadius.circular(2),
            ),
          ),
          const SizedBox(height: 16),

          // Judul materi
          Text(
            materi.judul,
            style: const TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: WarnaAplikasi.utama,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            materi.kelas,
            style: TextStyle(fontSize: 12, color: Colors.grey.shade500),
          ),
          const SizedBox(height: 20),
          const Divider(height: 1),

          // Aksi: Kerjakan Pre-Test
          _UbinAksi(
            ikon: Icons.quiz_outlined,
            label: 'Kerjakan Pre-Test',
            saatDiketuk: () {
              Navigator.pop(context);
              saatPreTest();
            },
          ),
          const Divider(height: 1),

          // Aksi: Buka Materi
          _UbinAksi(
            ikon: Icons.menu_book_rounded,
            label: 'Buka Materi',
            saatDiketuk: () {
              Navigator.pop(context);
              saatBukaMateri();
            },
          ),
          const Divider(height: 1),

          // Aksi: Post-Test
          _UbinAksi(
            ikon: Icons.assignment_turned_in_outlined,
            label: 'Post-Test',
            saatDiketuk: () {
              Navigator.pop(context);
              saatPostTest();
            },
          ),
          const SizedBox(height: 12),
        ],
      ),
    );
  }
}

class _UbinAksi extends StatelessWidget {
  final IconData ikon;
  final String label;
  final VoidCallback saatDiketuk;

  const _UbinAksi({
    required this.ikon,
    required this.label,
    required this.saatDiketuk,
  });

  @override
  Widget build(BuildContext context) {
    return ListTile(
      leading: Icon(ikon, color: WarnaAplikasi.utama, size: 22),
      title: Text(
        label,
        style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500),
      ),
      trailing: Icon(Icons.chevron_right_rounded, color: Colors.grey.shade400),
      onTap: saatDiketuk,
    );
  }
}

// ────────────────────────────────────────────────────────────────────────────
// Kotak Status Materi Kosong (Ketika guru belum upload materi di database)
// ────────────────────────────────────────────────────────────────────────────
class _KotakMateriKosong extends StatelessWidget {
  const _KotakMateriKosong();

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 32),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.03),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(
            Icons.menu_book_outlined,
            size: 48,
            color: Colors.grey.shade400,
          ),
          const SizedBox(height: 12),
          Text(
            'Belum ada materi pelajaran',
            style: TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.bold,
              color: Colors.grey.shade700,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            'Materi untuk kelas Anda belum diunggah oleh guru pengajar.\nTarik layar ke bawah untuk memuat ulang.',
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 12,
              color: Colors.grey.shade500,
              height: 1.4,
            ),
          ),
        ],
      ),
    );
  }
}
