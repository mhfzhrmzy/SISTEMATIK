import 'dart:convert';
import 'dart:typed_data';

import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:pretest_posttest_app/fitur/mesin_tes/data/layanan_quiz.dart';
import 'package:pretest_posttest_app/fitur/mesin_tes/models/soal.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';

class _ClientPalsu extends http.BaseClient {
  _ClientPalsu(this.penanganRequest);

  final Future<http.Response> Function(http.BaseRequest) penanganRequest;

  @override
  Future<http.StreamedResponse> send(http.BaseRequest request) async {
    final response = await penanganRequest(request);
    return http.StreamedResponse(
      Stream<Uint8List>.value(response.bodyBytes),
      response.statusCode,
      headers: response.headers,
    );
  }
}

void main() {
  test('submit quiz mengirim token dan kunci pilihan dari opsi terpilih',
      () async {
    String? requestPath;
    Map<String, dynamic>? requestBody;
    final client = _ClientPalsu((request) async {
      requestPath = request.url.path;
      expect(request.headers['Authorization'], 'Bearer token-tes');
      expect(request.headers['Accept'], 'application/json');
      requestBody =
          jsonDecode((request as http.Request).body) as Map<String, dynamic>;
      return http.Response(
        jsonEncode({'benar': 1, 'total': 1, 'total_poin': 100}),
        200,
        headers: {'content-type': 'application/json'},
      );
    });
    final quiz = LayananQuiz(
      api: KlienApi(client: client, tokenProvider: () => 'token-tes'),
    );
    final soal = Soal.fromApiJson({
      'id_soal': 17,
      'pertanyaan': 'Pertanyaan?',
      'choices': {
        'b': 'Opsi yang tampil pertama',
        'a': 'Opsi berikutnya',
        'c': 'Opsi ketiga',
        'd': 'Opsi keempat',
      },
    });

    final hasil = await quiz.kirimJawaban(
      idQuiz: 4,
      soal: [soal],
      jawaban: [0],
    );

    expect(soal.pilihan.first, 'Opsi yang tampil pertama');
    expect(requestPath, endsWith('/api/siswa/quiz/4'));
    expect(requestBody, {
      'jawaban': {'17': 'b'},
    });
    expect(hasil.totalPoin, 100);
  });
}
