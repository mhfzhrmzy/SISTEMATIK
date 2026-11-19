import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/data/layanan_quiz.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/views/tes_view.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';

class PemilihQuizView extends StatefulWidget {
  const PemilihQuizView({
    super.key,
    required this.idMateri,
    required this.tipeTest,
    this.layananQuiz,
  });

  final int idMateri;
  final String tipeTest;
  final LayananQuiz? layananQuiz;

  @override
  State<PemilihQuizView> createState() => _PemilihQuizViewState();
}

class _PemilihQuizViewState extends State<PemilihQuizView> {
  late final LayananQuiz _layananQuiz;
  late Future<List<QuizRingkas>> _quizFuture;

  @override
  void initState() {
    super.initState();
    _layananQuiz = widget.layananQuiz ?? LayananQuiz();
    _quizFuture = _layananQuiz.daftar(
      widget.idMateri,
      tipeTest: widget.tipeTest,
    );
  }

  void _muatUlang() {
    setState(() {
      _quizFuture = _layananQuiz.daftar(
        widget.idMateri,
        tipeTest: widget.tipeTest,
      );
    });
  }

  Future<void> _kerjakan(QuizRingkas quiz) async {
    await Navigator.of(context).push(
      MaterialPageRoute<void>(
        builder: (_) => _HalamanKerjakanQuiz(
          idQuiz: quiz.id,
          layananQuiz: _layananQuiz,
        ),
      ),
    );
    if (mounted) _muatUlang();
  }

  @override
  Widget build(BuildContext context) {
    final title = widget.tipeTest == 'pretest' ? 'Pre-Test' : 'Post-Test';
    return Scaffold(
      appBar: AppBar(
        title: Text(title),
        backgroundColor: WarnaAplikasi.utama,
        foregroundColor: Colors.white,
      ),
      body: FutureBuilder<List<QuizRingkas>>(
        future: _quizFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }
          if (snapshot.hasError) {
            return _PesanQuiz(
              pesan: _pesanError(snapshot.error),
              ulangi: _muatUlang,
            );
          }
          final quizzes = snapshot.data ?? const [];
          if (quizzes.isEmpty) {
            return const Center(
              child: Text('Belum ada quiz untuk materi ini.'),
            );
          }
          return ListView.separated(
            padding: const EdgeInsets.all(16),
            itemCount: quizzes.length,
            separatorBuilder: (_, __) => const SizedBox(height: 10),
            itemBuilder: (context, index) {
              final quiz = quizzes[index];
              final terkunci =
                  widget.tipeTest == 'posttest' && quiz.sudahDikerjakan;
              return Card(
                child: ListTile(
                  title: Text('$title ${index + 1}'),
                  subtitle: Text(
                    quiz.sudahDikerjakan
                        ? 'Sudah dikerjakan · Nilai ${quiz.skor ?? 0}'
                        : '${quiz.jumlahSoal ?? 0} soal',
                  ),
                  trailing: Icon(
                    terkunci ? Icons.check_circle : Icons.chevron_right,
                    color: WarnaAplikasi.utama,
                  ),
                  enabled: !terkunci,
                  onTap: terkunci ? null : () => _kerjakan(quiz),
                ),
              );
            },
          );
        },
      ),
    );
  }
}

class _HalamanKerjakanQuiz extends StatefulWidget {
  const _HalamanKerjakanQuiz({
    required this.idQuiz,
    required this.layananQuiz,
  });

  final int idQuiz;
  final LayananQuiz layananQuiz;

  @override
  State<_HalamanKerjakanQuiz> createState() => _HalamanKerjakanQuizState();
}

class _HalamanKerjakanQuizState extends State<_HalamanKerjakanQuiz> {
  late Future<DetailQuiz> _detailFuture;

  @override
  void initState() {
    super.initState();
    _detailFuture = widget.layananQuiz.detail(widget.idQuiz);
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<DetailQuiz>(
      future: _detailFuture,
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Scaffold(
            body: Center(child: CircularProgressIndicator()),
          );
        }
        if (snapshot.hasError) {
          return Scaffold(
            appBar: AppBar(title: const Text('Quiz')),
            body: _PesanQuiz(pesan: _pesanError(snapshot.error)),
          );
        }
        final detail = snapshot.data!;
        return TesView(
          key: ValueKey(detail.id),
          judul: detail.tipe == 'pretest' ? 'Pre-Test' : 'Post-Test',
          tipeTest: detail.tipe,
          judulMateri: detail.judulMateri,
          daftarSoal: detail.soal,
          idQuiz: detail.id,
          timerMenit: detail.timerMenit,
          sisaDetik: detail.sisaDetik,
          layananQuiz: widget.layananQuiz,
        );
      },
    );
  }
}

class _PesanQuiz extends StatelessWidget {
  const _PesanQuiz({required this.pesan, this.ulangi});

  final String pesan;
  final VoidCallback? ulangi;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(pesan, textAlign: TextAlign.center),
            if (ulangi != null)
              TextButton(onPressed: ulangi, child: const Text('Coba lagi')),
          ],
        ),
      ),
    );
  }
}

String _pesanError(Object? error) =>
    error is KesalahanApi ? error.pesan : 'Gagal memuat quiz dari server.';
