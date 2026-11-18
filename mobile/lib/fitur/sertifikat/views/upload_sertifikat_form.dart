import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/tema_sertifikat.dart';

/// Kontrak yang dipenuhi [UploadSertifikatView] agar form dapat memantik
/// interaksi tanpa tahu detail state-nya.
abstract class UploadSertifikatFormController {
  GlobalKey<FormState>? get kunciForm;
  TextEditingController? get kontrolerJudul;
  TextEditingController? get kontrolerPenerbit;
  DateTime? tanggalTerpilih;
  String? namaFileTerpilih;
  bool sedangUnggah = false;
  bool get sedangEdit;

  Future<void> pilihTanggal();
  Future<void> pilihFile();
  void hapusTanggal();
  void hapusFile();
  void batalEdit();
  Future<void> simpan();
}

/// Kartu form upload/edit sertifikat. Bergantung pada
/// [UploadSertifikatFormController], bukan pada state view.
class UploadSertifikatForm extends StatelessWidget {
  final UploadSertifikatFormController c;

  const UploadSertifikatForm({super.key, required this.c});

  @override
  Widget build(BuildContext context) {
    final kunciForm = c.kunciForm;
    final kontrolerJudul = c.kontrolerJudul;
    final kontrolerPenerbit = c.kontrolerPenerbit;
    if (kunciForm == null || kontrolerJudul == null || kontrolerPenerbit == null) {
      return const SizedBox.shrink();
    }
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: TemaSertifikat.kotak(
        warnaGaris: TemaSertifikat.maroonBorder,
        radius: 16,
        bayangan: [
          BoxShadow(
            color: TemaSertifikat.maroon.withValues(alpha: 0.07),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Form(
        key: kunciForm,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _kolomTeks(
              label: 'Judul Sertifikat',
              wajib: true,
              kontroler: kontrolerJudul,
              petunjuk: 'Masukkan judul sertifikat',
            ),
            const SizedBox(height: 16),
            _kolomTeks(
              label: 'Penerbit',
              wajib: true,
              kontroler: kontrolerPenerbit,
              petunjuk: 'Contoh: Coursera, Dicoding, dll.',
            ),
            const SizedBox(height: 16),
            _kolomTanggal(),
            const SizedBox(height: 20),
            _areaUpload(),
            const SizedBox(height: 24),
            _barisTombol(),
          ],
        ),
      ),
    );
  }

