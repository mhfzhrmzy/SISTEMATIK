import 'package:pretest_posttest_app/fitur/autentikasi/models/pengguna_siswa.dart';

/// Hasil login yang berisi token dan data pengguna.
class HasilLogin {
  final String token;
  final PenggunaSiswa pengguna;

  const HasilLogin({
    required this.token,
    required this.pengguna,
  });
}
