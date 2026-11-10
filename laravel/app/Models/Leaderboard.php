<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    protected $table = 'leaderboard';

    protected $primaryKey = 'id_leaderboard';

    protected $fillable = ['id_quiz', 'nisn', 'total_poin', 'peringkat'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'id_quiz', 'id_quiz');
    }

    public function pengguna()
    {
        return $this->belongsTo(PenggunaSiswa::class, 'nisn', 'nisn');
    }
}
