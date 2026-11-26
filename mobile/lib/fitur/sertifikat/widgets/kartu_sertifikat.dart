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

  static const _hitam = Color(0xFF1F2937);
  static const _abu = Color(0xFF4B5563);

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
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: WarnaAplikasi.utama, width: 1.2),
            boxShadow: [
              BoxShadow(
                color: WarnaAplikasi.utama.withValues(alpha: 0.08),
                blurRadius: 8,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
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
                            color: _hitam,
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 3),
                        Text(
                          item.tanggalTerbit,
                          style: const TextStyle(
                            color: _abu,
                            fontSize: 11,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ],
                    ),
                  ),
                  PopupMenuButton<String>(
                    tooltip: 'Opsi sertifikat',
                    enabled: item.id != null,
                    color: Colors.white,
                    surfaceTintColor: Colors.transparent,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                      side: const BorderSide(
                        color: WarnaAplikasi.utama,
                        width: 1.2,
                      ),
                    ),
                    onSelected: (nilai) {
                      if (nilai == 'unduh') {
                        onUnduh();
                      } else if (nilai == 'hapus') {
                        onHapus();
                      }
                    },
                    child: Icon(
                      Icons.more_vert_rounded,
                      color: item.id == null
                          ? Colors.grey.shade400
                          : WarnaAplikasi.utama,
                    ),
                    itemBuilder: (context) => [
                      PopupMenuItem(
                        value: 'unduh',
                        child: Row(
                          children: [
                            const Icon(Icons.download_rounded, size: 18),
                            const SizedBox(width: 8),
                            const Text('Unduh'),
                          ],
                        ),
                      ),
                      PopupMenuItem(
                        value: 'hapus',
                        child: Row(
                          children: [
                            const Icon(Icons.delete_outline_rounded, size: 18),
                            const SizedBox(width: 8),
                            const Text(
                              'Hapus',
                              style: TextStyle(color: Colors.redAccent),
                            ),
                          ],
                        ),
                      ),
                    ],
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
