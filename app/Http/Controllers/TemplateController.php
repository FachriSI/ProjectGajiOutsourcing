<?php

namespace App\Http\Controllers;

use App\Exports\TemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function downloadMutasi()
    {
        // Headers based on MutasiImport logic (Indexed)
        // Index 0: osis_id, 1: paket, 2: tanggal_mutasi, 3: kode_jabatan, 4: tanggal_promosi
        $headings = [
            'OSIS ID',
            'Paket Pekerjaan (Nama Paket)',
            'Tanggal Mutasi (YYYY-MM-DD)',
            'Kode Jabatan',
            'Tanggal Promosi (YYYY-MM-DD)'
        ];

        // Example data to guide the user
        $data = [
            ['12345', 'Paket Kebersihan', '2023-01-01', 'JAB001', '2023-01-01'],
            ['67890', '', '', 'JAB002', '2023-02-01']
        ];

        return Excel::download(new TemplateExport($headings, $data), 'templateMutasiPromosi_import.xlsx');
    }

    public function downloadPakaian()
    {
        // Headers based on PakaianImport logic (WithHeadingRow)
        $headings = [
            'osis_id',
            'nama_karyawan', // Optional, purely for reference
            'ukuran_baju',
            'ukuran_celana'
        ];

        $data = [
            ['12345', 'Budi Santoso', 'L', '32'],
            ['67890', 'Siti Aminah', 'M', '30']
        ];

        return Excel::download(new TemplateExport($headings, $data), 'templatePakaian_import.xlsx');
    }

    public function downloadPerusahaan()
    {
        // Headers based on PerusahaanImport logic (Indexed 0-10)
        // No, Nama, Alamat, CP, CPJAB, CPTelp, CPEmail, idMesin, Deleted, TKP, NPP
        $headings = [
            'No',
            'Nama Perusahaan',
            'Alamat',
            'Contact Person (CP)',
            'Jabatan CP',
            'No Telp CP',
            'Email CP',
            'ID Mesin (Fingerprint)',
            'Deleted (0/1)',
            'TKP',
            'NPP'
        ];

        $data = [
            ['1', 'PT. Contoh Sejahtera', 'Jl. Sudirman No. 1', 'Budi', 'Manager', '08123456789', 'budi@contoh.com', '101', '0', 'TKP-A', 'NPP-123']
        ];

        return Excel::download(new TemplateExport($headings, $data), 'templatePerusahaan_import.xlsx');
    }

    public function downloadKaryawan()
    {
        // Headers based on KaryawanImport logic (Replacement / Pengganti)
        // Row 0: OSIS ID Lama
        // Row 1: OSIS ID Baru
        // Row 2: KTP
        // Row 3: Nama
        // Row 4: Tanggal Lahir (YYYY-MM-DD)
        // Row 5: Jenis Kelamin (L/P)
        // Row 6: Agama
        // Row 7: Status (TK/K0/K1/etc)
        // Row 8: Alamat
        // Row 9: Asal
        // Row 10: Tanggal Bekerja (YYYY-MM-DD)
        // Row 11: Kode Jabatan

        $headings = [
            'OSIS ID Lama (Yang Diganti)',
            'OSIS ID Baru (Pengganti)',
            'No KTP',
            'Nama Lengkap',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Jenis Kelamin (L/P)',
            'Agama',
            'Status Pernikahan',
            'Alamat Domisili',
            'Asal (Kota/Kab)',
            'Tanggal Mulai Bekerja (YYYY-MM-DD)',
            'Kode Jabatan'
        ];

        $data = [
            ['1001', '2001', '1371000000000001', 'Budi Santoso', '1990-01-01', 'L', 'Islam', 'K0', 'Jl. Sudirman No. 1', 'Padang', date('Y-m-d'), 'JAB001']
        ];

        return Excel::download(new TemplateExport($headings, $data), 'template_import_karyawan.xlsx');
    }

    public function downloadPaket()
    {
        // Headers based on Paket table structure
        // Columns: Nama Paket, Kuota (Orang), Unit Kerja
        $headings = [
            'Nama Paket',
            'Kuota (Orang)',
            'Unit Kerja (Nama Unit)'
        ];

        $data = [
            ['Paket 1212', '10', 'Unit Konsumsi'],
            ['Paket 1313', '15', 'Unit Keamanan'],
        ];

        return Excel::download(new TemplateExport($headings, $data), 'template_import_paket.xlsx');
    }

    public function downloadLokasi()
    {
        // Headers based on Lokasi table structure
        $headings = [
            'Nama Lokasi',
            'Jenis (Provinsi/Kabupaten/Kota)'
        ];

        $data = [
            ['Padang', 'Kota'],
            ['Bukittinggi', 'Kota'],
            ['Pesisir Selatan', 'Kabupaten'],
        ];

        return Excel::download(new TemplateExport($headings, $data), 'template_import_lokasi.xlsx');
    }

    public function downloadDepartemen()
    {
        $headings = [
            'Nama Departemen',
            'Is SI (1=Ya, 0=Tidak)'
        ];

        $data = [
            ['IT Department', 1],
            ['HR Department', 0],
            ['Finance Department', 0],
        ];

        return Excel::download(new TemplateExport($headings, $data), 'template_import_departemen.xlsx');
    }

    public function downloadFungsi()
    {
        $headings = [
            'Nama Fungsi',
            'Keterangan'
        ];

        $data = [
            ['Admin', 'Administrasi kantor'],
            ['Security', 'Keamanan gedung'],
            ['Cleaning Service', 'Kebersihan gedung'],
        ];

        return Excel::download(new TemplateExport($headings, $data), 'template_import_fungsi.xlsx');
    }

    public function downloadUnitKerja()
    {
        $headings = [
            'ID Unit',
            'Nama Unit Kerja'
        ];

        $data = [
            ['UK001', 'Unit Keamanan'],
            ['UK002', 'Unit Kebersihan'],
            ['UK003', 'Unit Operasional'],
        ];

        return Excel::download(new TemplateExport($headings, $data), 'template_import_unitkerja.xlsx');
    }
}
