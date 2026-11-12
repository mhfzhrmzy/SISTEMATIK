<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PenggunaSiswa extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'pengguna_siswa';

    protected $primaryKey = 'nisn';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nisn', 'nama_lengkap', 'id_kelas', 'password', 'foto_profile', 'dibuat_oleh',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'nisn' => 'string',
            'password' => 'hashed',
        ];
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function dibuatOleh()
    {
        return $this->belongsTo(SuperAdmin::class, 'dibuat_oleh', 'nip');
    }

    public function leaderboard()
    {
        return $this->hasMany(Leaderboard::class, 'nisn', 'nisn');
    }

    public function materiSiswa()
    {
        return $this->hasMany(MateriSiswa::class, 'nisn', 'nisn');
    }

    public function sertifikat()
    {
        return $this->hasMany(Sertifikat::class, 'nisn', 'nisn');
    }

    public function getNamaKelasAttribute(): ?string
    {
        if ($this->relationLoaded('kelas')) {
            return $this->kelas?->nama_kelas;
        }

        return $this->kelas?->nama_kelas;
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

    /**
     * Poin terhitung otomatis dari akumulasi kuis (pre-test & post-test) yang sudah diselesaikan siswa di leaderboard.
     * Jika siswa belum mengerjakan kuis, poin bernilai 0.
     */
    public function getPoinAttribute(): int
    {
        if ($this->relationLoaded('leaderboard')) {
            return (int) $this->leaderboard->sum('total_poin');
        }

        return (int) $this->leaderboard()->sum('total_poin');
    }
}
