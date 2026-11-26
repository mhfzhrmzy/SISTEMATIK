import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/preferensi_lokal.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';
import 'package:pretest_posttest_app/fitur/beranda/screens/beranda_screen.dart';
import 'package:pretest_posttest_app/fitur/materi/services/api_materi.dart';
import 'package:pretest_posttest_app/fitur/materi/models/model_materi.dart';
import 'package:pretest_posttest_app/fitur/profil/screens/profil_screen.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/services/api_leaderboard.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/models/model_leaderboard.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/widgets/batang_podium.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/widgets/kartu_peringkat.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/widgets/filter_leaderboard.dart';

/// Halaman Leaderboard (papan peringkat).
///
/// 0 = Keseluruhan, 1 = Permateri
class LeaderboardScreen extends StatefulWidget {
  const LeaderboardScreen({super.key});

  @override
  State<LeaderboardScreen> createState() => _LeaderboardScreenState();
}

class _LeaderboardScreenState extends State<LeaderboardScreen> {
  static const Duration _intervalRefresh = Duration(seconds: 30);
  static const int _tabKeseluruhan = 0;
  static const int _tabPermateri = 1;

  final LayananLeaderboard _layanan = const LayananLeaderboard();
  final LayananMateri _layananMateri = LayananMateri();

  int _tabTerpilih = _tabKeseluruhan;
  int _indeksMateriTerpilih = 0;
  List<ModelMateri> _daftarMateri = const [];

  bool _sedangMemuat = true;     // status fetch data leaderboard (di-reset tiap ganti tab/materi/retry)
  bool _sedangMuatMateri = true; // status fetch daftar materi (sekali di initState, tidak di-reset lagi)
  bool _sedangMuatLanjutan = false; // status fetch batch lanjutan (scroll sampai bawah)
  bool _semuaDataDimuat = false;    // true jika tidak ada data lagi yang bisa di-load
  String? _pesanGagal;
  HasilLeaderboard? _hasil;
  int _offset = 0;               // offset batch saat ini
  Timer? _timerRefresh;
  final ScrollController _scrollController = ScrollController();

