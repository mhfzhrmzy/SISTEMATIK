import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';
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
  // 0 = Keseluruhan, 1 = Permateri
  int _tabTerpilih = 0;

  // Selected sub-materi index (ketika tab Permateri dipilih)
  int _indeksMateriTerpilih = 0;

  final List<String> _daftarMateri = [
    'MS Word',
    'MS Excel',
    'Power Point',
    'Windows 11',
    'Dasar Python',
  ];

  // Dummy data Top 3
  final PenggunaPeringkat _peringkat1 = const PenggunaPeringkat(
    peringkat: 1,
    nama: 'Appip',
    skor: 100,
  );

  final PenggunaPeringkat _peringkat2 = const PenggunaPeringkat(
    peringkat: 2,
    nama: 'Agus',
    skor: 96,
  );

  final PenggunaPeringkat _peringkat3 = const PenggunaPeringkat(
    peringkat: 3,
    nama: 'Sins',
    skor: 90,
  );

  // Dummy data rank 4 ke bawah
  final List<PenggunaPeringkat> _daftarPeringkat = const [
    PenggunaPeringkat(peringkat: 4, nama: 'Ahmad Fauzi', skor: 88),
    PenggunaPeringkat(peringkat: 5, nama: 'Biolina Rahma', skor: 87),
    PenggunaPeringkat(peringkat: 6, nama: 'Candra Kartika', skor: 85),
    PenggunaPeringkat(peringkat: 7, nama: 'Dini Lestari', skor: 84),
    PenggunaPeringkat(peringkat: 8, nama: 'Eko Prasetyo', skor: 83),
    PenggunaPeringkat(peringkat: 9, nama: 'Fajar Nugroho', skor: 82),
    PenggunaPeringkat(peringkat: 10, nama: 'Gita Saraswati', skor: 80),
    PenggunaPeringkat(peringkat: 11, nama: 'Hadi Saputra', skor: 79),
    PenggunaPeringkat(peringkat: 12, nama: 'Intan Permata', skor: 78),
    PenggunaPeringkat(peringkat: 13, nama: 'Joko Susilo', skor: 77),
    PenggunaPeringkat(peringkat: 14, nama: 'Kartika Sari', skor: 75),
    PenggunaPeringkat(peringkat: 15, nama: 'Lukman Hakim', skor: 74),
    PenggunaPeringkat(peringkat: 16, nama: 'Maya Indah', skor: 73),
    PenggunaPeringkat(peringkat: 17, nama: 'Naufal Zaki', skor: 72),
    PenggunaPeringkat(peringkat: 18, nama: 'Olivia Wijaya', skor: 70),
    PenggunaPeringkat(peringkat: 19, nama: 'Pandu Dewanata', skor: 69),
    PenggunaPeringkat(peringkat: 20, nama: 'Qori Amalia', skor: 68),
  ];

  // User rank terpin di bawah
  final PenggunaPeringkat _penggunaSaatIni = const PenggunaPeringkat(
    peringkat: 21,
    nama: 'Seto Son Horeg',
    skor: 67,
  );

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
                                  // Tab Keseluruhan
                                  Expanded(
                                    child: InkWell(
                                      onTap: () {
                                        setState(() {
                                          _tabTerpilih = 0;
                                        });
                                      },
                                      borderRadius: BorderRadius.circular(10),
                                      child: Container(
                                        decoration: BoxDecoration(
                                          color: _tabTerpilih == 0
                                              ? WarnaAplikasi.utama
                                              : Colors.transparent,
                                          borderRadius:
                                              BorderRadius.circular(10),
                                        ),
                                        alignment: Alignment.center,
                                        child: Text(
                                          'Keseluruhan',
                                          style: TextStyle(
                                            color: _tabTerpilih == 0
                                                ? Colors.white
                                                : WarnaAplikasi.utama,
                                            fontWeight: FontWeight.w600,
                                            fontSize: 13.5,
                                          ),
                                        ),
                                      ),
                                    ),
                                  ),

                                  // Tab Permateri
                                  Expanded(
                                    child: InkWell(
                                      onTap: () {
                                        setState(() {
                                          _tabTerpilih = 1;
                                        });
                                      },
                                      borderRadius: BorderRadius.circular(10),
                                      child: Container(
                                        decoration: BoxDecoration(
                                          color: _tabTerpilih == 1
                                              ? WarnaAplikasi.utama
                                              : Colors.transparent,
                                          borderRadius:
                                              BorderRadius.circular(10),
                                        ),
                                        alignment: Alignment.center,
                                        child: Text(
                                          'Permateri',
                                          style: TextStyle(
                                            color: _tabTerpilih == 1
                                                ? Colors.white
                                                : WarnaAplikasi.utama,
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

                            // ── Sub-Filter Pills ─────────────────────
                            if (_tabTerpilih == 1) ...[
                              const SizedBox(height: 14),
                              SizedBox(
                                height: 32,
                                child: ListView.separated(
                                  scrollDirection: Axis.horizontal,
                                  itemCount: _daftarMateri.length,
                                  separatorBuilder: (_, __) =>
                                      const SizedBox(width: 8),
                                  itemBuilder: (ctx, idx) {
                                    final terpilih =
                                        idx == _indeksMateriTerpilih;

                                    return InkWell(
                                      onTap: () {
                                        setState(() {
                                          _indeksMateriTerpilih = idx;
                                        });
                                      },
                                      borderRadius: BorderRadius.circular(8),
                                      child: Container(
                                        padding: const EdgeInsets.symmetric(
                                          horizontal: 14,
                                          vertical: 6,
                                        ),
                                        decoration: BoxDecoration(
                                          color: terpilih
                                              ? WarnaAplikasi.utama
                                              : const Color(0xFFF1EFF1),
                                          borderRadius:
                                              BorderRadius.circular(8),
                                        ),
                                        child: Text(
                                          _daftarMateri[idx],
                                          style: TextStyle(
                                            color: terpilih
                                                ? Colors.white
                                                : WarnaAplikasi.utama
                                                    .withValues(alpha: 0.65),
                                            fontSize: 12,
                                            fontWeight: terpilih
                                                ? FontWeight.bold
                                                : FontWeight.w600,
                                          ),
                                        ),
                                      ),
                                    );
                                  },
                                ),
                              ),
                            ],

                            const SizedBox(height: 18),

                            // ── Top 3 Podium Section ─────────────────
                            FittedBox(
                              fit: BoxFit.scaleDown,
                              alignment: Alignment.center,
                              child: SizedBox(
                                height: 130,
                                child: Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  crossAxisAlignment: CrossAxisAlignment.end,
                                  children: [
                                    // Peringkat 2
                                    BatangPodium(
                                      pengguna: _peringkat2,
                                      tinggiBatang: 70,
                                      lebar: 84,
                                    ),

                                    const SizedBox(width: 12),

                                    // Peringkat 1
                                    BatangPodium(
                                      pengguna: _peringkat1,
                                      tinggiBatang: 96,
                                      lebar: 90,
                                    ),

                                    const SizedBox(width: 12),

                                    // Peringkat 3
                                    BatangPodium(
                                      pengguna: _peringkat3,
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
                            Container(
                              margin: const EdgeInsets.only(
                                bottom: 12,
                                top: 4,
                              ),
                              height: 48,
                              padding: const EdgeInsets.symmetric(
                                horizontal: 18,
                              ),
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
                                    '${_penggunaSaatIni.peringkat}',
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  const SizedBox(width: 18),
                                  Expanded(
                                    child: Text(
                                      _penggunaSaatIni.nama,
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
