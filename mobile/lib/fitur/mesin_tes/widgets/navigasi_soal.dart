import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Baris bawah dengan kontrol Back / "Soal X/Y" / Next.
///
/// Back dinonaktifkan pada soal pertama, Next dinonaktifkan pada
/// soal terakhir. Status nonaktif ditampilkan dengan warna redup
/// dan callback tidak dipanggil.
class NavigasiSoal extends StatelessWidget {
  final int indeksSaatIni; // 0-based
  final int total;
  final VoidCallback saatKembali;
  final VoidCallback saatLanjut;

  const NavigasiSoal({
    super.key,
    required this.indeksSaatIni,
    required this.total,
    required this.saatKembali,
    required this.saatLanjut,
  });

  bool get _adalahPertama => indeksSaatIni <= 0;
  bool get _adalahTerakhir => indeksSaatIni >= total - 1;

  @override
  Widget build(BuildContext context) {
    final lebar = MediaQuery.of(context).size.width;
    final bool kompak = lebar < 340;

    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        _TombolNavigasi(
          label: 'Back',
          ikon: Icons.arrow_back,
          ikonDiDepan: true,
          aktif: !_adalahPertama,
          saatDiketuk: saatKembali,
          kompak: kompak,
        ),
        Flexible(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 8),
            child: Text(
              'Soal ${indeksSaatIni + 1}/$total',
              textAlign: TextAlign.center,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                color: WarnaAplikasi.utama,
                fontWeight: FontWeight.w700,
                fontSize: 14,
              ),
            ),
          ),
        ),
        _TombolNavigasi(
          label: 'Next',
          ikon: Icons.arrow_forward,
          ikonDiDepan: false,
          aktif: !_adalahTerakhir,
          saatDiketuk: saatLanjut,
          kompak: kompak,
        ),
      ],
    );
  }
}

class _TombolNavigasi extends StatelessWidget {
  final String label;
  final IconData ikon;
  final bool ikonDiDepan;
  final bool aktif;
  final VoidCallback saatDiketuk;
  final bool kompak;

  const _TombolNavigasi({
    required this.label,
    required this.ikon,
    required this.ikonDiDepan,
    required this.aktif,
    required this.saatDiketuk,
    required this.kompak,
  });

  @override
  Widget build(BuildContext context) {
    final Color warna = aktif ? WarnaAplikasi.utama : WarnaAplikasi.utamaRedup;

    final daftarAnak = <Widget>[
      Icon(ikon, size: 18, color: warna),
      const SizedBox(width: 6),
      Text(
        label,
        style: TextStyle(
          color: warna,
          fontWeight: FontWeight.w700,
          fontSize: 14,
        ),
      ),
    ];

    return Material(
      color: WarnaAplikasi.latarBelakang,
      borderRadius: BorderRadius.circular(24),
      child: InkWell(
        borderRadius: BorderRadius.circular(24),
        onTap: aktif ? saatDiketuk : null,
        child: Container(
          padding: EdgeInsets.symmetric(
            horizontal: kompak ? 10 : 16,
            vertical: 10,
          ),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: warna, width: 1.2),
          ),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: ikonDiDepan ? daftarAnak : daftarAnak.reversed.toList(),
          ),
        ),
      ),
    );
  }
}
