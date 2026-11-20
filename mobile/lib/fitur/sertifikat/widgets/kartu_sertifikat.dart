import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/item_sertifikat.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

/// Kartu item dalam daftar sertifikat.
class KartuSertifikat extends StatelessWidget {
  const KartuSertifikat({
    super.key,
    required this.item,
    required this.onTapKartu,
    required this.onUnduh,
    required this.onHapus,
  });

  final ItemSertifikat item;
  final VoidCallback onTapKartu;
  final VoidCallback onUnduh;
  final VoidCallback onHapus;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(12),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: onTapKartu,
        child: Container(
          padding: const EdgeInsets.fromLTRB(16, 14, 16, 14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: WarnaAplikasi.utama, width: 1.0),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                item.label,
                style: const TextStyle(
                  color: WarnaAplikasi.utama,
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 3),
              Text(
                item.judul,
                style: const TextStyle(
                  color: WarnaAplikasi.utama,
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 3),
              Text(
                item.tanggalTerbit,
                style: TextStyle(
                  color: WarnaAplikasi.utama.withValues(alpha: 0.7),
                  fontSize: 11,
                  fontWeight: FontWeight.w500,
                ),
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: SizedBox(
                      height: 38,
                      child: ElevatedButton(
                        onPressed: onUnduh,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: WarnaAplikasi.utama,
                          foregroundColor: Colors.white,
                          elevation: 0,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                        ),
                        child: const Text(
                          'Unduh',
                          style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: SizedBox(
                      height: 38,
                      child: OutlinedButton(
                        onPressed: onHapus,
                        style: OutlinedButton.styleFrom(
                          foregroundColor: Colors.red.shade700,
                          side: BorderSide(color: Colors.red.shade300, width: 1.2),
                          elevation: 0,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                        ),
                        child: const Text(
                          'Hapus',
                          style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              Center(
                child: Text(
                  'Ketuk kartu untuk pratinjau',
                  style: TextStyle(
                    fontSize: 10.5,
                    color: WarnaAplikasi.utama.withValues(alpha: 0.55),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
