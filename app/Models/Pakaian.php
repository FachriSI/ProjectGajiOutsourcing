<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pakaian extends Model
{
    use HasFactory;
    protected $table = 'md_pakaian';
    public $timestamps = true;

    protected $primaryKey = "pakaian_id"; //cek

    protected $fillable = [
        'karyawan_id',
        'nilai_jatah',
        'ukuran_baju',
        'ukuran_celana',
        'beg_date',
        'is_deleted',
        'deleted_by',
        'deleted_at'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'karyawan_id');
    }

}
