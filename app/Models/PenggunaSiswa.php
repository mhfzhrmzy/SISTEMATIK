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
        'nisn', 'nama_lengkap', 'kelas', 'tahun', 'jurusan', 'password', 'poin', 'foto_profile',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'nisn' => 'string',
            'password' => 'hashed',
        ];
    }

    public function leaderboard()
    {
        return $this->hasMany(Leaderboard::class, 'nisn', 'nisn');
    }

    public function materiSiswa()
    {
        return $this->hasMany(MateriSiswa::class, 'nisn', 'nisn');
    }

    /**
     * Poin terhitung otomatis dari akumulasi kuis (pre-test & post-test) yang sudah diselesaikan siswa di leaderboard.
     * Jika siswa belum mengerjakan kuis, poin bernilai 0.
     */
    public function getPoinAttribute($value): int
    {
        if ($this->relationLoaded('leaderboard')) {
            return (int) $this->leaderboard->sum('total_poin');
        }

        return (int) $this->leaderboard()->sum('total_poin');
    }
}
