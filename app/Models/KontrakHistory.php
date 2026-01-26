<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontrakHistory extends Model
{
    use HasFactory;

    protected $table = 'kontrak_history';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nilai_kontrak_id',
        'paket_id',
        'change_type',
        'old_value',
        'new_value',
        'change_description',
        'old_total',
        'new_total',
        'delta',
        'changed_at',
        'changed_by'
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'old_total' => 'decimal:2',
        'new_total' => 'decimal:2',
        'delta' => 'decimal:2',
        'changed_at' => 'datetime',
    ];

    /**
     * Relationship to NilaiKontrak
     */
    public function nilaiKontrak()
    {
        return $this->belongsTo(NilaiKontrak::class, 'nilai_kontrak_id', 'id');
    }

    /**
     * Relationship to Paket
     */
    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id', 'paket_id');
    }

    /**
     * Relationship to User (who made the change)
     */
    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by', 'id');
    }

    /**
     * Scope untuk filter by change type
     */
    public function scopeChangeType($query, $type)
    {
        return $query->where('change_type', $type);
    }

    /**
     * Scope untuk filter by paket
     */
    public function scopeByPaket($query, $paketId)
    {
        return $query->where('paket_id', $paketId);
    }

    /**
     * Get history for a specific paket
     */
    public static function getHistoryForPaket($paketId, $limit = 10)
    {
        return self::where('paket_id', $paketId)
            ->orderBy('changed_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Create history entry
     */
    public static function createEntry($paketId, $changeType, $oldValue, $newValue, $description = null, $userId = null)
    {
        $oldTotal = is_array($oldValue) && isset($oldValue['total_nilai_kontrak']) 
            ? $oldValue['total_nilai_kontrak'] 
            : null;
        
        $newTotal = is_array($newValue) && isset($newValue['total_nilai_kontrak']) 
            ? $newValue['total_nilai_kontrak'] 
            : null;

        $delta = ($oldTotal && $newTotal) ? ($newTotal - $oldTotal) : null;

        return self::create([
            'paket_id' => $paketId,
            'change_type' => $changeType,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'change_description' => $description,
            'old_total' => $oldTotal,
            'new_total' => $newTotal,
            'delta' => $delta,
            'changed_at' => now(),
            'changed_by' => $userId
        ]);
    }
}
