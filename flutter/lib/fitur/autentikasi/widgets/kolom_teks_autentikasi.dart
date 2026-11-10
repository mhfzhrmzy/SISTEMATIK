import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// TextField bulat dengan label, digunakan di halaman Login.
///
/// Warna border lembut saat normal, menjadi maroon saat fokus.
/// Jika [rahasia] = true, tampilkan ikon mata untuk toggle show/hide password.
class KolomTeksAutentikasi extends StatefulWidget {
  final String label;
  final String petunjuk;
  final bool rahasia;
  final TextEditingController? kontroler;
  final TextInputType tipeKeyboard;

  const KolomTeksAutentikasi({
    super.key,
    required this.label,
    required this.petunjuk,
    this.rahasia = false,
    this.kontroler,
    this.tipeKeyboard = TextInputType.text,
  });

  @override
  State<KolomTeksAutentikasi> createState() => _KolomTeksAutentikasiState();
}

class _KolomTeksAutentikasiState extends State<KolomTeksAutentikasi> {
  /// Apakah teks password sedang ditampilkan (bukan bintang-bintang).
  bool _tampilkanPassword = false;

  /// Warna border kartu autentikasi — menggunakan border dari WarnaAplikasi
  /// dengan opacity rendah agar kontras lembut di atas putih.
  static final Color _warnaKartuBorder =
      WarnaAplikasi.utama.withValues(alpha: 0.18);

  @override
  Widget build(BuildContext context) {
    // Jika kolom ini bertipe rahasia, obscure teks kecuali user tekan ikon mata
    final bool sembunyikanTeks = widget.rahasia && !_tampilkanPassword;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          widget.label,
          style: const TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.w600,
            color: Colors.black87,
          ),
        ),
        const SizedBox(height: 6),
        TextField(
          controller: widget.kontroler,
          obscureText: sembunyikanTeks,
          keyboardType: widget.tipeKeyboard,
          style: const TextStyle(fontSize: 13),
          decoration: InputDecoration(
            hintText: widget.petunjuk,
            hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 13),
            contentPadding:
                const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(30),
              borderSide: BorderSide(color: _warnaKartuBorder),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(30),
              borderSide: const BorderSide(
                color: WarnaAplikasi.utama,
                width: 1.5,
              ),
            ),
            filled: true,
            fillColor: Colors.white,
            // Ikon mata toggle — hanya muncul di kolom password
            suffixIcon: widget.rahasia
                ? IconButton(
                    onPressed: () {
                      setState(() => _tampilkanPassword = !_tampilkanPassword);
                    },
                    icon: Icon(
                      _tampilkanPassword
                          ? Icons.visibility_rounded
                          : Icons.visibility_off_rounded,
                      size: 20,
                      color: WarnaAplikasi.utama.withValues(alpha: 0.5),
                    ),
                    splashRadius: 20,
                    tooltip: _tampilkanPassword
                        ? 'Sembunyikan password'
                        : 'Tampilkan password',
                  )
                : null,
          ),
        ),
      ],
    );
  }
}
