<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;
    protected $table = 'md_unit_kerja';
    public $timestamps = true;

    protected $primaryKey = "unit_id"; //cek

    protected $fillable = [
        'unit_id',
        'unit_kerja',
        'departemen_id',
        'fungsi',
        'is_deleted',
        'deleted_by',
        'deleted_at'
    ];

    public function paketUnit()
    {
        return $this->hasMany(Paket::class, 'unit_id', 'unit_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'departemen_id');
    }

}
