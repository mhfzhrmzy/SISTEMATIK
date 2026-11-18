import 'dart:io';
import 'dart:typed_data';

import 'package:open_filex/open_filex.dart';
import 'package:path_provider/path_provider.dart';

Future<void> simpanFile(Uint8List bytes, String namaFile) async {
  final directory = await getTemporaryDirectory();
  final file = File('${directory.path}${Platform.pathSeparator}$namaFile');
  await file.writeAsBytes(bytes, flush: true);
  final result = await OpenFilex.open(file.path);
  if (result.type != ResultType.done) {
    throw FileSystemException(
      result.message,
      file.path,
    );
  }
}
