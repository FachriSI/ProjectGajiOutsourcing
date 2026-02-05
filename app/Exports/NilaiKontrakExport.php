<?php

namespace App\Exports;

use App\Models\NilaiKontrak;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NilaiKontrakExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $selectedColumns;
    protected $columnMap;

    public function __construct($data, $selectedColumns)
    {
        $this->data = $data;
        $this->selectedColumns = $selectedColumns;
        
        // Define available columns and their display names
        $this->columnMap = [
            'paket_nama' => 'Nama Paket',
            'unit_kerja' => 'Unit Kerja',
            'periode' => 'Periode',
            'tahun' => 'Tahun',
            'jumlah_karyawan_total' => 'Total Karyawan',
            'jumlah_karyawan_aktif' => 'Karyawan Aktif',
            'jumlah_pengawas' => 'Jumlah Pengawas',
            'jumlah_pelaksana' => 'Jumlah Pelaksana',
            'total_nilai_kontrak' => 'Total Nilai Kontrak',
            'total_pengawas' => 'Total Biaya Pengawas',
            'total_pelaksana' => 'Total Biaya Pelaksana',
            'ump_sumbar' => 'UMP Sumbar',
            'kuota_paket' => 'Kuota Paket',
            // Breakdown Components
            'upah_pokok' => 'Total Upah Pokok',
            'tj_tetap' => 'Total Tunjangan Tetap',
            'tj_tidak_tetap' => 'Total Tunjangan Tidak Tetap',
            'tj_lokasi' => 'Total Tunjangan Lokasi',
            'bpjs_kesehatan' => 'Total BPJS Kesehatan',
            'bpjs_ketenagakerjaan' => 'Total BPJS Ketenagakerjaan',
            'kompensasi' => 'Total Kompensasi',
            'uang_jasa' => 'Total Uang Jasa',
            'lembur' => 'Total Biaya Lembur',
        ];
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        $headings = [];
        foreach ($this->selectedColumns as $column) {
            if (isset($this->columnMap[$column])) {
                $headings[] = $this->columnMap[$column];
            }
        }
        return $headings;
    }

    public function map($row): array
    {
        $mapped = [];
        
        foreach ($this->selectedColumns as $column) {
            switch ($column) {
                case 'paket_nama':
                    $mapped[] = $row->paket->paket ?? '-';
                    break;
                case 'unit_kerja':
                    $mapped[] = $row->paket->unitKerja->unit_kerja ?? '-';
                    break;
                case 'total_nilai_kontrak':
                case 'total_pengawas':
                case 'total_pelaksana':
                case 'ump_sumbar':
                    $mapped[] = $row->$column; 
                    break;
                case 'upah_pokok':
                case 'tj_tetap':
                case 'tj_tidak_tetap':
                case 'tj_lokasi':
                case 'bpjs_kesehatan':
                case 'bpjs_ketenagakerjaan':
                case 'kompensasi':
                case 'uang_jasa':
                case 'lembur':
                    // Sum from breakdown_json
                    $breakdown = $row->breakdown_json ?? [];
                    $valPengawas = $breakdown['pengawas'][$column] ?? 0;
                    $valPelaksana = $breakdown['pelaksana'][$column] ?? 0;
                    $mapped[] = $valPengawas + $valPelaksana;
                    break;
                default:
                    $mapped[] = $row->$column ?? '';
            }
        }

        return $mapped;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
