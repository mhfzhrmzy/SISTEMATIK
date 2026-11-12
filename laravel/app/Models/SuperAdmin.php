<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SuperAdmin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'super_admin';

    protected $primaryKey = 'nip';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nip', 'nama_lengkap', 'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'nip' => 'string',
            'password' => 'hashed',
        ];
    }

    public function guruDibuat()
    {
        return $this->hasMany(AdminGuru::class, 'dibuat_oleh', 'nip');
    }

    public function siswaDibuat()
    {
        return $this->hasMany(PenggunaSiswa::class, 'dibuat_oleh', 'nip');
    }

    public function materiDikelola()
    {
        return $this->hasMany(Materi::class, 'nip_superadmin', 'nip');
    }

    public function soalDikelola()
    {
        return $this->hasMany(Soal::class, 'nip_superadmin', 'nip');
    }
}
