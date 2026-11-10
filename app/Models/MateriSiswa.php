<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriSiswa extends Model
{
    protected $table = 'materi_siswa';

    protected $fillable = [
        'nisn',
        'id_materi',
        'is_dibuka',
        'is_selesai',
        'selesai_at',
    ];

    protected function casts(): array
    {
        return [
            'is_dibuka' => 'boolean',
            'is_selesai' => 'boolean',
            'selesai_at' => 'datetime',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(PenggunaSiswa::class, 'nisn', 'nisn');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'id_materi', 'id_materi');
    }
}
