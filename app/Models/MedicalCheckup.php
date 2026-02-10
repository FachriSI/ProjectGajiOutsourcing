<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalCheckup extends Model
{
    use HasFactory;

    protected $table = 'md_medical_checkup';
    protected $primaryKey = 'id';
    
    // Allow mass assignment
    protected $fillable = [
        'karyawan_id',
        'biaya',
        'is_deleted', 
        'deleted_by', 
        'deleted_at'
    ];
}
