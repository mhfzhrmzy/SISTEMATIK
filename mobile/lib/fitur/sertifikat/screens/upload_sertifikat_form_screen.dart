import 'package:flutter/material.dart';
import 'package:pretest_posttest_app/fitur/sertifikat/models/jenis_file_sertifikat.dart';
import 'package:pretest_posttest_app/inti/konstanta/warna_aplikasi.dart';

class UploadSertifikatForm extends StatelessWidget {
  final GlobalKey<FormState> kunciForm;
  final TextEditingController kontrolerJudul;
  final TextEditingController kontrolerPenerbit;
  final DateTime? tanggalTerpilih;
  final String? namaFileTerpilih;
  final bool sedangUnggah;
  final VoidCallback onPilihTanggal;
  final VoidCallback onHapusTanggal;
  final VoidCallback onPilihFile;
  final VoidCallback onHapusFile;
  final VoidCallback onSimpan;

  const UploadSertifikatForm({
    super.key,
    required this.kunciForm,
    required this.kontrolerJudul,
    required this.kontrolerPenerbit,
    required this.tanggalTerpilih,
    required this.namaFileTerpilih,
    required this.sedangUnggah,
    required this.onPilihTanggal,
    required this.onHapusTanggal,
    required this.onPilihFile,
    required this.onHapusFile,
    required this.onSimpan,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFD4A0B0), width: 1.2),
      ),
      child: Form(
        key: kunciForm,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _inputTeks('Judul Sertifikat *', kontrolerJudul, 'Masukkan judul sertifikat'),
            const SizedBox(height: 16),
            _inputTeks('Penerbit *', kontrolerPenerbit, 'Contoh: Coursera, Dicoding, dll.'),
            const SizedBox(height: 16),
            _inputTanggal(),
            const SizedBox(height: 20),
            _areaUpload(),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              height: 46,
              child: ElevatedButton.icon(
                onPressed: sedangUnggah ? null : onSimpan,
                style: ElevatedButton.styleFrom(
                  backgroundColor: WarnaAplikasi.utama,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                icon: sedangUnggah
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                      )
                    : const Icon(Icons.add_circle_outline_rounded, size: 18),
                label: Text(
                  sedangUnggah ? 'Mengunggah...' : 'Tambah Sertifikat',
                  style: const TextStyle(fontWeight: FontWeight.bold),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _inputTeks(String label, TextEditingController controller, String hint) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label,
            style: const TextStyle(color: WarnaAplikasi.utama, fontSize: 13, fontWeight: FontWeight.w600)),
        const SizedBox(height: 6),
        TextFormField(
          controller: controller,
          validator: (v) => (v == null || v.trim().isEmpty) ? '$label wajib diisi' : null,
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: const TextStyle(color: Colors.grey, fontSize: 13),
            filled: true,
            fillColor: const Color(0xFFF6EEF1),
            contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 13),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: Color(0xFFD4A0B0)),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: Color(0xFFD4A0B0)),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: WarnaAplikasi.utama, width: 1.8),
            ),
          ),
        ),
      ],
    );
  }

  Widget _inputTanggal() {
    final aktif = tanggalTerpilih != null;
    const bulan = [
      'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    final teksTanggal = aktif
        ? '${tanggalTerpilih!.day.toString().padLeft(2, '0')} ${bulan[tanggalTerpilih!.month - 1]} ${tanggalTerpilih!.year}'
        : 'Pilih tanggal, bulan, dan tahun';

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Diterbitkan Pada *',
            style: TextStyle(color: WarnaAplikasi.utama, fontSize: 13, fontWeight: FontWeight.w600)),
        const SizedBox(height: 6),
        InkWell(
          onTap: onPilihTanggal,
          borderRadius: BorderRadius.circular(12),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 13),
            decoration: BoxDecoration(
              color: const Color(0xFFF6EEF1),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(
                color: aktif ? WarnaAplikasi.utama : const Color(0xFFD4A0B0),
                width: aktif ? 1.8 : 1.2,
              ),
            ),
            child: Row(
              children: [
                const Icon(Icons.calendar_month_rounded, color: WarnaAplikasi.utama, size: 18),
                const SizedBox(width: 10),
                Expanded(
                  child: Text(
                    teksTanggal,
                    style: TextStyle(
                      fontSize: 13,
                      color: aktif ? WarnaAplikasi.utama : Colors.grey,
                      fontWeight: aktif ? FontWeight.w600 : FontWeight.normal,
                    ),
                  ),
                ),
                if (aktif)
                  GestureDetector(
                    onTap: onHapusTanggal,
                    child: const Icon(Icons.close, size: 16, color: WarnaAplikasi.utama),
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
        const Text('Upload File Sertifikat (PDF/Gambar)',
            style: TextStyle(color: WarnaAplikasi.utama, fontSize: 13, fontWeight: FontWeight.w600)),
        const SizedBox(height: 8),
        if (namaFileTerpilih == null)
          InkWell(
            onTap: onPilihFile,
            borderRadius: BorderRadius.circular(14),
            child: Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 28),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: WarnaAplikasi.utama, width: 1.5),
              ),
              child: const Column(
                children: [
                  Icon(Icons.cloud_upload_rounded, color: WarnaAplikasi.utama, size: 36),
                  SizedBox(height: 8),
                  Text('Upload File Disini',
                      style: TextStyle(color: WarnaAplikasi.utama, fontWeight: FontWeight.bold)),
                  SizedBox(height: 4),
                  Text('Format: PDF, PNG, JPG — maksimal 2 MB',
                      style: TextStyle(color: Colors.grey, fontSize: 11.5)),
                ],
              ),
            ),
          )
        else
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
            decoration: BoxDecoration(
              color: const Color(0xFFF6EEF1),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: WarnaAplikasi.utama, width: 1.2),
            ),
            child: Row(
              children: [
                Icon(
                  jenisDariNama(namaFileTerpilih).adalahGambar
                      ? Icons.image_rounded
                      : Icons.picture_as_pdf_rounded,
                  color: WarnaAplikasi.utama,
                  size: 26,
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        namaFileTerpilih!,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          color: WarnaAplikasi.utama,
                          fontSize: 12.5,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const Text('File siap diunggah', style: TextStyle(color: Colors.grey, fontSize: 11)),
                    ],
                  ),
                ),
                InkWell(
                  onTap: onHapusFile,
                  borderRadius: BorderRadius.circular(8),
                  child: const Padding(
                    padding: EdgeInsets.all(6),
                    child: Icon(Icons.delete_outline_rounded,
                        color: WarnaAplikasi.utama, size: 20),
                  ),
                ),
              ],
            ),
          ),
      ],
    );
  }
}
