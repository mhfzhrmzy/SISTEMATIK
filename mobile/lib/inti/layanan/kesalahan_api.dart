/// Kesalahan yang berasal dari API atau koneksi jaringan.
class KesalahanApi implements Exception {
  final String pesan;
  final int? statusCode;

  const KesalahanApi(this.pesan, {this.statusCode});

  @override
  String toString() => pesan;
}
