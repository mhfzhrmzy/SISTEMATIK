<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AdminGuru extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table = 'admin_guru';

    protected $primaryKey = 'nip';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nip', 'nama_lengkap', 'password', 'foto_profile',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'nip' => 'string',
            'password' => 'hashed',
        ];
    }

    public function materi()
    {
        return $this->hasMany(Materi::class, 'nip', 'nip');
    }

    public function quiz()
    {
        return $this->hasMany(Quiz::class, 'nip', 'nip');
    }
}
