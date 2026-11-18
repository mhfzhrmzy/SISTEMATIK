import 'dart:js_interop';
import 'dart:typed_data';

import 'package:web/web.dart' as web;

Future<void> simpanFile(Uint8List bytes, String namaFile) async {
  final blob = web.Blob([bytes.toJS].toJS);
  final url = web.URL.createObjectURL(blob);
  try {
    web.HTMLAnchorElement()
      ..href = url
      ..download = namaFile
      ..click();
  } finally {
    web.URL.revokeObjectURL(url);
  }
}
