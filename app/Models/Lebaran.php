<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lebaran extends Model
{
    use HasFactory;

    protected $table = 'md_lebaran';
    
    protected $fillable = [
        'tahun',
        'tahun_hijriyah',
        'tanggal',
        'keterangan',
        'is_deleted',
        'deleted_by',
        'deleted_at'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_deleted' => 'boolean'
    ];
}
