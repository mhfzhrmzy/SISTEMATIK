import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:pretest_posttest_app/fitur/papan_peringkat/data/layanan_papan_peringkat.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/fitur/beranda/views/beranda_view.dart';
import 'package:pretest_posttest_app/fitur/profil/views/profil_view.dart';
import 'package:pretest_posttest_app/fitur/papan_peringkat/models/pengguna_peringkat.dart';
import 'package:pretest_posttest_app/fitur/papan_peringkat/widgets/batang_podium.dart';

/// Halaman papan peringkat (leaderboard).
class PapanPeringkatView extends StatefulWidget {
  const PapanPeringkatView({super.key});

  @override
  State<PapanPeringkatView> createState() => _PapanPeringkatViewState();
}

class _PapanPeringkatViewState extends State<PapanPeringkatView> {
  final LayananPapanPeringkat _layanan = LayananPapanPeringkat();
  List<PenggunaPeringkat> _peringkat = [];
  bool _memuat = true;
  String? _pesanGalat;

  List<PenggunaPeringkat> get _daftarPeringkat =>
      _peringkat.where((item) => item.peringkat > 3 && !item.isMe).toList();

  PenggunaPeringkat? get _penggunaSaatIni {
    for (final item in _peringkat) {
      if (item.isMe) return item;
    }
    return null;
  }

  @override
  void initState() {
    super.initState();
    _muatPeringkat();
  }

  Future<void> _muatPeringkat() async {
    setState(() {
      _memuat = true;
      _pesanGalat = null;
    });
    try {
      final hasil = await _layanan.ambil();
      if (!mounted) return;
      setState(() {
        _peringkat = hasil.siswa;
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
        _pesanGalat = 'Gagal memuat leaderboard.';
        _memuat = false;
      });
    }
  }

