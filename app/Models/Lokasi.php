<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;
    protected $table = 'md_lokasi';
    public $timestamps = true;

    protected $primaryKey = "kode_lokasi"; //cek

    protected $fillable = [
        'kode_lokasi',
        'lokasi',
        'jenis',
        'is_deleted',
        'deleted_by',
        'deleted_at'
    ];
    
    public function ump()
    {
        return $this->hasOne(Ump::class, 'kode_lokasi'); // Sesuai ERD
    }
}
