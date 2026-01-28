<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ump extends Model
{
    use HasFactory;
    protected $table = 'md_ump';
    public $timestamps = true;

    protected $primaryKey = "id"; //cek

    protected $fillable = [
        'id',
        'tahun',
        'kode_lokasi',
        'ump',
        'is_deleted',
        'deleted_by',
        'deleted_at'
    ];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'kode_lokasi');
    }
}
