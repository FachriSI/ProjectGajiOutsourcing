<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;
    protected $table = 'md_jabatan';
    public $timestamps = true;

    protected $primaryKey = "kode_jabatan"; //cek

    protected $fillable = [
        'kode_jabatan',
        'jabatan',
        'tunjangan_jabatan',
        'is_deleted',
        'deleted_by',
        'deleted_at'
    ];
}
