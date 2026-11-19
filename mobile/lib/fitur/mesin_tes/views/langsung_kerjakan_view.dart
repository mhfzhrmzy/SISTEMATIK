import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/data/layanan_quiz.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/views/tes_view.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';

/// Widget yang langsung mengambil quiz pertama yang tersedia untuk [idMateri]
/// dan [tipeTest], lalu membuka [TesView] tanpa halaman daftar perantara.
///
/// Jika quiz tidak ditemukan (daftar kosong) atau post-test sudah dikerjakan,
/// ditampilkan pesan informatif.
class LangsungKerjakanView extends StatefulWidget {
  const LangsungKerjakanView({
    super.key,
    required this.idMateri,
    required this.tipeTest,
    this.layananQuiz,
  });

  final int idMateri;
  final String tipeTest;
  final LayananQuiz? layananQuiz;

  @override
  State<LangsungKerjakanView> createState() => _LangsungKerjakanViewState();
}

class _LangsungKerjakanViewState extends State<LangsungKerjakanView> {
  late final LayananQuiz _layananQuiz;

  // Status pengambilan data
  bool _sedangMuat = true;
  String? _pesanError;
  bool _sudahDikerjakan = false;

  @override
  void initState() {
    super.initState();
    _layananQuiz = widget.layananQuiz ?? LayananQuiz();
    _ambilDanBuka();
  }

  Future<void> _ambilDanBuka() async {
    if (!mounted) return;
    setState(() {
      _sedangMuat = true;
      _pesanError = null;
      _sudahDikerjakan = false;
    });

    try {
      // 1. Ambil daftar quiz ringkas
      final daftar = await _layananQuiz.daftar(
        widget.idMateri,
        tipeTest: widget.tipeTest,
      );

      if (!mounted) return;

      if (daftar.isEmpty) {
        setState(() {
          _sedangMuat = false;
          _pesanError = 'Belum ada quiz untuk materi ini.';
        });
        return;
      }

      final quiz = daftar.first;

      // 2. Post-test yang sudah dikerjakan tidak bisa diulang
      if (widget.tipeTest == 'posttest' && quiz.sudahDikerjakan) {
        setState(() {
          _sedangMuat = false;
          _sudahDikerjakan = true;
        });
        return;
      }

      // 3. Ambil detail quiz
      final detail = await _layananQuiz.detail(quiz.id);

      if (!mounted) return;

      // 4. Langsung ganti layar dengan TesView (tanpa histori list di tumpukan)
      await Navigator.of(context).pushReplacement(
        MaterialPageRoute<void>(
          builder: (_) => TesView(
            key: ValueKey(detail.id),
            judul: detail.tipe == 'pretest' ? 'Pre-Test' : 'Post-Test',
            tipeTest: detail.tipe,
            judulMateri: detail.judulMateri,
            daftarSoal: detail.soal,
            idQuiz: detail.id,
            timerMenit: detail.timerMenit,
            sisaDetik: detail.sisaDetik,
            layananQuiz: _layananQuiz,
          ),
        ),
      );
    } on KesalahanApi catch (e) {
      if (!mounted) return;
      setState(() {
        _sedangMuat = false;
        _pesanError = e.pesan;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _sedangMuat = false;
        _pesanError = 'Gagal memuat quiz dari server.';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final labelTipe = widget.tipeTest == 'pretest' ? 'Pre-Test' : 'Post-Test';

    return Scaffold(
      appBar: AppBar(
        title: Text(labelTipe),
        backgroundColor: WarnaAplikasi.utama,
        foregroundColor: Colors.white,
      ),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: _sedangMuat
              ? const CircularProgressIndicator()
              : _sudahDikerjakan
                  ? _PesanSelesai(labelTipe: labelTipe)
                  : _PesanGagal(
                      pesan: _pesanError ?? 'Gagal memuat quiz dari server.',
                      onUlangi: _ambilDanBuka,
                    ),
        ),
      ),
    );
  }
}

// ---------------------------------------------------------------------------
// Widget bantu (private)
// ---------------------------------------------------------------------------

class _PesanSelesai extends StatelessWidget {
  const _PesanSelesai({required this.labelTipe});
  final String labelTipe;

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(
          Icons.check_circle_outline_rounded,
          size: 64,
          color: WarnaAplikasi.utama,
        ),
        const SizedBox(height: 16),
        Text(
          '$labelTipe sudah dikerjakan.',
          textAlign: TextAlign.center,
          style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
        ),
        const SizedBox(height: 8),
        const Text(
          'Post-Test hanya dapat dikerjakan satu kali.',
          textAlign: TextAlign.center,
          style: TextStyle(color: Colors.grey),
        ),
        const SizedBox(height: 24),
        ElevatedButton(
          style: ElevatedButton.styleFrom(
            backgroundColor: WarnaAplikasi.utama,
            foregroundColor: Colors.white,
          ),
          onPressed: () => Navigator.of(context).pop(),
          child: const Text('Kembali'),
        ),
      ],
    );
  }
}

class _PesanGagal extends StatelessWidget {
  const _PesanGagal({required this.pesan, this.onUlangi});
  final String pesan;
  final VoidCallback? onUlangi;

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Text(pesan, textAlign: TextAlign.center),
        if (onUlangi != null)
          TextButton(onPressed: onUlangi, child: const Text('Coba lagi')),
      ],
    );
  }
}
