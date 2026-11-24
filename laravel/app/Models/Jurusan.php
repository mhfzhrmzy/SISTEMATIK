<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $table = 'jurusan';

    protected $primaryKey = 'id_jurusan';

    public $timestamps = false;

    protected $fillable = ['nama_jurusan'];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_jurusan', 'id_jurusan');
    }

    /**
     * Mengembalikan singkatan baku untuk jurusan (contoh: "Teknik Komputer & Jaringan" -> "TKJ").
     */
    public static function getSingkatan(?string $namaJurusan): string
    {
        if (empty($namaJurusan)) {
            return 'TKJ';
        }

        $namaTrim = trim($namaJurusan);
        $lookup = [
            'Teknik Komputer & Jaringan' => 'TKJ',
            'Teknik Komputer dan Jaringan' => 'TKJ',
            'Desain Komunikasi Visual' => 'DKV',
            'Teknik Kendaraan Ringan' => 'TKR',
            'Teknik Kendaraan Ringan Otomotif' => 'TKRO',
            'Teknik Sepeda Motor' => 'TSM',
            'Teknik dan Bisnis Sepeda Motor' => 'TBSM',
            'Teknik Pemesinan' => 'TPM',
            'Teknik Instalasi Listrik' => 'TITL',
            'Teknik Instalasi Tenaga Listrik' => 'TITL',
            'Teknik Pembangkit Listrik' => 'TPL',
            'Teknik Pembangkit Tenaga Listrik' => 'TPTL',
            'Teknik Mekatronika' => 'TM',
            'Teknik Audio Video' => 'TAV',
            'Teknik Alat Berat' => 'TAB',
            'Teknik Konstruksi & Perumahan' => 'TKP',
            'Teknik Konstruksi dan Perumahan' => 'TKP',
            'Desain Permodelan & Informasi Bangunan' => 'DPIB',
            'Desain Pemodelan & Informasi Bangunan' => 'DPIB',
            'Desain Pemodelan dan Informasi Bangunan' => 'DPIB',
            'Rekayasa Perangkat Lunak' => 'RPL',
        ];

        if (isset($lookup[$namaTrim])) {
            return $lookup[$namaTrim];
        }

        if (preg_match('/^[A-Z0-9]{2,5}$/', strtoupper($namaTrim))) {
            return strtoupper($namaTrim);
        }

        $words = preg_split('/[\s&\-_]+/', $namaTrim);
        $acronym = '';
        foreach ($words as $w) {
            $w = trim($w);
            if ($w === '' || strtolower($w) === 'dan') {
                continue;
            }
            $acronym .= strtoupper($w[0]);
        }

        return ! empty($acronym) ? substr($acronym, 0, 5) : 'TKJ';
    }

    public function getSingkatanAttribute(): string
    {
        return self::getSingkatan($this->nama_jurusan);
    }
}
