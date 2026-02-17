<?php

namespace App\Exports;

use App\Services\GajiCalculatorService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaketDetailExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $paketName;

    public function __construct($data, $paketName)
    {
        $this->data = $data;
        $this->paketName = $paketName;
    }

    public function array(): array
    {
        $rows = [];
        $no = 1;

        foreach ($this->data as $item) {
            $calc = GajiCalculatorService::calculate([
                'ump_sumbar' => $item->ump_sumbar ?? 0,
                'ump_lokasi' => $item->lokasi['ump']['ump'] ?? 0,
                'kode_lokasi' => $item->kode_lokasi ?? 12,
                'tunjangan_jabatan' => $item->tunjangan_jabatan ?? 0,
                'tunjangan_masakerja' => $item->tunjangan_masakerja ?? 0,
                'tunjangan_penyesuaian' => $item->tunjangan_penyesuaian ?? 0,
                'tunjangan_shift' => $item->tunjangan_shift ?? 0,
                'kode_resiko' => $item->kode_resiko ?? 2,
                'tunjangan_resiko' => $item->resiko['tunjangan_resiko'] ?? 0,
                'perusahaan_id' => $item->perusahaan_id ?? 0,
                'kuota_jam' => $item->kuota ?? 0,
                'nilai_jatah' => $item->nilai_jatah ?? 0,
                'mcu' => $item->mcu ?? 0,
            ]);

            $rows[] = [
                $no++,
                $item->osis_id,
                $item->nama_tk,
                $item->jabatan->nama_jabatan ?? '-',
                $item->perusahaan ?? '-',
                $item->aktif_mulai ?? '-',
                $item->tipe_pekerjaan ?? '-',
                round($calc['upah_pokok']),
                round($calc['t_tetap']),
                round($calc['t_tdk_tetap']),
                round($calc['tj_lokasi']),
                round($calc['bpjs_kesehatan']),
                round($calc['bpjs_ketenagakerjaan']),
                round($calc['kompensasi']),
                round($calc['jumlah_fix_cost']),
                round($calc['tarif_lembur']),
                round($calc['nilai_lembur']),
                round($calc['total_kontrak']),
                round($calc['mcu']),
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'OSIS ID',
            'Nama',
            'Jabatan',
            'Vendor',
            'Aktif Mulai',
            'Tipe Pekerjaan',
            'Upah Pokok',
            'Tj. Tetap',
            'Tj. Tidak Tetap',
            'Tj. Lokasi',
            'BPJS Kesehatan',
            'BPJS Ketenagakerjaan',
            'Kompensasi',
            'Nilai Kontrak/Bln',
            'Tarif Lembur/Jam',
            'Nilai Lembur/Bln',
            'Total Kontrak/Bln',
            'MCU',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
