import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/fitur/papan_peringkat/models/pengguna_peringkat.dart';

/// Widget Podium Bar tunggal untuk Top 3.
class BatangPodium extends StatelessWidget {
  final PenggunaPeringkat pengguna;
  final double tinggiBatang;
  final double lebar;

  const BatangPodium({
    super.key,
    required this.pengguna,
    required this.tinggiBatang,
    required this.lebar,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      mainAxisAlignment: MainAxisAlignment.end,
      children: [
        // Nama siswa
        SizedBox(
          width: lebar + 10,
          child: Text(
            pengguna.nama,
            textAlign: TextAlign.center,
            style: const TextStyle(
              color: WarnaAplikasi.utama,
              fontSize: 12.5,
              fontWeight: FontWeight.bold,
            ),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ),

        const SizedBox(height: 6),

        // Batang Podium Maroon
        Container(
          width: lebar,
          height: tinggiBatang,
          decoration: const BoxDecoration(
            color: WarnaAplikasi.utama,
            borderRadius: BorderRadius.only(
              topLeft: Radius.circular(10),
              topRight: Radius.circular(10),
            ),
          ),
          child: Center(
            child: Text(
              '${pengguna.peringkat}',
              style: const TextStyle(
                color: Colors.white,
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),
      ],
    );
  }
}
