<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriSertifikat extends Model
{
    protected $table = 'kategori_sertifikat';

    protected $primaryKey = 'id_kategori';

    public $timestamps = false;

    protected $fillable = ['nama_kategori'];

    public function sertifikat()
    {
        return $this->hasMany(Sertifikat::class, 'id_kategori', 'id_kategori');
    }
}
