<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractValidation extends Model
{
    use HasFactory;

    protected $table = 'contract_validations';

    protected $fillable = [
        'nilai_kontrak_id',
        'validation_token',
        'document_hash',
        'pdf_path',
        'is_valid',
        'generated_at',
        'generated_by',
        'validated_at',
        'validation_count',
        'last_validated_ip',
        'expires_at',
        'metadata'
    ];

    protected $casts = [
        'is_valid' => 'boolean',
        'generated_at' => 'datetime',
        'validated_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
        'validation_count' => 'integer'
    ];

    /**
     * Relationship to NilaiKontrak
     */
    public function nilaiKontrak()
    {
        return $this->belongsTo(NilaiKontrak::class, 'nilai_kontrak_id', 'id');
    }

    /**
     * Relationship to User (who generated)
     */
    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by', 'id');
    }

    /**
     * Check if validation is expired
     */
    public function isExpired()
    {
        if (!$this->expires_at) {
            return false;
        }
        return now()->greaterThan($this->expires_at);
    }

    /**
     * Increment validation count
     */
    public function incrementValidationCount($ipAddress = null)
    {
        $this->increment('validation_count');
        $this->update([
            'validated_at' => now(),
            'last_validated_ip' => $ipAddress
        ]);
    }

    /**
     * Generate unique token
     */
    public static function generateToken()
    {
        do {
            $token = \Illuminate\Support\Str::uuid()->toString();
        } while (self::where('validation_token', $token)->exists());

        return $token;
    }
}
