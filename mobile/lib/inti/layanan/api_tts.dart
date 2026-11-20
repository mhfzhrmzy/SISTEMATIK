import 'dart:async';
import 'dart:convert';

import 'package:audioplayers/audioplayers.dart';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:pretest_posttest_app/inti/konstanta/konfigurasi_api.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';

/// Status pemutaran Text-to-Speech (TTS).
enum StatusTts {
  berhenti,
  memuat,
  memutar,
  jeda,
}

/// Layanan terpusat untuk Text-To-Speech (ElevenLabs via Laravel API).
class LayananTts {
  LayananTts._() {
    _inisialisasi();
  }

  static final LayananTts instance = LayananTts._();

  final AudioPlayer _player = AudioPlayer();
  final ValueNotifier<StatusTts> status =
      ValueNotifier<StatusTts>(StatusTts.berhenti);

  String? _teksTerakhir;
  StreamSubscription<PlayerState>? _stateSubscription;

  void _inisialisasi() {
    _stateSubscription ??= _player.onPlayerStateChanged.listen((state) {
      switch (state) {
        case PlayerState.playing:
          status.value = StatusTts.memutar;
          break;
        case PlayerState.paused:
          status.value = StatusTts.jeda;
          break;
        case PlayerState.stopped:
        case PlayerState.completed:
          status.value = StatusTts.berhenti;
          break;
        default:
          break;
      }
    });
  }

  /// Memutar, menjeda, atau melanjutkan pembacaan teks materi.
  Future<void> togglePutar(String teks) async {
    final teksBersih = teks.trim();
    if (teksBersih.isEmpty) {
      throw const KesalahanApi('Teks materi kosong, tidak dapat dibacakan.');
    }

    // Jika sedang memutar teks yang sama -> jeda (pause)
    if (status.value == StatusTts.memutar && _teksTerakhir == teksBersih) {
      await _player.pause();
      return;
    }

    // Jika sedang jeda teks yang sama -> lanjutkan (resume)
    if (status.value == StatusTts.jeda && _teksTerakhir == teksBersih) {
      await _player.resume();
      return;
    }

    // Jika memutar materi baru atau sedang berhenti
    await _player.stop();
    status.value = StatusTts.memuat;
    _teksTerakhir = teksBersih;

    try {
      final bytes = await _ambilAudioDariServer(teksBersih);
      await _player.play(BytesSource(bytes));
    } catch (e) {
      status.value = StatusTts.berhenti;
      rethrow;
    }
  }

  /// Menghentikan pemutaran audio.
  Future<void> hentikan() async {
    try {
      await _player.stop();
    } catch (_) {}
    status.value = StatusTts.berhenti;
  }

  /// Mengambil file audio biner MP3 dari endpoint POST /api/tts Laravel.
  Future<Uint8List> _ambilAudioDariServer(String teks) async {
    // Batasi maksimal 2000 karakter sesuai validasi Laravel TtsController
    final potonganTeks =
        teks.length > 2000 ? teks.substring(0, 2000) : teks;

    final uri = Uri.parse('${KonfigurasiApi.alamatDasar}/tts');

    try {
      final response = await http
          .post(
            uri,
            headers: {
              'Accept': 'audio/mpeg',
              'Content-Type': 'application/json',
            },
            body: jsonEncode({'text': potonganTeks}),
          )
          .timeout(const Duration(seconds: 25));

      if (response.statusCode == 200 && response.bodyBytes.isNotEmpty) {
        return response.bodyBytes;
      }

      String pesanError = 'Gagal menghasilkan audio dari server.';
      try {
        final Map<String, dynamic> bodyJson = jsonDecode(response.body);
        if (bodyJson.containsKey('message')) {
          pesanError = bodyJson['message'].toString();
        }
      } catch (_) {}

      throw KesalahanApi(pesanError, statusCode: response.statusCode);
    } on TimeoutException {
      throw const KesalahanApi(
        'Server terlalu lama merespons. Periksa koneksi Anda.',
      );
    } on http.ClientException {
      throw const KesalahanApi('Tidak dapat terhubung ke server Laravel TTS.');
    }
  }

  void dispose() {
    _stateSubscription?.cancel();
    _stateSubscription = null;
    _player.dispose();
  }
}
