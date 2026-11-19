import 'dart:async';

import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/layanan_tts.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/models/soal.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/data/layanan_quiz.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/views/hasil_quiz_view.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/opsi_jawaban.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/baris_timer_audio.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/kartu_soal.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/navigasi_soal.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/tombol_kirim.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/header_tes.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';

/// Mesin tes generik yang digunakan oleh Pre-Test maupun Post-Test.
///
/// Cukup serahkan [judul] dan [daftarSoal], dan widget ini menangani
/// navigasi antar soal, pemilihan jawaban, validasi, serta dialog konfirmasi.
class TesView extends StatefulWidget {
  final String judul;
  final String? judulMateri;
  final String? tipeTest;
  final List<Soal> daftarSoal;
  final int? idQuiz;
  final int timerMenit;
  final int? sisaDetik;
  final LayananQuiz? layananQuiz;

  const TesView({
    super.key,
    required this.judul,
    this.judulMateri,
    this.tipeTest,
    required this.daftarSoal,
    this.idQuiz,
    this.timerMenit = 30,
    this.sisaDetik,
    this.layananQuiz,
  });

  @override
  State<TesView> createState() => _TesViewState();
}

class _TesViewState extends State<TesView> with WidgetsBindingObserver {
  late int _indeksSaatIni;
  late List<int?> _jawabanTerpilih;
  final ScrollController _kontrolerScroll = ScrollController();
  bool _sedangMengirim = false;

  // Timer absolut post-test (mengikuti Laravel): dihitung dari `sisa_detik`
  // yang dikirim server, bukan dari `timer` menit statis.
  Timer? _timerHitungMundur;
  DateTime? _batasWaktu;
  int? _sisaDetik;
  bool _waktuHabis = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    _indeksSaatIni = 0;
    // Satu slot per soal, null = belum dijawab.
    _jawabanTerpilih = List<int?>.filled(widget.daftarSoal.length, null);

