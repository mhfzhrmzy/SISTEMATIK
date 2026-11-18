import 'dart:typed_data';

import 'penyimpan_file_stub.dart'
    if (dart.library.html) 'penyimpan_file_web.dart' as platform;

Future<void> simpanFile(Uint8List bytes, String namaFile) =>
    platform.simpanFile(bytes, namaFile);
