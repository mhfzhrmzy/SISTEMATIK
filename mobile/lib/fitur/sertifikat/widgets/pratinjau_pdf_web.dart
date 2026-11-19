import 'dart:js_interop';
import 'dart:typed_data';
import 'dart:ui_web' as ui_web;

import 'package:flutter/material.dart';
import 'package:web/web.dart' as web;

/// Menampilkan pratinjau PDF di web memakai iframe + blob URL.
///
/// Byte PDF diubah menjadi blob agar iframe memakai penampil PDF bawaan
/// peramban tanpa perlu mengakses URL file secara langsung.
class PratinjauPdfWeb extends StatefulWidget {
  const PratinjauPdfWeb({super.key, required this.data});

  final Uint8List data;

  @override
  State<PratinjauPdfWeb> createState() => _PratinjauPdfWebState();
}

class _PratinjauPdfWebState extends State<PratinjauPdfWeb> {
  String? _urlBlob;
  String? _tipeView;

  @override
  void initState() {
    super.initState();
    _siapkanPratinjau();
  }

  void _siapkanPratinjau() {
    final blob = web.Blob(
      [widget.data.toJS].toJS,
      web.BlobPropertyBag(type: 'application/pdf'),
    );
    final url = web.URL.createObjectURL(blob);
    final tipeView = 'pratinjau-pdf-${DateTime.now().microsecondsSinceEpoch}';

    ui_web.platformViewRegistry.registerViewFactory(tipeView, (int _) {
      return web.HTMLIFrameElement()
        ..src = url
        ..style.border = 'none'
        ..style.width = '100%'
        ..style.height = '100%';
    });

    _urlBlob = url;
    _tipeView = tipeView;
  }

  @override
  void dispose() {
    final url = _urlBlob;
    if (url != null) {
      web.URL.revokeObjectURL(url);
    }
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final tipeView = _tipeView;
    if (tipeView == null) {
      return const Center(child: CircularProgressIndicator());
    }
    return HtmlElementView(viewType: tipeView);
  }
}