  Widget _kolomTeks({
    required String label,
    required TextEditingController kontroler,
    required String petunjuk,
    bool wajib = false,
  }) {
    String? validasiKosong(String? nilai) =>
        (nilai == null || nilai.trim().isEmpty) ? '$label wajib diisi' : null;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _label(label, wajib: wajib),
        const SizedBox(height: 6),
        TextFormField(
          controller: kontroler,
          validator: validasiKosong,
          style: const TextStyle(
            color: TemaSertifikat.maroon,
            fontSize: 13.5,
            fontWeight: FontWeight.w500,
          ),
          cursorColor: TemaSertifikat.maroon,
          decoration: TemaSertifikat.dekorasiInput(petunjuk),
        ),
      ],
    );
  }

  Widget _label(String teks, {bool wajib = false}) {
    return Row(
      children: [
        Text(
          teks,
          style: const TextStyle(
            color: TemaSertifikat.maroon,
            fontSize: 13,
            fontWeight: FontWeight.w600,
          ),
        ),
        if (wajib)
          const Text(
            ' *',
            style: TextStyle(
              color: Colors.red,
              fontSize: 13,
              fontWeight: FontWeight.bold,
            ),
          ),
      ],
    );
  }

  Widget _kolomTanggal() {
    final aktif = c.tanggalTerpilih != null;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _label('Diterbitkan Pada', wajib: true),
        const SizedBox(height: 6),
        InkWell(
          onTap: c.pilihTanggal,
          borderRadius: BorderRadius.circular(12),
          child: Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 13),
            decoration: TemaSertifikat.kotak(
              warnaLatar: TemaSertifikat.maroonTerang,
              warnaGaris: aktif ? TemaSertifikat.maroon : TemaSertifikat.maroonBorder,
              ketebalanGaris: aktif ? 1.8 : 1.2,
            ),
            child: Row(
              children: [
                const Icon(Icons.calendar_month_rounded,
                    color: TemaSertifikat.maroon, size: 18),
                const SizedBox(width: 10),
                Expanded(
                  child: Text(
                    aktif ? _formatTanggal(c.tanggalTerpilih!) : 'Pilih tanggal, bulan, dan tahun',
                    style: TextStyle(
                      fontSize: 13,
                      fontWeight:
                          aktif ? FontWeight.w600 : FontWeight.normal,
                      color: aktif ? TemaSertifikat.maroon : Colors.grey.shade500,
                    ),
                  ),
                ),
                if (aktif)
                  InkWell(
                    onTap: c.hapusTanggal,
                    borderRadius: BorderRadius.circular(10),
                    child:
                        const Icon(Icons.close, size: 16, color: TemaSertifikat.maroon),
                  ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _areaUpload() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _label('Upload File Sertifikat (PDF)'),
        const SizedBox(height: 8),
        c.namaFileTerpilih == null ? _dropzoneKosong() : _dropzonePenuh(),
      ],
    );
  }

  Widget _dropzoneKosong() {
    return GestureDetector(
      onTap: c.pilihFile,
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(vertical: 30),
        decoration: TemaSertifikat.kotak(
          warnaGaris: TemaSertifikat.maroon,
          ketebalanGaris: 1.5,
          radius: 14,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 64,
              height: 64,
              decoration: const BoxDecoration(
                color: TemaSertifikat.maroonTerang,
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.cloud_upload_rounded,
                  color: TemaSertifikat.maroon, size: 34),
            ),
            const SizedBox(height: 12),
            const Text(
              'Upload File Disini',
              style: TextStyle(
                color: TemaSertifikat.maroon,
                fontSize: 14,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 4),
              Text('Format: PDF, maksimal 10 MB',
                style:
                    TextStyle(color: Colors.grey.shade500, fontSize: 11.5)),
          ],
        ),
      ),
    );
  }

  Widget _dropzonePenuh() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: TemaSertifikat.kotak(
        warnaLatar: TemaSertifikat.maroonTerang,
        warnaGaris: TemaSertifikat.maroon,
        radius: 12,
      ),
      child: Row(
        children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: TemaSertifikat.maroon,
              borderRadius: BorderRadius.circular(8),
            ),
            child: const Icon(Icons.picture_as_pdf_rounded,
                color: Colors.white, size: 22),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  c.namaFileTerpilih!,
                  style: const TextStyle(
                    color: TemaSertifikat.maroon,
                    fontSize: 12.5,
                    fontWeight: FontWeight.bold,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
                const Text('File siap diunggah',
                    style: TextStyle(color: Colors.grey, fontSize: 11)),
              ],
            ),
          ),
          IconButton(
            onPressed: c.hapusFile,
            icon:
                const Icon(Icons.delete_outline_rounded, color: TemaSertifikat.maroon, size: 20),
            tooltip: 'Hapus file',
          ),
        ],
      ),
    );
  }

  Widget _barisTombol() {
    return Row(
      children: [
        if (c.sedangEdit) ...[
          Expanded(
            child: OutlinedButton(
              onPressed: c.batalEdit,
              style: TemaSertifikat.tombolGaris(),
              child:
                  const Text('Batal Edit', style: TextStyle(fontWeight: FontWeight.bold)),
            ),
          ),
          const SizedBox(width: 12),
        ],
        Expanded(
          flex: c.sedangEdit ? 2 : 1,
          child: ElevatedButton.icon(
            onPressed: c.sedangUnggah ? null : c.simpan,
            icon: c.sedangUnggah
                ? const SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(strokeWidth: 2))
                : Icon(
                    c.sedangEdit
                        ? Icons.save_rounded
                        : Icons.add_circle_outline_rounded,
                    size: 18),
            label: Text(
              c.sedangUnggah
                  ? 'Mengunggah...'
                  : c.sedangEdit
                      ? 'Perbarui Sertifikat'
                      : 'Tambah Sertifikat',
              style: const TextStyle(fontWeight: FontWeight.bold),
            ),
            style: TemaSertifikat.tombolUtama(),
          ),
        ),
      ],
    );
  }

  String _formatTanggal(DateTime d) {
    const bulan = [
      'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];
    return '${d.day.toString().padLeft(2, '0')} ${bulan[d.month - 1]} ${d.year}';
  }
}
