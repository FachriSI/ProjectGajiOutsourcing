<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harianshift extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = "md_harianshift"; //cek
    protected $primaryKey = "kode_harianshift"; //cek

    protected $fillable = [
        'kode_harianshift',
        'harianshift',
        'tunjangan_shift',
        'is_deleted',
        'deleted_by',
        'deleted_at'
    ];
}