  void _saatFooterDiketuk(int idx) {
    if (idx == 1) return;

    if (idx == 0) {
      Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(
          builder: (_) => const BerandaView(),
        ),
        (route) => false,
      );
    } else if (idx == 2) {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(
          builder: (_) => const ProfilView(),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;

    // Padding horizontal proporsional sama persis dengan Pre-Test dan Post-Test
    final double paddingHorizontal = (size.width * 0.075).clamp(22.0, 40.0);

    return Scaffold(
      backgroundColor: Colors.white,
      bottomNavigationBar: NavigasiBawah(
        indeksSaatIni: 1,
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
              // ── Header Maroon ─────────────────────────────────────
              Container(
                width: double.infinity,
                color: WarnaAplikasi.utama,
                child: SafeArea(
                  bottom: false,
                  child: Padding(
                    padding: EdgeInsets.fromLTRB(
                      paddingHorizontal,
                      16,
                      paddingHorizontal,
                      24,
                    ),
                    child: const Column(
                      children: [
                        Text(
                          'Leaderboard',
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 26,
                            fontWeight: FontWeight.bold,
                            letterSpacing: -0.3,
                          ),
                        ),
                        SizedBox(height: 6),
                        Text(
                          'Peringkat siswa',
                          style: TextStyle(
                            color: Colors.white70,
                            fontSize: 13,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),

              Expanded(
                child: Stack(
                  clipBehavior: Clip.none,
                  children: [
                    Positioned(
                      top: -10,
                      left: 0,
                      right: 0,
                      height: 50,
                      child: Container(
                        color: WarnaAplikasi.utama,
                      ),
                    ),

                    // Container putih utama
                    Container(
                      width: double.infinity,
                      height: double.infinity,
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.only(
                          topLeft: Radius.circular(24),
                          topRight: Radius.circular(24),
                        ),
                      ),
                      child: Padding(
                        padding: EdgeInsets.fromLTRB(
                          paddingHorizontal,
                          20,
                          paddingHorizontal,
                          0,
                        ),
                        child: Column(
                          children: [
                            // ── Toggle Switch ───────────────────────
                            Container(
                              height: 44,
                              padding: const EdgeInsets.all(3),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(14),
                                border: Border.all(
                                  color: WarnaAplikasi.utama,
                                  width: 1.5,
                                ),
                              ),
                              child: Row(
                                children: [
                                  Expanded(
                                    child: InkWell(
                                      onTap: () {},
                                      borderRadius: BorderRadius.circular(10),
                                      child: Container(
                                        decoration: BoxDecoration(
                                          color: WarnaAplikasi.utama,
                                          borderRadius:
                                              BorderRadius.circular(10),
                                        ),
                                        alignment: Alignment.center,
                                        child: const Text(
                                          'Keseluruhan',
                                          style: TextStyle(
                                            color: Colors.white,
                                            fontWeight: FontWeight.w600,
                                            fontSize: 13.5,
                                          ),
                                        ),
                                      ),
                                    ),
                                  ),
                                  Expanded(
                                    child: InkWell(
                                      onTap: () => ScaffoldMessenger.of(context)
                                        ..hideCurrentSnackBar()
                                        ..showSnackBar(
                                          const SnackBar(
                                            content: Text(
                                              'API leaderboard belum mendukung filter per materi.',
                                            ),
                                          ),
                                        ),
                                      borderRadius: BorderRadius.circular(10),
                                      child: Container(
                                        alignment: Alignment.center,
                                        child: const Text(
                                          'Permateri',
                                          style: TextStyle(
                                            color: WarnaAplikasi.utama,
                                            fontWeight: FontWeight.w600,
                                            fontSize: 13.5,
                                          ),
                                        ),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),

                            const SizedBox(height: 18),

                            if (_memuat)
                              const Padding(
                                padding: EdgeInsets.all(24),
                                child: CircularProgressIndicator(),
                              )
                            else if (_pesanGalat != null)
                              Column(
                                children: [
                                  Text(_pesanGalat!),
                                  TextButton(
                                    onPressed: _muatPeringkat,
                                    child: const Text('Coba lagi'),
                                  ),
                                ],
                              )
                            else if (_peringkat.isEmpty)
                              const Padding(
                                padding: EdgeInsets.all(24),
                                child: Text('Belum ada nilai di leaderboard.'),
                              )
                            else if (_peringkat.length >= 3)
                              FittedBox(
                                fit: BoxFit.scaleDown,
                                alignment: Alignment.center,
                                child: SizedBox(
                                  height: 130,
                                  child: Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    crossAxisAlignment: CrossAxisAlignment.end,
                                    children: [
                                      BatangPodium(
                                        pengguna: _peringkat[1],
                                        tinggiBatang: 70,
                                        lebar: 84,
                                      ),
                                      const SizedBox(width: 12),
                                      BatangPodium(
                                        pengguna: _peringkat[0],
                                        tinggiBatang: 96,
                                        lebar: 90,
                                      ),
                                      const SizedBox(width: 12),
                                      BatangPodium(
                                        pengguna: _peringkat[2],
                                        tinggiBatang: 52,
                                        lebar: 84,
                                      ),
                                    ],
                                  ),
                                ),
                              ),

                            const SizedBox(height: 16),

                            // ── List Rank 4 ke bawah ─────────────────
                            Expanded(
                              child: ListView.separated(
                                physics: const BouncingScrollPhysics(),
                                padding: const EdgeInsets.only(bottom: 12),
                                itemCount: _daftarPeringkat.length,
                                separatorBuilder: (_, __) =>
                                    const SizedBox(height: 10),
                                itemBuilder: (ctx, idx) {
                                  final item = _daftarPeringkat[idx];

                                  return Container(
                                    height: 46,
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 16,
                                    ),
                                    decoration: BoxDecoration(
                                      color: Colors.white,
                                      borderRadius: BorderRadius.circular(12),
                                      border: Border.all(
                                        color: WarnaAplikasi.utama,
                                        width: 1.2,
                                      ),
                                    ),
                                    child: Row(
                                      children: [
                                        Text(
                                          '${item.peringkat}',
                                          style: const TextStyle(
                                            color: WarnaAplikasi.utama,
                                            fontSize: 15,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                        const SizedBox(width: 16),
                                        Expanded(
                                          child: Text(
                                            item.nama,
                                            style: const TextStyle(
                                              color: WarnaAplikasi.utama,
                                              fontSize: 14,
                                              fontWeight: FontWeight.w600,
                                            ),
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                        ),
                                      ],
                                    ),
                                  );
                                },
                              ),
                            ),

                            // ── Pinned Current User Rank Bar ─────────
                            if (_penggunaSaatIni case final pengguna?
                                when pengguna.peringkat > 3)
                              Container(
                                margin: const EdgeInsets.only(
                                  bottom: 12,
                                  top: 4,
                                ),
                                height: 48,
                                padding:
                                    const EdgeInsets.symmetric(horizontal: 18),
                                decoration: BoxDecoration(
                                  color: WarnaAplikasi.utama,
                                  borderRadius: BorderRadius.circular(12),
                                  boxShadow: [
                                    BoxShadow(
                                      color: WarnaAplikasi.utama
                                          .withValues(alpha: 0.3),
                                      blurRadius: 8,
                                      offset: const Offset(0, 3),
                                    ),
                                  ],
                                ),
                                child: Row(
                                  children: [
                                    Text(
                                      '${pengguna.peringkat}',
                                      style: const TextStyle(
                                        color: Colors.white,
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    const SizedBox(width: 18),
                                    Expanded(
                                      child: Text(
                                        pengguna.nama,
                                        style: const TextStyle(
                                          color: Colors.white,
                                          fontSize: 15,
                                          fontWeight: FontWeight.bold,
                                        ),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
