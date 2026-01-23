<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanCetak extends Model
{
    use HasFactory;

    protected $table = 'tagihan_cetak';
    protected $primaryKey = 'cetak_id';

    protected $fillable = [
        'paket_id',
        'token',
        'total_boq',
        'jumlah_pengawas',
        'jumlah_pelaksana',
        'vendor',
        'tanggal_cetak'
    ];

    protected $casts = [
        'total_boq' => 'decimal:2',
        'tanggal_cetak' => 'datetime',
    ];

    /**
     * Generate unique token for verification
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Relation to Paket
     */
    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id', 'paket_id');
    }
}
