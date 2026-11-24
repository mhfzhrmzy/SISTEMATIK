<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Guru extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'guru';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nip', 'nik', 'nama_lengkap', 'password', 'foto_profile', 'dibuat_oleh',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function materi()
    {
        return $this->hasMany(Materi::class, 'guru_id', 'id');
    }

    public function dibuatOleh()
    {
        return $this->belongsTo(SuperAdmin::class, 'dibuat_oleh', 'nip');
    }

    /**
     * Get identifier for authentication (NIP or NIK)
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }
}
