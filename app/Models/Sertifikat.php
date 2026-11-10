<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    protected $table = 'sertifikat';

    protected $primaryKey = 'id_sertifikat';

    protected $fillable = [
        'nisn', 'nip', 'id_materi',
        'judul_sertifikat', 'kategori', 'penerbit', 'deskripsi',
        'jam_pelatihan', 'event_kompetisi',
        'file_sertifikat', 'file_hash', 'badge_url',
        'tipe_sertifikat', 'terverifikasi', 'tanggal_terbit',
    ];

    protected $casts = [
        'terverifikasi' => 'boolean',
        'tanggal_terbit' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(PenggunaSiswa::class, 'nisn', 'nisn');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'id_materi', 'id_materi');
    }

    public function guru()
    {
        return $this->belongsTo(AdminGuru::class, 'nip', 'nip');
    }
}
