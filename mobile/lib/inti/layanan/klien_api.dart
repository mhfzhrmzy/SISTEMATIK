import 'dart:async';
import 'dart:convert';
import 'dart:typed_data';

import 'package:http/http.dart' as http;
import 'package:pretest_posttest_app/inti/konstanta/konfigurasi_api.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
import 'package:pretest_posttest_app/inti/layanan/navigasi_aplikasi.dart';
import 'package:pretest_posttest_app/inti/layanan/sesi_pengguna.dart';

class KlienApi {
  KlienApi({
    http.Client? client,
    String? Function()? tokenProvider,
  })  : _client = client ?? http.Client(),
        _tokenProvider = tokenProvider ?? (() => SesiPengguna.token);

  final http.Client _client;
  final String? Function() _tokenProvider;

  Future<Map<String, dynamic>> getJson(String path) async {
    final response = await _request(
      () => _client.get(_uri(path), headers: _headers()),
    );
    return _decodeJson(response);
  }

  Future<Map<String, dynamic>> postJson(
    String path,
    Map<String, dynamic> body,
  ) async {
    final response = await _request(
      () => _client.post(
        _uri(path),
        headers: _headers(json: true),
        body: jsonEncode(body),
      ),
    );
    return _decodeJson(response);
  }

  Future<Map<String, dynamic>> putJson(
    String path,
    Map<String, dynamic> body,
  ) async {
    final response = await _request(
      () => _client.put(
        _uri(path),
        headers: _headers(json: true),
        body: jsonEncode(body),
      ),
    );
    return _decodeJson(response);
  }

  Future<Uint8List> postBytes(
    String path,
    Map<String, dynamic> body, {
    bool authenticated = true,
  }) async {
    final response = await _request(
      () => _client.post(
        _uri(path),
        headers: _headers(json: true, authenticated: authenticated),
        body: jsonEncode(body),
      ),
    );
    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw _errorFromResponse(response);
    }
    return response.bodyBytes;
  }

  Future<Map<String, dynamic>> postFile(
    String path, {
    required String field,
    required String filename,
    required Uint8List bytes,
  }) async {
    final request = http.MultipartRequest('POST', _uri(path))
      ..headers.addAll(_headers())
      ..files
          .add(http.MultipartFile.fromBytes(field, bytes, filename: filename));

    final response = await _request(() async {
      final streamed = await _client.send(request);
      return http.Response.fromStream(streamed);
    });
    return _decodeJson(response);
  }

  Future<Uint8List> getBytes(String path) async {
    final response = await _request(
      () => _client.get(_uri(path), headers: _headers()),
    );
    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw _errorFromResponse(response);
    }
    return response.bodyBytes;
  }

  Uri _uri(String path) => Uri.parse('${KonfigurasiApi.alamatDasar}/$path');

  Map<String, String> _headers({
    bool json = false,
    bool authenticated = true,
  }) {
    final headers = <String, String>{'Accept': 'application/json'};
    if (json) headers['Content-Type'] = 'application/json';
    if (!authenticated) return headers;
    final token = _tokenProvider();
    if (token == null || token.isEmpty) {
      throw const KesalahanApi(
        'Sesi login tidak ditemukan. Silakan login kembali.',
      );
    }
    headers['Authorization'] = 'Bearer $token';
    return headers;
  }

  Future<http.Response> _request(
    Future<http.Response> Function() send,
  ) async {
    try {
      return await send().timeout(const Duration(seconds: 20));
    } on TimeoutException {
      throw const KesalahanApi('Server terlalu lama merespons. Coba lagi.');
    } on http.ClientException {
      throw const KesalahanApi(
        'Tidak dapat terhubung ke server. Periksa alamat API dan koneksi.',
      );
    }
  }

  Map<String, dynamic> _decodeJson(http.Response response) {
    final decoded = bacaJson(response);
    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw _errorFromResponse(response, decoded);
    }
    if (response.body.trim().isNotEmpty) {
      final value = jsonDecode(response.body);
      if (value is! Map<String, dynamic>) {
        throw const KesalahanApi('Format respons server tidak valid.');
      }
    }
    return decoded;
  }

  KesalahanApi _errorFromResponse(
    http.Response response, [
    dynamic decoded,
  ]) {
    final data = decoded ?? bacaJson(response);
    return KesalahanApi(
      pesanKesalahan(
        data is Map<String, dynamic> ? data : const {},
        bawaan: 'Permintaan gagal (${response.statusCode}).',
      ),
      statusCode: response.statusCode,
    );
  }

  static const Duration _batasWaktu = Duration(seconds: 15);

  static Future<http.Response> get(
    String path, {
    bool tanganiSesiBerakhir = true,
  }) =>
      _kirim('GET', path, tanganiSesiBerakhir: tanganiSesiBerakhir);

  static Future<http.Response> post(
    String path, {
    Map<String, dynamic>? body,
    bool tanganiSesiBerakhir = true,
  }) =>
      _kirim(
        'POST',
        path,
        body: body,
        tanganiSesiBerakhir: tanganiSesiBerakhir,
      );

  static Future<http.Response> put(
    String path, {
    Map<String, dynamic>? body,
    bool tanganiSesiBerakhir = true,
  }) =>
      _kirim(
        'PUT',
        path,
        body: body,
        tanganiSesiBerakhir: tanganiSesiBerakhir,
      );

  static Map<String, dynamic> bacaJson(http.Response response) {
    if (response.body.trim().isEmpty) return <String, dynamic>{};
    final decoded = jsonDecode(response.body);
    return decoded is Map<String, dynamic> ? decoded : <String, dynamic>{};
  }

  static String pesanKesalahan(
    Map<String, dynamic> data, {
    String bawaan = 'Terjadi kesalahan. Silakan coba lagi.',
  }) {
    final errors = data['errors'];
    if (errors is Map && errors.isNotEmpty) {
      final first = errors.values.first;
      if (first is List && first.isNotEmpty) return first.first.toString();
    }
    final message = data['message'];
    return message is String && message.isNotEmpty ? message : bawaan;
  }

  static Future<http.Response> _kirim(
    String method,
    String path, {
    Map<String, dynamic>? body,
    required bool tanganiSesiBerakhir,
  }) async {
    final base = KonfigurasiApi.alamatDasar;
    final normalizedPath = path.startsWith('/') ? path : '/$path';
    final uri = Uri.parse('$base$normalizedPath');
    final headers = <String, String>{'Accept': 'application/json'};
    if (body != null) headers['Content-Type'] = 'application/json';
    final token = SesiPengguna.token;
    if (token != null && token.isNotEmpty) {
      headers['Authorization'] = 'Bearer $token';
    }

    late final http.Response response;
    try {
      switch (method) {
        case 'POST':
          response = await http
              .post(
                uri,
                headers: headers,
                body: body == null ? null : jsonEncode(body),
              )
              .timeout(_batasWaktu);
          break;
        case 'PUT':
          response = await http
              .put(
                uri,
                headers: headers,
                body: body == null ? null : jsonEncode(body),
              )
              .timeout(_batasWaktu);
          break;
        default:
          response = await http.get(uri, headers: headers).timeout(_batasWaktu);
      }
    } on TimeoutException {
      throw const KesalahanApi(
        'Server terlalu lama merespons. Periksa koneksi Anda.',
      );
    } on http.ClientException {
      throw const KesalahanApi(
        'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
      );
    }

    if (response.statusCode == 401 && tanganiSesiBerakhir) {
      await NavigasiAplikasi.sesiBerakhir();
    }
    return response;
  }
}

final klienApi = KlienApi();
