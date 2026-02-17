<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KaryawanExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $paketKaryawan;
    protected $jabatan;
    protected $harianShift;
    protected $area;

    public function __construct($data, $paketKaryawan, $jabatan, $harianShift, $area)
    {
        $this->data = $data;
        $this->paketKaryawan = $paketKaryawan;
        $this->jabatan = $jabatan;
        $this->harianShift = $harianShift;
        $this->area = $area;
    }

    public function array(): array
    {
        $rows = [];
        $no = 1;

        foreach ($this->data as $item) {
            $kid = $item->karyawan_id;

            $rows[] = [
                $no++,
                $item->osis_id,
                $item->ktp,
                $item->nama_tk,
                $item->perusahaan->perusahaan ?? '-',
                $item->status_aktif ?? '-',
                $item->tipe_pekerjaan ?? '-',
                $this->jabatan[$kid]->jabatan ?? '-',
                $this->harianShift[$kid]->harianshift ?? '-',
                $this->paketKaryawan[$kid]->nama_paket ?? '-',
                $this->area[$kid]->area ?? '-',
                $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                $item->tanggal_lahir ?? '-',
                $item->alamat ?? '-',
                $item->asal ?? '-',
                $item->agama ?? '-',
                $item->tanggal_bekerja ?? '-',
                $item->tanggal_pensiun ?? '-',
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'OSIS ID',
            'KTP',
            'Nama',
            'Perusahaan',
            'Status',
            'Tipe Pekerjaan',
            'Jabatan',
            'Shift',
            'Paket',
            'Area',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Alamat',
            'Asal',
            'Agama',
            'Tanggal Bekerja',
            'Tanggal Pensiun',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
