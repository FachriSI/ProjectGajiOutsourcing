<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Masakerja extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "masa_kerja"; //cek
    protected $primaryKey = "id"; //cek

    protected $fillable = [
        'id',
        'karyawan_id',
        'tunjangan_masakerja',
        'beg_date',
        'created_at',
        'updated_at',
        'deleted_at',
        'deleted_by',
        'is_deleted'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
