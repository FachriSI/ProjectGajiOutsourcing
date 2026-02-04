<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuotajam extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "kuota_jam"; //cek
    protected $primaryKey = "kuota_id"; //cek

    protected $fillable = [
        'kuota_id',
        'karyawan_id',
        'kuota',
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
