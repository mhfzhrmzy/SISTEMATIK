import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/preferensi_lokal.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';
import 'package:pretest_posttest_app/fitur/beranda/views/beranda_view.dart';
import 'package:pretest_posttest_app/fitur/materi/data/api_materi.dart';
import 'package:pretest_posttest_app/fitur/materi/models/model_materi.dart';
import 'package:pretest_posttest_app/fitur/profil/views/profil_view.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/data/api_leaderboard.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/models/model_leaderboard.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/widgets/batang_podium.dart'
    show BatangPodium, BatangPodiumKosong, AvatarSiswa;

/// Halaman Leaderboard (papan peringkat).
///
/// 0 = Keseluruhan, 1 = Permateri
class LeaderboardView extends StatefulWidget {
  const LeaderboardView({super.key});

  @override
  State<LeaderboardView> createState() => _LeaderboardViewState();
}

class _LeaderboardViewState extends State<LeaderboardView> {
  static const Duration _intervalRefresh = Duration(seconds: 30);
  static const int _tabKeseluruhan = 0;
  static const int _tabPermateri = 1;

  final LayananLeaderboard _layanan = const LayananLeaderboard();
  final LayananMateri _layananMateri = LayananMateri();

  int _tabTerpilih = _tabKeseluruhan;
  int _indeksMateriTerpilih = 0;
  List<ModelMateri> _daftarMateri = const [];

  bool _sedangMemuat = true;
  bool _sedangMuatMateri = true;
  String? _pesanGagal;
  HasilLeaderboard? _hasil;
  Timer? _timerRefresh;

  int get _materiIdTerpilih => _daftarMateri.isEmpty
      ? 0
      : _daftarMateri[_indeksMateriTerpilih.clamp(0, _daftarMateri.length - 1)]
          .id;

  @override
  void initState() {
    super.initState();
    _pulihkanPreferensi();
    _muatDaftarMateri();
    _muatLeaderboard();
    _timerRefresh = Timer.periodic(
      _intervalRefresh,
      (_) => _muatLeaderboard(autoRefresh: true),
    );
  }

  @override
  void dispose() {
    _timerRefresh?.cancel();
    super.dispose();
  }

  Future<void> _pulihkanPreferensi() async {
    final tab =
        await PreferensiLokal.bacaInt('tab_leaderboard') ?? _tabKeseluruhan;
    final materi = await PreferensiLokal.bacaInt('materi_idx_leaderboard') ?? 0;
    if (!mounted) return;
    setState(() {
      _tabTerpilih = tab;
      _indeksMateriTerpilih = materi;
    });
  }