  static const int _batasHalaman = 10;

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
    _timerRefresh =
        Timer.periodic(_intervalRefresh, (_) => _muatLeaderboard(autoRefresh: true));
  }

  @override
  void dispose() {
    _timerRefresh?.cancel();
    _scrollController.dispose();
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
    setState(() => _indeksMateriTerpilih = indeks);
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

    // Reset pagination saat fetch batch pertama
    _offset = 0;
    _semuaDataDimuat = false;

    if (!autoRefresh) {
      setState(() {
        _sedangMemuat = true;
        _pesanGagal = null;
      });
    }

    try {
      final hasil = mode == _tabPermateri
          ? await _layanan.ambilLeaderboardPermateri(_materiIdTerpilih, offset: 0)
          : await _layanan.ambilLeaderboardKeseluruhan(offset: 0);
      if (!mounted) return;
      setState(() {
        _hasil = hasil;
        if (hasil.data.length < hasil.totalSiswa) {
          _offset = hasil.data.length;
        } else {
          _semuaDataDimuat = true;
        }
        if (!autoRefresh) _sedangMemuat = false;
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

  /// Load batch berikutnya saat user scroll sampai bawah.
  Future<void> _muatLanjutan() async {
    if (_sedangMuatLanjutan || _semuaDataDimuat || _hasil == null) return;

    setState(() => _sedangMuatLanjutan = true);

    try {
      final mode = _tabTerpilih;
      final hasil = mode == _tabPermateri
          ? await _layanan.ambilLeaderboardPermateri(_materiIdTerpilih, offset: _offset)
          : await _layanan.ambilLeaderboardKeseluruhan(offset: _offset);
      if (!mounted) return;
      setState(() {
        _hasil = _hasil!;
        _hasil!.data.addAll(hasil.data);
        _offset += hasil.data.length;
        if (_hasil!.data.length >= _hasil!.totalSiswa) {
          _semuaDataDimuat = true;
        }
        _sedangMuatLanjutan = false;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() => _sedangMuatLanjutan = false);
    }
  }

  void _saatFooterDiketuk(int idx) {
    if (idx == 1) return;

    if (idx == 0) {
      Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(builder: (_) => const BerandaScreen()),
        (route) => false,
      );
    } else if (idx == 2) {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const ProfilScreen()),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;
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
              _header(paddingHorizontal),
              Expanded(
                child: Stack(
                  clipBehavior: Clip.none,
                  children: [
                    Positioned(
                      top: -10,
                      left: 0,
                      right: 0,
                      height: 50,
                      child: Container(color: WarnaAplikasi.utama),
                    ),
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
                        child: _isiKonten(),
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

  Widget _header(double paddingHorizontal) {
    return Container(
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
                _subtitleKelas,
                style: const TextStyle(color: Colors.white70, fontSize: 13),
              ),
            ],
          ),
        ),
      ),
    );
  }

  String get _subtitleKelas {
    final h = _hasil;
    if (h == null) return '';
    final kelas = h.kelas;
    final jurusan = h.jurusan;
    if ((kelas == null || kelas.isEmpty) &&
        (jurusan == null || jurusan.isEmpty)) {
      return '';
    }
    if (kelas == null || kelas.isEmpty) return 'Jurusan ${jurusan}';
    if (jurusan == null || jurusan.isEmpty) return 'Kelas ${kelas}';
    return 'Kelas ${kelas} | ${jurusan}';
  }

  Widget _isiKonten() {
    final children = <Widget>[
      ToggleModeLeaderboard(
        tabTerpilih: _tabTerpilih,
        onPilihTab: _pilihTab,
      ),
      if (_tabTerpilih == _tabPermateri) ...[
        const SizedBox(height: 14),
        PillsMateriLeaderboard(
          sedangMemuat: _sedangMuatMateri,
          daftarMateri: _daftarMateri,
          indeksTerpilih: _indeksMateriTerpilih,
          onPilihMateri: _pilihMateri,
        ),
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
              style: TextStyle(color: WarnaAplikasi.utama, fontSize: 14),
            ),
          ),
        ),
      );
      return Column(children: children);
    }

    final daftar = hasil.data;
    if (daftar.isEmpty) {
      // Podium kosong + tabel placeholder 10 baris (Approach A)
      children.add(_podiumKosong());
      children.add(const SizedBox(height: 16));
      children.add(_tabelPlaceholder(7));
      return Column(children: children);
    }

    children.addAll(_kontenLeaderboard(daftar));
    return Column(children: children);
  }

  // Tabel baris 4–N (N = total siswa di bawah top-3).
  // Jika N < 7, sisa slot diisi placeholder agar papan tetap terlihat penuh.
  // Jika N >= 7, semua baris terisi nyata (tidak ada placeholder).
  Widget _tabelPlaceholder(int jumlah) {
    final daftarPlaceholder = List.generate(
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
        itemBuilder: (_, idx) => KartuPeringkat(item: daftarPlaceholder[idx]),
      ),
    );
  }

  /// Podium kosong (placeholder) untuk Top 3.
  Widget _podiumKosong() {
    return Center(
      child: SizedBox(
        height: 160,
        child: FittedBox(
          fit: BoxFit.scaleDown,
          child: const Row(
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
  }

  /// Podium Top 3 dari daftar asli.
  Widget _podiumTerisi(List<ModelLeaderboard> daftar) {
    final topTiga = daftar.take(3).toList();
    final peringkatSatu = topTiga.isNotEmpty
        ? topTiga[0]
        : const ModelLeaderboard(peringkat: 1, nisn: '', nama: '-');
    final peringkatKedua = topTiga.length > 1
        ? topTiga[1]
        : const ModelLeaderboard(peringkat: 2, nisn: '', nama: '-');
    final peringkatTiga = topTiga.length > 2
        ? topTiga[2]
        : const ModelLeaderboard(peringkat: 3, nisn: '', nama: '-');

    return Center(
      child: SizedBox(
        height: 160,
        child: FittedBox(
          fit: BoxFit.scaleDown,
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              BatangPodium(
                pengguna:
                    peringkatKedua.nisn.isEmpty
                        ? const ModelLeaderboard(
                            peringkat: 2, nisn: '', nama: '-')
                        : peringkatKedua,
                tinggiBatang: 60,
                lebar: 92,
              ),
              const SizedBox(width: 12),
              BatangPodium(
                pengguna: peringkatSatu,
                tinggiBatang: 90,
                lebar: 100,
              ),
              const SizedBox(width: 12),
              BatangPodium(
                pengguna:
                    peringkatTiga.nisn.isEmpty
                        ? const ModelLeaderboard(
                            peringkat: 3, nisn: '', nama: '-')
                        : peringkatTiga,
                tinggiBatang: 44,
                lebar: 92,
              ),
            ],
          ),
        ),
      ),
    );
  }

  /// Susunan konten leaderboard (podium + kartu 4–N + kartu pinned bawah).
  List<Widget> _kontenLeaderboard(List<ModelLeaderboard> daftar) {
    final widgets = <Widget>[];

    widgets.add(_podiumTerisi(daftar));
    widgets.add(const SizedBox(height: 16));

    final daftarTerisi = daftar.length > 3 ? daftar.sublist(3) : <ModelLeaderboard>[];
    final jumlahBarisNyata = daftarTerisi.length;
    final jumlahSlot = jumlahBarisNyata > 7 ? jumlahBarisNyata : 7;
    final placeholderKosong =
        (jumlahSlot - jumlahBarisNyata).clamp(0, jumlahSlot);
    final daftarGabungan = [
      ...daftarTerisi,
      ...List.generate(
        placeholderKosong,
        (i) => ModelLeaderboard(
          peringkat: jumlahBarisNyata + i + 4,
          nisn: '',
          nama: '-',
          skor: 0,
        ),
      ),
    ];

    // Scroll listener untuk infinite scroll / load more
    _scrollController.removeListener(_listenerScroll);
    _scrollController.addListener(_listenerScroll);

    // Hitung total item dalam ListView (tambah 1 item loading indicator di bawah jika masih bisa load)
    final itemCount = daftarGabungan.length + (!_semuaDataDimuat ? 1 : 0);

    widgets.add(
      Expanded(
        child: ListView.separated(
          controller: _scrollController,
          physics: const BouncingScrollPhysics(),
          padding: const EdgeInsets.only(bottom: 12),
          itemCount: itemCount,
          separatorBuilder: (_, __) => const SizedBox(height: 10),
          itemBuilder: (_, idx) {
            if (idx < daftarGabungan.length) {
              return KartuPeringkat(item: daftarGabungan[idx]);
            } else {
              // Loading spinner kecil di bawah saat memuat batch berikutnya
              return const Padding(
                padding: EdgeInsets.symmetric(vertical: 12),
                child: Center(
                  child: SizedBox(
                    width: 24,
                    height: 24,
                    child: CircularProgressIndicator(strokeWidth: 2),
                  ),
                ),
              );
            }
          },
        ),
      ),
    );

    // Kartu pinned bawah (user)
    final penggunaSaatIni = daftar.firstWhere(
      (item) => item.saya,
      orElse: () => const ModelLeaderboard(peringkat: 0, nisn: '', nama: '-'),
    );
    if (penggunaSaatIni.peringkat > 0) {
      widgets.add(KartuPeringkat(item: penggunaSaatIni, kartuSayaDiBawah: true));
    }

    return widgets;
  }

  void _listenerScroll() {
    if (_scrollController.position.pixels >=
        _scrollController.position.maxScrollExtent - 100) {
      _muatLanjutan();
    }
  }
}
