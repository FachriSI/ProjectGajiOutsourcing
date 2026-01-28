<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fungsi extends Model
{
    use HasFactory;
    protected $table = 'md_fungsi';
    public $timestamps = true;

    protected $primaryKey = "kode_fungsi"; //cek

    protected $fillable = [
        'kode_fungsi',
        'fungsi',
        'keterangan',
        'is_deleted',
        'deleted_by',
        'deleted_at'
    ];
}