    _sisaDetik = widget.sisaDetik;
    if (_sisaDetik != null) {
      _batasWaktu = DateTime.now().add(Duration(seconds: _sisaDetik!));
      if (_sisaDetik! <= 0) {
        WidgetsBinding.instance.addPostFrameCallback((_) => _kunciDanKirim());
      } else {
        _mulaiHitungMundur();
      }
    }
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    _timerHitungMundur?.cancel();
    unawaited(LayananTts.instance.hentikan());
    _kontrolerScroll.dispose();
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state != AppLifecycleState.resumed ||
        _waktuHabis ||
        _batasWaktu == null) {
      return;
    }

    final sisa = _hitungSisaDetik();
    if (sisa <= 0) {
      _timerHitungMundur?.cancel();
      setState(() => _sisaDetik = 0);
      unawaited(_kunciDanKirim());
      return;
    }
    if (sisa != _sisaDetik) {
      setState(() => _sisaDetik = sisa);
    }
  }

  void _mulaiHitungMundur() {
    _timerHitungMundur?.cancel();
    _timerHitungMundur = Timer.periodic(const Duration(seconds: 1), (_) {
      if (!mounted || _waktuHabis) return;

      // Utamakan jam dinding agar tetap akurat setelah app di latar
      // belakang, tetapi jangan lebih lambat dari hitungan per detik.
      final dariJam = _hitungSisaDetik();
      final dariTick = (_sisaDetik ?? 0) - 1;
      final sisa = dariJam < dariTick ? dariJam : dariTick;

      if (sisa <= 0) {
        _timerHitungMundur?.cancel();
        setState(() => _sisaDetik = 0);
        unawaited(_kunciDanKirim());
        return;
      }
      if (sisa != _sisaDetik) {
        setState(() => _sisaDetik = sisa);
      }
    });
  }

  /// Dihitung dari jam batas, sehingga tetap akurat setelah aplikasi
  /// sempat berjalan di latar belakang.
  int _hitungSisaDetik() {
    final batas = _batasWaktu;
    if (batas == null) return _sisaDetik ?? 0;
    final sisa =
        (batas.difference(DateTime.now()).inMilliseconds / 1000).ceil();
    return sisa < 0 ? 0 : sisa;
  }

  /// Kunci jawaban lalu kirim otomatis, sama seperti `lockQuizAndSubmit()`
  /// di halaman pengerjaan web.
  Future<void> _kunciDanKirim() async {
    if (_waktuHabis) return;
    _timerHitungMundur?.cancel();
    unawaited(LayananTts.instance.hentikan());
    setState(() => _waktuHabis = true);

    await Future<void>.delayed(const Duration(milliseconds: 500));
    if (!mounted) return;
    await _saatKirim(paksa: true);
  }

  bool get _semuaTerjawab => _jawabanTerpilih.every((j) => j != null);

  void _pilihJawaban(int indeksOpsi) {
    if (_waktuHabis || _sedangMengirim) return;
    setState(() {
      _jawabanTerpilih[_indeksSaatIni] = indeksOpsi;
    });
  }

  void _kembali() {
    if (_waktuHabis || _indeksSaatIni == 0) return;
    unawaited(LayananTts.instance.hentikan());
    setState(() {
      _indeksSaatIni -= 1;
    });
    _gulirKeAtas();
  }

  void _lanjut() {
    if (_waktuHabis || _indeksSaatIni >= widget.daftarSoal.length - 1) return;
    unawaited(LayananTts.instance.hentikan());
    setState(() {
      _indeksSaatIni += 1;
    });
    _gulirKeAtas();
  }

  void _gulirKeAtas() {
    if (_kontrolerScroll.hasClients) {
      _kontrolerScroll.jumpTo(0);
    }
  }

  Future<void> _saatKirim({bool paksa = false}) async {
    if (_sedangMengirim) return;
    // Kirim normal butuh semua soal terjawab. Saat waktu habis (`paksa`),
    // jawaban yang kosong tetap dikirim agar nilainya tidak hilang.
    if (!paksa && !_semuaTerjawab) return;
    final idQuiz = widget.idQuiz;
    if (idQuiz == null) {
      tampilkanPesan('Identitas quiz tidak tersedia.');
      return;
    }

    unawaited(LayananTts.instance.hentikan());
    setState(() => _sedangMengirim = true);
    try {
      final hasil = await (widget.layananQuiz ?? LayananQuiz()).kirimJawaban(
        idQuiz: idQuiz,
        soal: widget.daftarSoal,
        jawaban: _jawabanTerpilih,
      );
      if (!mounted) return;
      setState(() => _sedangMengirim = false);
      await Navigator.of(context).pushReplacement(
        MaterialPageRoute<void>(
          builder: (_) => HasilQuizView(
            judulMateri: (widget.judulMateri != null &&
                    widget.judulMateri!.trim().isNotEmpty)
                ? widget.judulMateri!
                : widget.judul,
            tipeTest:
                (widget.tipeTest != null && widget.tipeTest!.trim().isNotEmpty)
                    ? widget.tipeTest!
                    : widget.judul,
            hasil: hasil,
          ),
        ),
      );
    } on KesalahanApi catch (error) {
      if (!mounted) return;
      setState(() => _sedangMengirim = false);
      tampilkanPesan(error.pesan);
    } catch (_) {
      if (!mounted) return;
      setState(() => _sedangMengirim = false);
      tampilkanPesan('Jawaban gagal dikirim. Silakan coba lagi.');
    }
  }

  String _susunTeksSoal(Soal soal) {
    final buffer = StringBuffer('Soal nomor ${_indeksSaatIni + 1}. ');
    if (soal.kode != null && soal.kode!.trim().isNotEmpty) {
      buffer.write('Perhatikan kode program berikut: ${soal.kode}. ');
    }
    buffer.write('${soal.pertanyaan}. ');
    for (var i = 0; i < soal.pilihan.length; i++) {
      final label = i < 4 ? String.fromCharCode(65 + i) : '${i + 1}';
      buffer.write('Pilihan $label, ${soal.pilihan[i]}. ');
    }
    return buffer.toString();
  }

  Future<void> _putarAudioSoal(Soal soal) async {
    try {
      await LayananTts.instance.togglePutar(_susunTeksSoal(soal));
    } on KesalahanApi catch (error) {
      if (mounted) tampilkanPesan(error.pesan);
    } catch (_) {
      if (mounted) tampilkanPesan('Audio gagal diputar.');
    }
  }

  void tampilkanPesan(String pesan) {
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(SnackBar(content: Text(pesan)));
  }

  static const List<String> _labelOpsi = ['A', 'B', 'C', 'D'];

  @override
  Widget build(BuildContext context) {
    if (widget.daftarSoal.isEmpty) {
      return Scaffold(
        backgroundColor: WarnaAplikasi.latarBelakang,
        body: SafeArea(
          top: false,
          bottom: false,
          child: Column(
            children: [
              HeaderTes(judul: widget.judul),
              const Expanded(
                child: Center(
                  child: Text('Quiz ini belum memiliki soal.'),
                ),
              ),
            ],
          ),
        ),
      );
    }

    final soal = widget.daftarSoal[_indeksSaatIni];
    final terpilih = _jawabanTerpilih[_indeksSaatIni];
    final ukuran = MediaQuery.of(context).size;
    final bool adalahTablet = ukuran.shortestSide >= 600;
    // Margin samping proporsional agar konten bernapas dari tepi layar.
    final double paddingHorizontal = (ukuran.width * 0.075).clamp(22.0, 40.0);
    // Batasi lebar konten agar tidak terlalu lebar di tablet/landscape.
    final double lebarMaksKonten = adalahTablet ? 640 : double.infinity;

    return Scaffold(
      backgroundColor: WarnaAplikasi.latarBelakang,
      body: Stack(
        children: [
          SafeArea(
            // top dihandle manual di dalam HeaderTes: kotak maroon
            // full-bleed di belakang status bar (sesuai desain).
            top: false,
            bottom: false,
            child: Column(
              children: [
                // Header fixed — tidak di dalam scroll view.
                HeaderTes(judul: widget.judul),
                // Konten soal yang bisa di-scroll.
                Expanded(
                  child: SingleChildScrollView(
                    controller: _kontrolerScroll,
                    padding: EdgeInsets.fromLTRB(
                      paddingHorizontal,
                      18,
                      paddingHorizontal,
                      18,
                    ),
                    child: Center(
                      child: ConstrainedBox(
                        constraints: BoxConstraints(maxWidth: lebarMaksKonten),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            BarisTimerAudio(
                              saatPutarAudio: () => _putarAudioSoal(soal),
                              sisaDetik: _sisaDetik,
                            ),
                            const SizedBox(height: 14),
                            KartuSoal(
                              teksSoal: soal.pertanyaan,
                              kode: soal.kode,
                            ),
                            const SizedBox(height: 16),
                            ...List.generate(soal.pilihan.length, (i) {
                              return Padding(
                                padding: const EdgeInsets.only(bottom: 12),
                                child: OpsiJawaban(
                                  label: i < _labelOpsi.length
                                      ? _labelOpsi[i]
                                      : '${i + 1}',
                                  teks: soal.pilihan[i],
                                  terpilih: terpilih == i,
                                  saatDiketuk: () => _pilihJawaban(i),
                                ),
                              );
                            }),
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
                // Kontrol bawah fixed: baris navigasi + tombol submit.
                SafeArea(
                  top: false,
                  child: Padding(
                    padding: EdgeInsets.fromLTRB(
                      paddingHorizontal,
                      10,
                      paddingHorizontal,
                      14,
                    ),
                    child: Center(
                      child: ConstrainedBox(
                        constraints: BoxConstraints(maxWidth: lebarMaksKonten),
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            NavigasiSoal(
                              indeksSaatIni: _indeksSaatIni,
                              total: widget.daftarSoal.length,
                              saatKembali: _kembali,
                              saatLanjut: _lanjut,
                            ),
                            const SizedBox(height: 14),
                            TombolKirim(
                              aktif: _semuaTerjawab &&
                                  !_sedangMengirim &&
                                  !_waktuHabis,
                              saatDitekan: () => _saatKirim(),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
          if (_waktuHabis)
            Positioned.fill(
              child: _LapisanWaktuHabis(
                sedangMengirim: _sedangMengirim,
                saatKirimUlang: () => _saatKirim(paksa: true),
              ),
            ),
        ],
      ),
    );
  }
}

/// Overlay saat waktu pengerjaan habis — meniru overlay web: jawaban
/// dikunci dan dikirim otomatis, dengan tombol coba lagi bila gagal.
class _LapisanWaktuHabis extends StatelessWidget {
  const _LapisanWaktuHabis({
    required this.sedangMengirim,
    required this.saatKirimUlang,
  });

  final bool sedangMengirim;
  final VoidCallback saatKirimUlang;

  @override
  Widget build(BuildContext context) {
    return ColoredBox(
      color: Colors.black.withValues(alpha: 0.6),
      child: Center(
        child: Container(
          margin: const EdgeInsets.symmetric(horizontal: 32),
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(18),
            boxShadow: const [
              BoxShadow(
                color: Colors.black26,
                blurRadius: 18,
                offset: Offset(0, 6),
              ),
            ],
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 56,
                height: 56,
                decoration: const BoxDecoration(
                  color: Color(0xFFFEF3C7),
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.timer_off_outlined,
                  color: Color(0xFFB45309),
                  size: 28,
                ),
              ),
              const SizedBox(height: 14),
              const Text(
                'Waktu Habis',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF1F2937),
                ),
              ),
              const SizedBox(height: 6),
              const Text(
                'Waktu pengerjaan telah berakhir. Jawaban Anda sedang '
                'dikirim otomatis...',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 13,
                  color: Color(0xFF4B5563),
                  height: 1.45,
                ),
              ),
              const SizedBox(height: 16),
              if (sedangMengirim)
                const SizedBox(
                  width: 26,
                  height: 26,
                  child: CircularProgressIndicator(strokeWidth: 2.5),
                )
              else ...[
                OutlinedButton.icon(
                  onPressed: saatKirimUlang,
                  icon: const Icon(Icons.refresh),
                  label: const Text('Kirim ulang jawaban'),
                ),
                const SizedBox(height: 4),
                TextButton(
                  onPressed: () => Navigator.of(context).maybePop(),
                  child: const Text('Kembali'),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
