<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "perusahaan"; //cek
    protected $primaryKey = "perusahaan_id"; //cek

    protected $fillable = [
        'perusahaan_id',
        'id_pt',
        'perusahaan',
        'cp',
        'cp_jab',
        'cp_telp',
        'cp_email',
        'id_mesin',
        'deleted_data',
        'tkp',
        'npp',
        'alamat'
    ];

}
