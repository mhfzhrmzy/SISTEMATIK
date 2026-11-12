import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/models/soal.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/opsi_jawaban.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/baris_timer_audio.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/kartu_soal.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/navigasi_soal.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/tombol_kirim.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/header_tes.dart';

/// Mesin tes generik yang digunakan oleh Pre-Test maupun Post-Test.
///
/// Cukup serahkan [judul] dan [daftarSoal], dan widget ini menangani
/// navigasi antar soal, pemilihan jawaban, validasi, serta dialog konfirmasi.
class TesView extends StatefulWidget {
  final String judul;
  final List<Soal> daftarSoal;

  const TesView({
    super.key,
    required this.judul,
    required this.daftarSoal,
  });

  @override
  State<TesView> createState() => _TesViewState();
}

class _TesViewState extends State<TesView> {
  late int _indeksSaatIni;
  late List<int?> _jawabanTerpilih;
  final ScrollController _kontrolerScroll = ScrollController();

  @override
  void initState() {
    super.initState();
    _indeksSaatIni = 0;
    // Satu slot per soal, null = belum dijawab.
    _jawabanTerpilih = List<int?>.filled(widget.daftarSoal.length, null);
  }

  @override
  void dispose() {
    _kontrolerScroll.dispose();
    super.dispose();
  }

  bool get _semuaTerjawab => _jawabanTerpilih.every((j) => j != null);

  void _pilihJawaban(int indeksOpsi) {
    setState(() {
      _jawabanTerpilih[_indeksSaatIni] = indeksOpsi;
    });
  }

  void _kembali() {
    if (_indeksSaatIni == 0) return;
    setState(() {
      _indeksSaatIni -= 1;
    });
    _gulirKeAtas();
  }

  void _lanjut() {
    if (_indeksSaatIni >= widget.daftarSoal.length - 1) return;
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

  void _saatKirim() {
    if (!_semuaTerjawab) return;
    showDialog<void>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
        ),
        title: const Text(
          'Berhasil',
          style: TextStyle(
            color: WarnaAplikasi.utama,
            fontWeight: FontWeight.bold,
          ),
        ),
        content: const Text('Jawaban berhasil dikirim.'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text(
              'OK',
              style: TextStyle(
                color: WarnaAplikasi.utama,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );
  }

  static const List<String> _labelOpsi = ['A', 'B', 'C', 'D'];

  @override
  Widget build(BuildContext context) {
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
      body: SafeArea(
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
                        BarisTimerAudio(saatPutarAudio: () {}),
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
                              label: _labelOpsi[i],
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
                          aktif: _semuaTerjawab,
                          saatDitekan: _saatKirim,
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
    );
  }
}
