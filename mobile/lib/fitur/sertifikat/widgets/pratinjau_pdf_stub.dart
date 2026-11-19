import 'dart:typed_data';

import 'package:flutter/material.dart';

/// Fallback non-web. Pratinjau PDF di Android/iOS memakai `PDFView`,
/// sehingga widget ini tidak pernah dirender di sana.
class PratinjauPdfWeb extends StatelessWidget {
  const PratinjauPdfWeb({super.key, required this.data});

  final Uint8List data;

  @override
  Widget build(BuildContext context) => const SizedBox.shrink();
}
