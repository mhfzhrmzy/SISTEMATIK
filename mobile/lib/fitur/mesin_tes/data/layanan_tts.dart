import 'package:audioplayers/audioplayers.dart';
import 'package:pretest_posttest_app/inti/layanan/klien_api.dart';

class LayananTts {
  LayananTts({KlienApi? api, AudioPlayer? player})
      : _api = api ?? klienApi,
        _player = player ?? AudioPlayer();

  final KlienApi _api;
  final AudioPlayer _player;

  Future<void> ucapkan(String teks) async {
    if (teks.trim().isEmpty) return;
    final audio = await _api.postBytes(
      'tts',
      {'text': teks},
      authenticated: false,
    );
    await _player.play(BytesSource(audio, mimeType: 'audio/mpeg'));
  }

  Future<void> tutup() => _player.dispose();
}
