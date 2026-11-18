import 'package:pretest_posttest_app/fitur/mesin_tes/models/soal.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';

class QuizRingkas {
  final int id;
  final String tipe;
  final bool sudahDikerjakan;
  final int? skor;
  final int? jumlahSoal;

  const QuizRingkas({
    required this.id,
    required this.tipe,
    required this.sudahDikerjakan,
    this.skor,
    this.jumlahSoal,
  });

  factory QuizRingkas.fromJson(Map<String, dynamic> json) {
    return QuizRingkas(
      id: _toInt(json['id_quiz']),
      tipe: (json['tipe_test'] ?? '').toString(),
      sudahDikerjakan: json['attempted'] == true,
      skor: json['score'] == null ? null : _toInt(json['score']),
      jumlahSoal:
          json['jumlah_soal'] == null ? null : _toInt(json['jumlah_soal']),
    );
  }
}

class DetailQuiz {
  final int id;
  final String tipe;
  final String judulMateri;
  final int timerMenit;
  final int? sisaDetik;
  final List<Soal> soal;

  const DetailQuiz({
    required this.id,
    required this.tipe,
    required this.judulMateri,
    required this.timerMenit,
    required this.soal,
    this.sisaDetik,
  });

  factory DetailQuiz.fromJson(Map<String, dynamic> json) {
    final rawSoal = json['soals'];
    if (rawSoal is! List) {
      throw const FormatException('Daftar soal dari server tidak valid.');
    }
    return DetailQuiz(
      id: _toInt(json['id_quiz']),
      tipe: (json['tipe_test'] ?? '').toString(),
      judulMateri: (json['judul_materi'] ?? '').toString(),
      timerMenit: _toInt(json['timer']),
      sisaDetik: json['sisa_detik'] == null ? null : _toInt(json['sisa_detik']),
      soal: rawSoal
          .whereType<Map<String, dynamic>>()
          .map(Soal.fromApiJson)
          .toList(),
    );
  }
}

class HasilQuiz {
  final int benar;
  final int total;
  final int totalPoin;

  const HasilQuiz({
    required this.benar,
    required this.total,
    required this.totalPoin,
  });

  factory HasilQuiz.fromJson(Map<String, dynamic> json) => HasilQuiz(
        benar: _toInt(json['benar']),
        total: _toInt(json['total']),
        totalPoin: _toInt(json['total_poin']),
      );
}

class LayananQuiz {
  LayananQuiz({KlienApi? api}) : _api = api ?? klienApi;

  final KlienApi _api;

  Future<List<QuizRingkas>> daftar(
    int idMateri, {
    required String tipeTest,
  }) async {
    final response = await _api.getJson(
      'siswa/materi/$idMateri/quiz?tipe_test=$tipeTest',
    );
    final data = response['data'];
    if (data is! List) {
      throw const FormatException('Daftar quiz dari server tidak valid.');
    }
    return data
        .whereType<Map<String, dynamic>>()
        .map(QuizRingkas.fromJson)
        .toList();
  }

  Future<DetailQuiz> detail(int idQuiz) async {
    final response = await _api.getJson('siswa/quiz/$idQuiz');
    final data = response['data'];
    if (data is! Map<String, dynamic>) {
      throw const FormatException('Detail quiz dari server tidak valid.');
    }
    return DetailQuiz.fromJson(data);
  }

  Future<HasilQuiz> kirimJawaban({
    required int idQuiz,
    required List<Soal> soal,
    required List<int?> jawaban,
  }) async {
    final jawabanApi = <String, String>{};
    for (var index = 0; index < soal.length; index++) {
      final idSoal = soal[index].idSoal;
      final pilihan = jawaban[index];
      if (idSoal == null || pilihan == null) continue;
      jawabanApi['$idSoal'] = soal[index].kunciPilihan[pilihan];
    }
    final response = await _api.postJson(
      'siswa/quiz/$idQuiz',
      {'jawaban': jawabanApi},
    );
    return HasilQuiz.fromJson(response);
  }
}

int _toInt(dynamic value) =>
    value is num ? value.toInt() : int.tryParse('$value') ?? 0;
