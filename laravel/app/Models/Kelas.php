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

    public static function formatTingkatRomawi(int|string|null $tingkat): string
    {
        return match ((int) $tingkat) {
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            default => (string) ($tingkat ?? ''),
        };
    }

    public static function parseTingkat(int|string|null $tingkat): int
    {
        $val = strtoupper(trim((string) $tingkat));

        return match ($val) {
            'X' => 10,
            'XI' => 11,
            'XII' => 12,
            default => (int) $val,
        };
    }

    public function getTingkatRomawiAttribute(): string
    {
        return self::formatTingkatRomawi($this->tingkat);
    }

    public function setTingkatAttribute($value): void
    {
        $this->attributes['tingkat'] = self::parseTingkat($value);
    }

    public function getNamaKelasAttribute(): string
    {
        $tingkat = self::formatTingkatRomawi($this->tingkat);

        return "{$tingkat}-{$this->rombel}";
    }

    public function __toString(): string
    {
        return $this->nama_kelas;
    }

    public static function findOrCreateByNama(string $namaJurusan, int|string $tingkat, int $rombel, string $tahun): self
    {
        $jurusan = Jurusan::firstOrCreate(['nama_jurusan' => $namaJurusan]);
        $tingkatInt = self::parseTingkat($tingkat);

        return self::firstOrCreate([
            'id_jurusan' => $jurusan->id_jurusan,
            'tingkat' => $tingkatInt,
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
