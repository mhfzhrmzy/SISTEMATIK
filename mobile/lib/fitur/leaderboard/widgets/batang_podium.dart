import 'package:flutter/material.dart';
import 'package:characters/characters.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';
import 'package:pretest_posttest_app/inti/konstanta/konfigurasi_api.dart';
import 'package:pretest_posttest_app/fitur/leaderboard/models/model_leaderboard.dart';

/// Avatar lingkaran untuk siswa (foto profil / inisial / placeholder).
class AvatarSiswa extends StatelessWidget {
  final ModelLeaderboard pengguna;
  final double ukuran;
  final bool placeholder;

  const AvatarSiswa({
    super.key,
    required this.pengguna,
    this.ukuran = 40,
    this.placeholder = false,
  });

  @override
  Widget build(BuildContext context) {
    final foto = placeholder
        ? null
        : KonfigurasiApi.perbaikiUrlFoto(pengguna.fotoProfil);
    final inisial = placeholder
        ? null
        : (pengguna.nama.trim().isEmpty
            ? '?'
            : pengguna.nama.trim().characters.first.toUpperCase());

    final warnaLatar = placeholder
        ? WarnaAplikasi.utama.withValues(alpha: 0.25)
        : WarnaAplikasi.utama;

    return Container(
      width: ukuran,
      height: ukuran,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: warnaLatar,
      ),
      alignment: Alignment.center,
      child: foto == null
          ? (placeholder
                ? Icon(
                    Icons.person,
                    size: ukuran * 0.5,
                    color: WarnaAplikasi.utama.withValues(alpha: 0.5),
                  )
                : Text(
                    inisial ?? '?',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: ukuran * 0.42,
                      fontWeight: FontWeight.bold,
                    ),
                  ))
          : ClipOval(
              child: SizedBox(
                width: ukuran,
                height: ukuran,
                child: Image.network(
                  foto,
                  fit: BoxFit.cover,
                  errorBuilder: (_, __, ___) => Center(
                    child: Text(
                      inisial ?? '?',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: ukuran * 0.42,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ),
              ),
            ),
    );
  }
}

/// Widget Podium Bar tunggal untuk Top 3 (dengan avatar di atas).
class BatangPodium extends StatelessWidget {
  final ModelLeaderboard pengguna;
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
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        // Avatar
        AvatarSiswa(pengguna: pengguna, ukuran: 40),
        const SizedBox(height: 6),
        // Nama
        SizedBox(
          width: lebar,
          child: Text(
            pengguna.nama,
            textAlign: TextAlign.center,
            style: const TextStyle(
              color: WarnaAplikasi.utama,
              fontSize: 12,
              fontWeight: FontWeight.bold,
            ),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ),
        const SizedBox(height: 6),
        // Batang
        Container(
          width: lebar,
          height: tinggiBatang,
          decoration: const BoxDecoration(
            color: WarnaAplikasi.utama,
            borderRadius: BorderRadius.only(
              topLeft: Radius.circular(8),
              topRight: Radius.circular(8),
            ),
          ),
          alignment: Alignment.center,
          child: Text(
            '${pengguna.peringkat}',
            style: const TextStyle(
              color: Colors.white,
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
      ],
    );
  }
}

/// Widget Podium Kosong (placeholder) untuk Top 3.
class BatangPodiumKosong extends StatelessWidget {
  final int peringkat;
  final double tinggiBatang;
  final double lebar;

  const BatangPodiumKosong({
    super.key,
    required this.peringkat,
    required this.tinggiBatang,
    required this.lebar,
  });

  static const _placeholder = ModelLeaderboard(
    peringkat: 0,
    nisn: '',
    nama: '',
  );

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        AvatarSiswa(pengguna: _placeholder, ukuran: 40, placeholder: true),
        const SizedBox(height: 6),
        SizedBox(
          width: lebar,
          child: const Text(
            '-',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: WarnaAplikasi.utama,
              fontSize: 12,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
        const SizedBox(height: 6),
        Container(
          width: lebar,
          height: tinggiBatang,
          decoration: const BoxDecoration(
            color: WarnaAplikasi.utama,
            borderRadius: BorderRadius.only(
              topLeft: Radius.circular(8),
              topRight: Radius.circular(8),
            ),
          ),
          alignment: Alignment.center,
          child: Text(
            '$peringkat',
            style: const TextStyle(
              color: Colors.white,
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
      ],
    );
  }
}
