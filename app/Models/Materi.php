<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';

    protected $primaryKey = 'id_materi';

    protected $fillable = ['nip', 'kelas', 'tahun', 'jurusan', 'judul_materi', 'isi_materi', 'upload_file'];

    public function adminGuru()
    {
        return $this->belongsTo(AdminGuru::class, 'nip', 'nip');
    }

    public function quiz()
    {
        return $this->hasMany(Quiz::class, 'id_materi', 'id_materi');
    }

    public function materiSiswa()
    {
        return $this->hasMany(MateriSiswa::class, 'id_materi', 'id_materi');
    }
}
