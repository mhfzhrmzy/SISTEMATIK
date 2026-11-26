import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Menampilkan konfirmasi sertifikat (seperti hapus atau unduh)
Future<bool> konfirmasiAksiSertifikat(
  BuildContext context, {
  required String judulDialog,
  required String pesan,
  required String labelAksi,
}) async {
  final konfirmasi = await showDialog<bool>(
    context: context,
    builder: (ctx) => AlertDialog(
      backgroundColor: Colors.white,
      surfaceTintColor: Colors.transparent,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.all(Radius.circular(20)),
      ),
      title: Text(
        judulDialog,
        style: const TextStyle(
          color: WarnaAplikasi.utama,
          fontSize: 16,
          fontWeight: FontWeight.bold,
        ),
      ),
      content: Text(
        pesan,
        style: const TextStyle(fontSize: 13),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(ctx, false),
          child: Text('Batal', style: TextStyle(color: Colors.grey.shade600)),
        ),
        TextButton(
          onPressed: () => Navigator.pop(ctx, true),
          child: Text(
            labelAksi,
            style: const TextStyle(
              color: WarnaAplikasi.utama,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
      ],
    ),
  );
  return konfirmasi == true;
}
