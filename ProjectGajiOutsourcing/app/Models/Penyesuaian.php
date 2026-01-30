<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyesuaian extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = "md_penyesuaian"; //cek
    protected $primaryKey = "kode_suai"; //cek

    protected $fillable = [
        'kode_suai',
        'keterangan',
        'tunjangan_penyesuaian',
        'is_deleted',
        'deleted_by',
        'deleted_at'
    ];
}
