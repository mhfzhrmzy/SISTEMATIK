import 'dart:async';
import 'dart:convert';
import 'dart:typed_data';

import 'package:http/http.dart' as http;
import 'package:pretest_posttest_app/inti/konstanta/konfigurasi_api.dart';
import 'package:pretest_posttest_app/inti/layanan/kesalahan_api.dart';
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
          'Sesi login tidak ditemukan. Silakan login kembali.');
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
    dynamic decoded;
    try {
      decoded = response.body.trim().isEmpty
          ? <String, dynamic>{}
          : jsonDecode(response.body);
    } on FormatException {
      throw const KesalahanApi('Format respons server tidak valid.');
    }
    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw _errorFromResponse(response, decoded);
    }
    if (decoded is! Map<String, dynamic>) {
      throw const KesalahanApi('Format respons server tidak valid.');
    }
    return decoded;
  }

  KesalahanApi _errorFromResponse(
    http.Response response, [
    dynamic decoded,
  ]) {
    final data = decoded ??
        (response.body.trim().isEmpty ? null : _tryDecode(response.body));
    if (data is Map<String, dynamic>) {
      final errors = data['errors'];
      if (errors is Map && errors.isNotEmpty) {
        final messages = errors.values.first;
        if (messages is List && messages.isNotEmpty) {
          return KesalahanApi(
            messages.first.toString(),
            statusCode: response.statusCode,
          );
        }
      }
      final message = data['message'];
      if (message is String && message.isNotEmpty) {
        return KesalahanApi(message, statusCode: response.statusCode);
      }
    }
    return KesalahanApi(
      'Permintaan gagal (${response.statusCode}).',
      statusCode: response.statusCode,
    );
  }

  dynamic _tryDecode(String body) {
    try {
      return jsonDecode(body);
    } on FormatException {
      return null;
    }
  }
}

final klienApi = KlienApi();
