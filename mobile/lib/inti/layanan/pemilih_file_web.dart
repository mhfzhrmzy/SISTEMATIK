import 'dart:async';
import 'dart:js_interop';

import 'package:web/web.dart' as web;

Future<Map<String, dynamic>?> pilihFileSertifikat() async {
  final completer = Completer<Map<String, dynamic>?>();
  final input = web.document.createElement('input') as web.HTMLInputElement
    ..type = 'file'
    ..accept = '.pdf,.png,.jpg,.jpeg';

  input.onChange.listen((_) {
    final file = input.files?.item(0);
    if (file == null) {
      input.remove();
      completer.complete(null);
      return;
    }

    file.arrayBuffer().toDart.then((buffer) {
      final view = buffer.toDart.asUint8List();
      completer.complete({
        'name': file.name,
        'bytes': view,
      });
      input.remove();
    }).catchError((e) {
      completer.completeError(e);
      input.remove();
    });
  }); 

  web.document.body?.appendChild(input);
  input.click();
  return completer.future;
}
