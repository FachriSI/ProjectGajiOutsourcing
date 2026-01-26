<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiKontrak extends Model
{
    use HasFactory;

    protected $table = 'nilai_kontrak';
    protected $primaryKey = 'id';

    protected $fillable = [
        'paket_id',
        'periode',
        'tahun',
        'bulan',
        'ump_sumbar',
        'jumlah_karyawan_aktif',
        'jumlah_karyawan_total',
        'kuota_paket',
        'total_nilai_kontrak',
        'total_pengawas',
        'total_pelaksana',
        'jumlah_pengawas',
        'jumlah_pelaksana',
        'breakdown_json',
        'calculated_at',
        'calculated_by'
    ];

    protected $casts = [
        'ump_sumbar' => 'decimal:2',
        'total_nilai_kontrak' => 'decimal:2',
        'total_pengawas' => 'decimal:2',
        'total_pelaksana' => 'decimal:2',
        'breakdown_json' => 'array',
        'calculated_at' => 'datetime',
    ];

    /**
     * Relationship to Paket
     */
    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id', 'paket_id');
    }

    /**
     * Relationship to KontrakHistory
     */
    public function histories()
    {
        return $this->hasMany(KontrakHistory::class, 'nilai_kontrak_id', 'id');
    }

    /**
     * Relationship to User (who calculated)
     */
    public function calculator()
    {
        return $this->belongsTo(User::class, 'calculated_by', 'id');
    }

    /**
     * Scope untuk filter by periode
     */
    public function scopePeriode($query, $periode)
    {
        return $query->where('periode', $periode);
    }

    /**
     * Scope untuk filter by tahun
     */
    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    /**
     * Scope untuk filter by paket
     */
    public function scopeByPaket($query, $paketId)
    {
        return $query->where('paket_id', $paketId);
    }

    /**
     * Get latest calculation for a paket
     */
    public static function getLatestForPaket($paketId)
    {
        return self::where('paket_id', $paketId)
            ->orderBy('periode', 'desc')
            ->first();
    }

    /**
     * Get calculation for specific periode
     */
    public static function getForPeriode($paketId, $periode)
    {
        return self::where('paket_id', $paketId)
            ->where('periode', $periode)
            ->first();
    }
}
