<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';

    protected $primaryKey = 'id_materi';

    protected $fillable = ['nip', 'nip_superadmin', 'id_kelas', 'judul_materi', 'isi_materi', 'upload_file'];

    public function adminGuru()
    {
        return $this->belongsTo(AdminGuru::class, 'nip', 'nip');
    }

    public function superAdmin()
    {
        return $this->belongsTo(SuperAdmin::class, 'nip_superadmin', 'nip');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function quiz()
    {
        return $this->hasMany(Quiz::class, 'id_materi', 'id_materi');
    }

    public function materiSiswa()
    {
        return $this->hasMany(MateriSiswa::class, 'id_materi', 'id_materi');
    }

    public function getJurusanAttribute(): ?string
    {
        if ($this->relationLoaded('kelas')) {
            return $this->kelas?->jurusan?->nama_jurusan;
        }

        return $this->kelas()->with('jurusan')->first()?->jurusan?->nama_jurusan;
    }

    public function getTahunAttribute(): ?string
    {
        if ($this->relationLoaded('kelas')) {
            return $this->kelas?->tahun;
        }

        return $this->kelas?->tahun;
    }
}
