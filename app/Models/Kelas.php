<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $primaryKey = 'id_kelas';

    protected $fillable = ['id_jurusan', 'tingkat', 'rombel', 'tahun'];

    protected function casts(): array
    {
        return [
            'tingkat' => 'integer',
            'rombel' => 'integer',
        ];
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }

    public function getNamaKelasAttribute(): string
    {
        return "{$this->tingkat} {$this->rombel}";
    }

    public function __toString(): string
    {
        return "{$this->tingkat} {$this->rombel}";
    }

    public static function findOrCreateByNama(string $namaJurusan, int $tingkat, int $rombel, string $tahun): self
    {
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => $namaJurusan]);

        return self::firstOrCreate([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => $tingkat,
            'rombel' => $rombel,
            'tahun' => $tahun,
        ]);
    }

    public function siswa()
    {
        return $this->hasMany(PenggunaSiswa::class, 'id_kelas', 'id_kelas');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class, 'id_kelas', 'id_kelas');
    }
}
