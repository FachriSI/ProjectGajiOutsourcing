<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resiko extends Model
{
    use HasFactory;
    protected $table = 'md_resiko';
    public $timestamps = true;

    protected $primaryKey = "kode_resiko"; //cek

    protected $fillable = [
        'kode_resiko',
        'resiko',
        'tunjangan_resiko',
        'is_deleted',
        'deleted_by',
        'deleted_at'
    ];
}
