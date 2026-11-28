import 'package:file_picker/file_picker.dart';

Future<Map<String, dynamic>?> pilihFileSertifikat() async {
  final daftar = await FilePicker.pickFiles(
    type: FileType.custom,
    allowedExtensions: const ['pdf', 'png', 'jpg', 'jpeg'],
  );
  if (daftar.isEmpty) return null;

  final file = daftar.single;
  final bytes = await file.readAsBytes();
  return {
    'name': file.name,
    'bytes': bytes,
  };
}
