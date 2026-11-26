asiimport 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/beranda/screens/beranda_screen.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/screens/leaderboard_screen.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/services/api_quiz.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/widgets/header_tes.dart';
import 'package:pretest_posttest_app/fitur/profil/screens/profil_screen.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/widgets/navigasi_bawah.dart';

/// Halaman after submit quiz yang menampilkan ringkasan hasil pengerjaan kuis.
class HasilQuizScreen extends StatelessWidget {
  final String judulMateri;
  final String tipeTest;
  final HasilQuiz hasil;

  const HasilQuizScreen({
    super.key,
    required this.judulMateri,
    required this.tipeTest,
    required this.hasil,
  });

  String get _judulHeader {
    final tipe = tipeTest.toLowerCase().contains('pre') ? 'Pre-Test' : 'Post-Test';
    final materi = judulMateri.trim();
    if (materi.isEmpty) {
      return 'Hasil $tipe';
    }
    if (materi.toLowerCase().contains(tipe.toLowerCase())) {
      return 'Hasil $materi';
    }
    return 'Hasil $tipe $materi';
  }

  void _saatFooterDiketuk(BuildContext context, int idx) {
    if (idx == 0) {
      Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(builder: (_) => const BerandaScreen()),
        (route) => false,
      );
    } else if (idx == 1) {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const LeaderboardScreen()),
      );
    } else if (idx == 2) {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const ProfilScreen()),
      );
    }
  }

  void _saatSelesai(BuildContext context) {
    Navigator.of(context).pushAndRemoveUntil(
      MaterialPageRoute(builder: (_) => const BerandaScreen()),
      (route) => false,
    );
  }

  @override
  Widget build(BuildContext context) {
    final ukuran = MediaQuery.of(context).size;
    final bool adalahTablet = ukuran.shortestSide >= 600;
    final double paddingHorizontal = (ukuran.width * 0.075).clamp(22.0, 40.0);
    final double lebarMaksKonten = adalahTablet ? 560 : double.infinity;

    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) {
        if (didPop) return;
        _saatSelesai(context);
      },
      child: Scaffold(
        backgroundColor: WarnaAplikasi.latarBelakang,
        bottomNavigationBar: NavigasiBawah(
          indeksSaatIni: -1,
          saatDiketuk: (idx) => _saatFooterDiketuk(context, idx),
        ),
        body: SafeArea(
          top: false,
          bottom: false,
          child: Column(
            children: [
              HeaderTes(judul: _judulHeader),
              Expanded(
                child: SingleChildScrollView(
                  padding: EdgeInsets.symmetric(
                    horizontal: paddingHorizontal,
                    vertical: 24,
                  ),
                  child: Center(
                    child: ConstrainedBox(
                      constraints: BoxConstraints(maxWidth: lebarMaksKonten),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          _KartuHasil(
                            judulMateri: judulMateri.trim().isNotEmpty
                                ? judulMateri.trim()
                                : (tipeTest.toLowerCase().contains('pre')
                                    ? 'Pre-Test'
                                    : 'Post-Test'),
                            hasil: hasil,
                          ),
                          const SizedBox(height: 24),
                          SizedBox(
                            height: 48,
                            child: ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: WarnaAplikasi.utama,
                                foregroundColor: Colors.white,
                                elevation: 2,
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(12),
                                ),
                              ),
                              onPressed: () => _saatSelesai(context),
                              child: const Text(
                                'Kembali ke Beranda',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
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
      ),
    );
  }
}

class _KartuHasil extends StatelessWidget {
  final String judulMateri;
  final HasilQuiz hasil;

  const _KartuHasil({
    required this.judulMateri,
    required this.hasil,
  });

  @override
  Widget build(BuildContext context) {
    const warnaMaroon = Color(0xFF701A39);
    final salah = (hasil.total - hasil.benar).clamp(0, hasil.total);

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: const Color(0xFF334155).withValues(alpha: 0.4),
          width: 1.2,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.03),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 26),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text(
            judulMateri,
            textAlign: TextAlign.center,
            style: const TextStyle(
              color: warnaMaroon,
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 24),
          _BarisStatistik(
            ikon: Icons.assignment_outlined,
            nilai: '${hasil.total}',
            label: 'Total soal',
          ),
          const SizedBox(height: 16),
          _BarisStatistik(
            ikon: Icons.close_rounded,
            nilai: '$salah',
            label: 'Salah',
            warnaLatarKotak: const Color(0xFFFFCCD5),
          ),
          const SizedBox(height: 16),
          _BarisStatistik(
            ikon: Icons.done_all_rounded,
            nilai: '${hasil.benar}',
            label: 'Benar',
            warnaLatarKotak: const Color(0xFFD4F7DC),
          ),
          const SizedBox(height: 16),
          _BarisStatistik(
            ikon: Icons.emoji_events_outlined,
            nilai: '${hasil.totalPoin}',
            label: 'Poin',
          ),
        ],
      ),
    );
  }
}

class _BarisStatistik extends StatelessWidget {
  final IconData ikon;
  final String nilai;
  final String label;
  final Color? warnaLatarKotak;

  const _BarisStatistik({
    required this.ikon,
    required this.nilai,
    required this.label,
    this.warnaLatarKotak,
  });

  @override
  Widget build(BuildContext context) {
    const warnaMaroon = Color(0xFF701A39);

    return Row(
      children: [
        const SizedBox(width: 8),
        SizedBox(
          width: 28,
          child: Icon(ikon, color: warnaMaroon, size: 24),
        ),
        const SizedBox(width: 16),
        Container(
          width: 52,
          height: 32,
          alignment: Alignment.center,
          decoration: BoxDecoration(
            color: warnaLatarKotak ?? Colors.white,
            borderRadius: BorderRadius.circular(8),
            border: Border.all(
              color: warnaMaroon,
              width: 1.2,
            ),
          ),
          child: Text(
            nilai,
            style: const TextStyle(
              color: warnaMaroon,
              fontWeight: FontWeight.bold,
              fontSize: 14,
            ),
          ),
        ),
        const SizedBox(width: 18),
        Expanded(
          child: Text(
            label,
            style: const TextStyle(
              color: warnaMaroon,
              fontWeight: FontWeight.bold,
              fontSize: 14,
            ),
          ),
        ),
      ],
    );
  }
}
