<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = "md_perusahaan"; //cek
    protected $primaryKey = "perusahaan_id"; //cek

    protected $fillable = [
        'perusahaan_id',
        'id_pt',
        'perusahaan',
        'alamat',
        'cp',
        'cp_jab',
        'cp_telp',
        'cp_email',
        'id_mesin',
        'tkp',
        'npp',
        'is_deleted',
        'deleted_by',
        'deleted_at'
    ];

}
