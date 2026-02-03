<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatKaryawan extends Model
{
    protected $table = 'riwayat_karyawan';
    protected $primaryKey = 'riwayat_id';

    protected $fillable = [
        'karyawan_id',
        'osis_id',
        'ktp',
        'nama_tk',
        'perusahaan_id',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status',
        'alamat',
        'asal',
        'tanggal_bekerja',
        'tahun_pensiun',
        'tanggal_pensiun',
        'catatan_berhenti',
        'tanggal_berhenti',
        'diganti_dengan_nama',
        'diganti_oleh'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_bekerja' => 'date',
        'tahun_pensiun' => 'date',
        'tanggal_pensiun' => 'date',
        'tanggal_berhenti' => 'date',
    ];

    // Relasi ke karyawan aktif
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    // Relasi ke user yang melakukan penggantian
    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'diganti_oleh');
    }
}
