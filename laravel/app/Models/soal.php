<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $table = 'soal';

    protected $primaryKey = 'id_soal';

    protected $fillable = [
        'id_quiz',
        'nip_superadmin',
        'pertanyaan',
        'jawaban_benar',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'id_quiz', 'id_quiz');
    }

    public function pilihanJawaban()
    {
        return $this->hasMany(PilihanJawaban::class, 'id_soal', 'id_soal');
    }

    public function superAdmin()
    {
        return $this->belongsTo(SuperAdmin::class, 'nip_superadmin', 'nip');
    }

    public function getPilihanAAttribute(): ?string
    {
        return $this->getPilihanByLabel('a');
    }

    public function getPilihanBAttribute(): ?string
    {
        return $this->getPilihanByLabel('b');
    }

    public function getPilihanCAttribute(): ?string
    {
        return $this->getPilihanByLabel('c');
    }

    public function getPilihanDAttribute(): ?string
    {
        return $this->getPilihanByLabel('d');
    }

    protected function getPilihanByLabel(string $label): ?string
    {
        if ($this->relationLoaded('pilihanJawaban')) {
            return $this->pilihanJawaban->firstWhere('label', $label)?->teks_pilihan;
        }

        return $this->pilihanJawaban()->where('label', $label)->value('teks_pilihan');
    }

    public function syncPilihan(array $pilihan): void
    {
        foreach (['a', 'b', 'c', 'd'] as $label) {
            $teks = $pilihan[$label] ?? $pilihan['pilihan_'.$label] ?? null;
            if ($teks !== null) {
                PilihanJawaban::updateOrCreate(
                    ['id_soal' => $this->id_soal, 'label' => $label],
                    ['teks_pilihan' => $teks]
                );
            }
        }
    }
}
