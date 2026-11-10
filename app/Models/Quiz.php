<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quiz';

    protected $primaryKey = 'id_quiz';

    protected $fillable = ['nip', 'id_materi', 'tipe_test', 'poin', 'timer', 'tanggal', 'jam_mulai', 'is_shuffle'];

    protected $casts = [
        'tanggal' => 'date',
        'is_shuffle' => 'boolean',
    ];

    /**
     * Grace period (detik) setelah waktu_selesai di mana submit masih diterima.
     */
    public const GRACE_PERIOD_DETIK = 10;

    /**
     * Waktu mulai posttest (tanggal + jam_mulai).
     */
    public function waktuMulai(): ?Carbon
    {
        if (! $this->tanggal || ! $this->jam_mulai) {
            return null;
        }

        return Carbon::parse($this->tanggal->format('Y-m-d').' '.$this->jam_mulai);
    }

    /**
     * Waktu selesai posttest (waktu_mulai + timer menit).
     */
    public function waktuSelesai(): ?Carbon
    {
        $mulai = $this->waktuMulai();
        if (! $mulai) {
            return null;
        }

        return $mulai->copy()->addMinutes($this->timer);
    }

    /**
     * Apakah jadwal posttest lengkap (tanggal + jam_mulai tersedia).
     */
    public function jadwalLengkap(): bool
    {
        return $this->tanggal !== null && $this->jam_mulai !== null;
    }

    /**
     * Status jadwal posttest relatif terhadap now():
     * 'terjadwal', 'berlangsung', 'selesai', atau null jika jadwal tidak lengkap.
     */
    public function statusJadwal(): ?string
    {
        if (! $this->jadwalLengkap()) {
            return null;
        }

        $now = now();
        $mulai = $this->waktuMulai();
        $selesai = $this->waktuSelesai();

        if ($now->lt($mulai)) {
            return 'terjadwal';
        }

        if ($now->lte($selesai)) {
            return 'berlangsung';
        }

        return 'selesai';
    }

    public function adminGuru()
    {
        return $this->belongsTo(AdminGuru::class, 'nip', 'nip');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'id_materi', 'id_materi');
    }

    public function soal()
    {
        return $this->hasMany(Soal::class, 'id_quiz', 'id_quiz');
    }

    public function leaderboard()
    {
        return $this->hasMany(Leaderboard::class, 'id_quiz', 'id_quiz');
    }
}
