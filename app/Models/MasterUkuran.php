<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterUkuran extends Model
{
    use HasFactory;

    protected $table = 'master_ukuran';
    protected $fillable = ['nama_ukuran'];
}
