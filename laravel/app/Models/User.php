<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Helper cek role
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function materis()
    {
        return $this->hasMany(Materi::class, 'created_by');
    }

    public function soals()
    {
        return $this->hasMany(Soal::class, 'created_by');
    }
}