  Future<void> _muatDaftarMateri() async {
    setState(() => _sedangMuatMateri = true);
    try {
      final materi = await _layananMateri.ambilDaftarMateri();
      if (!mounted) return;
      setState(() {
        _daftarMateri = materi.daftar;
        if (_indeksMateriTerpilih >= _daftarMateri.length) {
          _indeksMateriTerpilih = 0;
        }
        _sedangMuatMateri = false;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() => _sedangMuatMateri = false);
    }
  }

  void _pilihMateri(int indeks) {
    setState(() {
      _indeksMateriTerpilih = indeks;
    });
    PreferensiLokal.simpanInt('materi_idx_leaderboard', indeks);
    _muatLeaderboard();
  }

  void _pilihTab(int indeks) {
    if (_tabTerpilih == indeks) return;
    setState(() {
      _tabTerpilih = indeks;
      _pesanGagal = null;
    });
    PreferensiLokal.simpanInt('tab_leaderboard', indeks);
    _muatLeaderboard();
  }

  Future<void> _muatLeaderboard({bool autoRefresh = false}) async {
    final mode = _tabTerpilih;

    setState(() {
      if (!autoRefresh) {
        _sedangMemuat = true;
        _pesanGagal = null;
      }
    });

    try {
      final hasil = mode == _tabPermateri
          ? await _layanan.ambilLeaderboardPermateri(_materiIdTerpilih)
          : await _layanan.ambilLeaderboardKeseluruhan();
      if (!mounted) return;
      setState(() {
        _hasil = hasil;
        if (!autoRefresh) {
          _sedangMemuat = false;
        }
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        if (!autoRefresh) {
          _sedangMemuat = false;
          _pesanGagal = e.toString();
        }
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
              // â”€â”€ Header Maroon â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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
                    child: Column(
                      children: [
                        const Text(
                          'Leaderboard',
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 26,
                            fontWeight: FontWeight.bold,
                            letterSpacing: -0.3,
                          ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          _hasil == null
                              ? ''
                              : (_hasil!.kelas != null &&
                                      _hasil!.kelas!.isNotEmpty
                                  ? (_hasil!.jurusan != null &&
                                          _hasil!.jurusan!.isNotEmpty
                                      ? 'Kelas ${_hasil!.kelas} â€¢ ${_hasil!.jurusan}'
                                      : 'Kelas ${_hasil!.kelas}')
                                  : ''),
                          style: const TextStyle(
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
                        child: _isiKonten(panHorizontal: paddingHorizontal),
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

  Widget _isiKonten({required double panHorizontal}) {
    final children = <Widget>[
      // â”€â”€ Toggle Keseluruhan / Permateri â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
      _toggleMode(),

      if (_tabTerpilih == _tabPermateri) ...[
        const SizedBox(height: 14),
        _pillsMateri(),
      ],

      const SizedBox(height: 18),
    ];

    if (_sedangMemuat || _sedangMuatMateri) {
      children.add(
          const Expanded(child: Center(child: CircularProgressIndicator())));
      return Column(children: children);
    }

    final pesan = _pesanGagal;
    if (pesan != null) {
      children.add(
        Expanded(
          child: Center(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(
                  Icons.error_outline,
                  color: WarnaAplikasi.utama,
                  size: 40,
                ),
                const SizedBox(height: 12),
                Text(
                  pesan,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    color: WarnaAplikasi.utama,
                    fontSize: 14,
                  ),
                ),
                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: () => _muatLeaderboard(),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: WarnaAplikasi.utama,
                    foregroundColor: Colors.white,
                  ),
                  child: const Text('Coba Lagi'),
                ),
              ],
            ),
          ),
        ),
      );
      return Column(children: children);
    }

    final hasil = _hasil;
    if (hasil == null) {
      children.add(
        const Expanded(
          child: Center(
            child: Text(
              'Memuat...',
              style: TextStyle(
                color: WarnaAplikasi.utama,
                fontSize: 14,
              ),
            ),
          ),
        ),
      );
      return Column(children: children);
    }

    final daftar = hasil.data;
    if (daftar.isEmpty) {
      // Tampilkan podium kosong + tabel placeholder 10 baris
      children.add(_podiumKosong());
      children.add(const SizedBox(height: 16));
      children.add(_tabelPlaceholder(7));
      return Column(children: children);
    }

    children.addAll(_kontenLeaderboard(daftar));

    return Column(children: children);
  }

  /// Tabel placeholder (N baris kosong, angka 4-10) — dipakai saat data kosong.
  Widget _tabelPlaceholder(int jumlah) {
    final daftarPlaceholder = List<ModelLeaderboard>.generate(
      jumlah,
      (i) => ModelLeaderboard(
        peringkat: i + 4,
        nisn: '',
        nama: '-',
        skor: 0,
      ),
    );
    return Expanded(
      child: ListView.separated(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.only(bottom: 12),
        itemCount: daftarPlaceholder.length,
        separatorBuilder: (_, __) => const SizedBox(height: 10),
        itemBuilder: (ctx, idx) {
          final item = daftarPlaceholder[idx];
          return Container(
            height: 50,
            padding: const EdgeInsets.symmetric(horizontal: 14),
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
                // Peringkat (kiri)
                SizedBox(
                  width: 28,
                  child: Text(
                    '${item.peringkat}',
                    style: TextStyle(
                      color: WarnaAplikasi.utama.withValues(alpha: 0.4),
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                    ),
                    textAlign: TextAlign.center,
                  ),
                ),
                const SizedBox(width: 10),
                // Avatar
                AvatarSiswa(
                  pengguna: item,
                  ukuran: 36,
                  placeholder: true,
                ),
                const SizedBox(width: 10),
                // Nama (tengah)
                Expanded(
                  child: Text(
                    item.nama,
                    style: TextStyle(
                      color: WarnaAplikasi.utama.withValues(alpha: 0.4),
                      fontSize: 15,
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
    );
  }

  /// Podium kosong (placeholder) untuk Top 3.
  Widget _podiumKosong() {
    return const Center(
      child: SizedBox(
        height: 160,
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            BatangPodiumKosong(peringkat: 2, tinggiBatang: 60, lebar: 92),
            SizedBox(width: 12),
            BatangPodiumKosong(peringkat: 1, tinggiBatang: 90, lebar: 100),
            SizedBox(width: 12),
            BatangPodiumKosong(peringkat: 3, tinggiBatang: 44, lebar: 92),
          ],
        ),
      ),
    );
  }

  Widget _toggleMode() {
    return Container(
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
              onTap: () => _pilihTab(_tabKeseluruhan),
              borderRadius: BorderRadius.circular(10),
              child: Container(
                decoration: BoxDecoration(
                  color: _tabTerpilih == _tabKeseluruhan
                      ? WarnaAplikasi.utama
                      : Colors.transparent,
                  borderRadius: BorderRadius.circular(10),
                ),
                alignment: Alignment.center,
                child: Text(
                  'Keseluruhan',
                  style: TextStyle(
                    color: _tabTerpilih == _tabKeseluruhan
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
              onTap: () => _pilihTab(_tabPermateri),
              borderRadius: BorderRadius.circular(10),
              child: Container(
                decoration: BoxDecoration(
                  color: _tabTerpilih == _tabPermateri
                      ? WarnaAplikasi.utama
                      : Colors.transparent,
                  borderRadius: BorderRadius.circular(10),
                ),
                alignment: Alignment.center,
                child: Text(
                  'Permateri',
                  style: TextStyle(
                    color: _tabTerpilih == _tabPermateri
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
    );
  }

  Widget _pillsMateri() {
    if (_sedangMuatMateri) {
      return const SizedBox(
        height: 32,
        child: Center(
          child: SizedBox(
            width: 16,
            height: 16,
            child: CircularProgressIndicator(strokeWidth: 2),
          ),
        ),
      );
    }

    if (_daftarMateri.isEmpty) {
      return const SizedBox(
        height: 32,
        child: Center(
          child: Text(
            'Tidak ada materi',
            style: TextStyle(
              color: WarnaAplikasi.utama,
              fontSize: 12,
            ),
          ),
        ),
      );
    }

    return SizedBox(
      height: 32,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        itemCount: _daftarMateri.length,
        separatorBuilder: (_, __) => const SizedBox(width: 8),
        itemBuilder: (ctx, idx) {
          final terpilih = idx == _indeksMateriTerpilih;

          return InkWell(
            onTap: () => _pilihMateri(idx),
            borderRadius: BorderRadius.circular(8),
            child: Container(
              padding: const EdgeInsets.symmetric(
                horizontal: 14,
                vertical: 6,
              ),
              decoration: BoxDecoration(
                color: terpilih ? WarnaAplikasi.utama : const Color(0xFFF1EFF1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                _daftarMateri[idx].judul,
                style: TextStyle(
                  color: terpilih
                      ? Colors.white
                      : WarnaAplikasi.utama.withValues(alpha: 0.65),
                  fontSize: 12,
                  fontWeight: terpilih ? FontWeight.bold : FontWeight.w600,
                ),
              ),
            ),
          );
        },
      ),
    );
  }

  List<Widget> _kontenLeaderboard(List<ModelLeaderboard> daftar) {
    // Top 3 untuk podium
    final topTiga = daftar.take(3).toList();
    final peringkatKedua = topTiga.length > 1
        ? topTiga[1]
        : const ModelLeaderboard(peringkat: 2, nisn: '', nama: '-');
    final peringkatSatu = topTiga.isNotEmpty
        ? topTiga[0]
        : const ModelLeaderboard(peringkat: 1, nisn: '', nama: '-');
    final peringkatTiga = topTiga.length > 2
        ? topTiga[2]
        : const ModelLeaderboard(peringkat: 3, nisn: '', nama: '-');

    // Current user (pinned)
    final penggunaSaatIni = daftar.firstWhere((item) => item.saya,
        orElse: () =>
            const ModelLeaderboard(peringkat: 0, nisn: '', nama: '-'));

    final widgets = <Widget>[];

    if (topTiga.isNotEmpty) {
      widgets.add(
        Center(
          child: SizedBox(
            height: 160,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                // Peringkat 2
                BatangPodium(
                  pengguna: peringkatKedua,
                  tinggiBatang: 60,
                  lebar: 92,
                ),

                const SizedBox(width: 12),

                // Peringkat 1
                BatangPodium(
                  pengguna: peringkatSatu,
                  tinggiBatang: 90,
                  lebar: 100,
                ),

                const SizedBox(width: 12),

                // Peringkat 3
                BatangPodium(
                  pengguna: peringkatTiga,
                  tinggiBatang: 44,
                  lebar: 92,
                ),
              ],
            ),
          ),
        ),
      );
      widgets.add(const SizedBox(height: 16));
    } else {
      // Podium kosong: tampilkan 3 batang placeholder
      widgets.add(
        const Center(
          child: SizedBox(
            height: 160,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                BatangPodiumKosong(peringkat: 2, tinggiBatang: 60, lebar: 92),
                SizedBox(width: 12),
                BatangPodiumKosong(peringkat: 1, tinggiBatang: 90, lebar: 100),
                SizedBox(width: 12),
                BatangPodiumKosong(peringkat: 3, tinggiBatang: 44, lebar: 92),
              ],
            ),
          ),
        ),
      );
      widgets.add(const SizedBox(height: 16));
    }

    // Tampilkan semua siswa mulai dari peringkat 4 (podium Top 3 di atas).
    final daftarTerisi = daftar.length > 3
        ? daftar.sublist(3)
        : <ModelLeaderboard>[];
    final daftarGabungan = daftarTerisi;

    widgets.add(
      Expanded(
        child: ListView.separated(
          physics: const BouncingScrollPhysics(),
          padding: const EdgeInsets.only(bottom: 12),
          itemCount: daftarGabungan.length,
          separatorBuilder: (_, __) => const SizedBox(height: 10),
          itemBuilder: (ctx, idx) {
            final item = daftarGabungan[idx];

            return Container(
              height: 50,
              padding: const EdgeInsets.symmetric(horizontal: 14),
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
                  // Peringkat (kiri)
                  SizedBox(
                    width: 28,
                    child: Text(
                      '${item.peringkat}',
                      style: const TextStyle(
                        color: WarnaAplikasi.utama,
                        fontSize: 15,
                        fontWeight: FontWeight.bold,
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ),
                  const SizedBox(width: 10),
                  // Nama (tengah)
                  Expanded(
                    child: Text(
                      item.nama,
                      style: const TextStyle(
                        color: WarnaAplikasi.utama,
                        fontSize: 15,
                        fontWeight: FontWeight.w600,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  const SizedBox(width: 10),
                  // Profil circle (kanan)
                  AvatarSiswa(pengguna: item, ukuran: 36),
                ],
              ),
            );
          },
        ),
      ),
    );

    if (penggunaSaatIni.peringkat > 0) {
      widgets.add(
        Container(
          margin: const EdgeInsets.only(bottom: 12, top: 4),
          height: 58,
          padding: const EdgeInsets.symmetric(horizontal: 14),
          decoration: BoxDecoration(
            color: WarnaAplikasi.utama,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: WarnaAplikasi.utama.withValues(alpha: 0.3),
                blurRadius: 8,
                offset: const Offset(0, 3),
              ),
            ],
          ),
          child: Row(
            children: [
              // Peringkat
              SizedBox(
                width: 28,
                child: Text(
                  '${penggunaSaatIni.peringkat}',
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 15,
                    fontWeight: FontWeight.bold,
                  ),
                  textAlign: TextAlign.center,
                ),
              ),
              const SizedBox(width: 10),
              // Nama
              Expanded(
                child: Text(
                  penggunaSaatIni.nama,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 15,
                    fontWeight: FontWeight.w600,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
              const SizedBox(width: 10),
              // Avatar
              AvatarSiswa(pengguna: penggunaSaatIni, ukuran: 36),
            ],
          ),
        ),
      );
    }

    return widgets;
  }
}
