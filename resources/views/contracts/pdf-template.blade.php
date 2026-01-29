<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOQ Kontrak - {{ $contract_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.2;
            color: #000;
            padding: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table td, table th {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }
        
        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
            padding: 6px;
        }
        
        .info-row td {
            font-size: 8.5pt;
        }
        
        .info-label {
            font-weight: bold;
            width: 180px;
        }
        
        .table-header {
            background-color: #E8D5F2;
            font-weight: bold;
            text-align: center;
            font-size: 9pt;
        }
        
        .section-header {
            font-weight: bold;
            background-color: #F5F5F5;
            font-size: 8.5pt;
        }
        
        .subsection {
            padding-left: 15px;
            font-size: 8.5pt;
        }
        
        .total-row {
            background-color: #D4EDDA;
            font-weight: bold;
        }
        
        .grand-total {
            background-color: #A8D5BA;
            font-weight: bold;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .notes {
            font-size: 7.5pt;
            margin: 8px 0;
            line-height: 1.3;
        }
        
        .additional-section {
            margin-top: 15px;
            font-size: 8.5pt;
        }
        
        .qr-section {
            text-align: center;
            margin-top: 25px;
            padding: 12px;
            border: 2px dashed #666;
            background: #f9f9f9;
            page-break-inside: avoid;
        }
        
        .qr-section h4 {
            margin-bottom: 8px;
            font-size: 10pt;
        }
        
        .validation-info {
            font-size: 7pt;
            color: #666;
            margin-top: 8px;
        }
        
        .footer-text {
            margin-top: 20px;
            font-size: 8.5pt;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <table>
        <tr>
            <td class="header-title" colspan="3">
                BOQ Jasa Pekerjaan Penunjang Operasional di PT Semen Padang ({{ $nilai_kontrak->tahun }})
            </td>
        </tr>
        <tr class="info-row">
            <td class="info-label">Vendor</td>
            <td colspan="2">{{ $paket->unitKerja->unit_kerja ?? '-' }}</td>
        </tr>
        <tr class="info-row">
            <td class="info-label">Jumlah Pekerja Alih Daya</td>
            <td colspan="2">{{ $nilai_kontrak->jumlah_karyawan_total }} orang</td>
        </tr>
        <tr class="info-row">
            <td class="info-label">Jangka Waktu Kontrak</td>
            <td colspan="2">{{ $periode_formatted }}</td>
        </tr>
        <tr class="info-row">
            <td class="info-label">Pekerjaan POS (Paket)</td>
            <td colspan="2">{{ $paket->paket }}</td>
        </tr>

        <!-- TABLE HEADER -->
        <tr>
            <th class="table-header" style="width: 50%;">KOMPONEN UPAH</th>
            <th class="table-header" style="width: 25%;">Pengawas</th>
            <th class="table-header" style="width: 25%;">Pelaksana</th>
        </tr>

        <!-- I. UPAH PEKERJA ALIH DAYA -->
        <tr class="section-header">
            <td colspan="3"><strong>I. UPAH PEKERJA ALIH DAYA</strong></td>
        </tr>
        <tr>
            <td class="subsection">a. Upah Pokok</td>
            <td class="text-right">{{ number_format($breakdown_pengawas['upah_pokok'] ?? 0, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($breakdown_pelaksana['upah_pokok'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="subsection">b. Tunjangan Tetap</td>
            <td class="text-right">{{ number_format($breakdown_pengawas['tj_tetap'] ?? 0, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($breakdown_pelaksana['tj_tetap'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="subsection">c. Tunjangan Tidak Tetap</td>
            <td class="text-right">{{ number_format($breakdown_pengawas['tj_tidak_tetap'] ?? 0, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($breakdown_pelaksana['tj_tidak_tetap'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="subsection">d. Tunjangan Lokasi (diluar Padang)</td>
            <td class="text-right">{{ number_format($breakdown_pengawas['tj_lokasi'] ?? 0, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($breakdown_pelaksana['tj_lokasi'] ?? 0, 0, ',', '.') }}</td>
        </tr>

        <!-- II. UPAH NORMATIF -->
        <tr class="section-header">
            <td colspan="3"><strong>II. UPAH NORMATIF (Sesuai UU)</strong></td>
        </tr>
        <tr>
            <td class="subsection">a. BPJS Kesehatan</td>
            <td class="text-right">{{ number_format($breakdown_pengawas['bpjs_kesehatan'] ?? 0, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($breakdown_pelaksana['bpjs_kesehatan'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="subsection">b. BPJS Ketenagakerjaan</td>
            <td class="text-right">{{ number_format($breakdown_pengawas['bpjs_ketenagakerjaan'] ?? 0, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($breakdown_pelaksana['bpjs_ketenagakerjaan'] ?? 0, 0, ',', '.') }}</td>
        </tr>

        <!-- III. UANG KOMPENSASI -->
        <tr class="section-header">
            <td colspan="3"><strong>III. UANG KOMPENSASI (Sesuai PP No 35)</strong></td>
        </tr>
        <tr>
            <td></td>
            <td class="text-right">{{ number_format($breakdown_pengawas['kompensasi'] ?? 0, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($breakdown_pelaksana['kompensasi'] ?? 0, 0, ',', '.') }}</td>
        </tr>

        <!-- NILAI KONTRAK 1 ORANG -->
        <tr>
            <td><strong>Nilai Kontrak 1 orang Pekerja Alih Daya perbulan</strong></td>
            <td class="text-right">
                @php
                    $nilaiPerPengawas = $breakdown_pengawas['count'] > 0 
                        ? $breakdown_pengawas['total'] / $breakdown_pengawas['count'] 
                        : 0;
                @endphp
                {{ number_format($nilaiPerPengawas, 0, ',', '.') }}
            </td>
            <td class="text-right">
                @php
                    $nilaiPerPelaksana = $breakdown_pelaksana['count'] > 0 
                        ? $breakdown_pelaksana['total'] / $breakdown_pelaksana['count'] 
                        : 0;
                @endphp
                {{ number_format($nilaiPerPelaksana, 0, ',', '.') }}
            </td>
        </tr>

        <!-- IV. PEKERJAAN TAMBAH LEMBUR -->
        <tr class="section-header">
            <td colspan="3"><strong>IV. PEKERJAAN TAMBAH LEMBUR (Sesuai UU)</strong></td>
        </tr>
        <tr>
            <td class="subsection">a. Tarif Lembur perjam</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
        </tr>
        <tr>
            <td class="subsection">b. Alokasi Jam Lembur ..... jam real, ..... jam perkalian</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
        </tr>

        <!-- NILAI LEMBUR 1 ORANG -->
        <tr>
            <td><strong>Nilai Lembur 1 orang Pekerja Alih Daya perbulan</strong></td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
        </tr>

        <!-- JUMLAH PEKERJA SESUAI KONTRAK -->
        <tr>
            <td><strong>Jumlah Pekerja Alih Daya (sesuai Kontrak)</strong></td>
            <td class="text-right">{{ $breakdown_pengawas['count'] ?? 0 }}</td>
            <td class="text-right">{{ $breakdown_pelaksana['count'] ?? 0 }}</td>
        </tr>

        <!-- NILAI FEE KONTRAK -->
        <tr>
            <td><strong>Nilai Fee Kontrak Pekerja Alih Daya 1 bulan (..........)</strong></td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
        </tr>

        <!-- TOTAL NILAI KONTRAK 1 BULAN -->
        <tr class="total-row">
            <td><strong>Total Nilai Kontrak Pekerja Alih Daya 1 bulan</strong></td>
            <td class="text-right">{{ number_format($breakdown_pengawas['total'] ?? 0, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($breakdown_pelaksana['total'] ?? 0, 0, ',', '.') }}</td>
        </tr>

        <!-- NILAI LEMBUR 1 BULAN -->
        <tr>
            <td><strong>Nilai Lembur 1 bulan</strong></td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
        </tr>

        <!-- NILAI FEE LEMBUR 1 BULAN -->
        <tr class="total-row">
            <td><strong>Nilai Fee Lembur 1 bulan (.......)</strong></td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
        </tr>

        <!-- TOTAL NILAI LEMBUR -->
        <tr class="total-row">
            <td><strong>Total Nilai Lembur Pekerja Alih Daya 1 bulan</strong></td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
        </tr>

        <!-- TOTAL NILAI 1 BULAN -->
        <tr class="grand-total">
            <td><strong>Total Nilai 1 bulan</strong></td>
            <td class="text-right">{{ number_format($breakdown_pengawas['total'] ?? 0, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($breakdown_pelaksana['total'] ?? 0, 0, ',', '.') }}</td>
        </tr>

        <!-- TOTAL NILAI 36 BULAN -->
        <tr class="grand-total">
            <td><strong>Total Nilai 36 bulan</strong></td>
            <td class="text-right">{{ number_format(($breakdown_pengawas['total'] ?? 0) * 36, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format(($breakdown_pelaksana['total'] ?? 0) * 36, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- CATATAN -->
    <div class="notes">
        <p>*) Nilai tersebut akan disesuaikan apabila ada kenaikan UMP/UMK/UMR</p>
        <p>*) Nilai real belum termasuk ppn</p>
        <p>*) Nilai Fee sudah termasuk Pajak Penghasilan (PPH)</p>
    </div>

    <!-- TABEL PEKERJAAN TAMBAH -->
    <div class="additional-section">
        <table>
            <tr>
                <td colspan="4" style="font-weight: bold; background-color: #F5F5F5;">
                    Apabila terdapat pekerjaan tambah sesuai ketentuan PPK dan Perjanjian, maka:
                </td>
            </tr>
            <tr class="table-header">
                <th style="width: 50px;">No.</th>
                <th style="width: 200px;">Komponen</th>
                <th style="width: 150px;">Nilai Rupiah</th>
                <th>Keterangan</th>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>Biaya Perjalanan Dinas</td>
                <td></td>
                <td>* Nilai sesuai realisasi biaya yang dikeluarkan, yang disetujui unit kerja</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Biaya Tugas Belajar</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>dll</td>
                <td></td>
                <td></td>
            </tr>
            <tr class="total-row">
                <td colspan="2" class="text-center"><strong>Total</strong></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2"><strong>Fee Pekerjaan Tambah</strong></td>
                <td></td>
                <td>* Nilai ini sudah termasuk pajak penghasilan (PPH)</td>
            </tr>
            <tr class="total-row">
                <td colspan="2"><strong>Total Pekerjaan Tambah</strong></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>

    <div class="notes">
        <p>*) Nilai tersebut belum termasuk ppn</p>
    </div>

    <!-- QR CODE SECTION -->
    <div class="qr-section">
        <h4>VALIDASI DOKUMEN KONTRAK</h4>
        <p style="margin-bottom: 8px; font-size: 8pt;">Scan QR Code untuk memvalidasi keaslian dokumen ini</p>
        
        <div class="qr-code">
            {!! $qr_code_svg !!}
        </div>
        
        <div class="validation-info">
            <p><strong>Nomor Kontrak:</strong> {{ $contract_number }}</p>
            <p><strong>URL Validasi:</strong> {{ $validation_url }}</p>
            <p><strong>Token:</strong> {{ substr($validation_token, 0, 20) }}...</p>
            <p style="margin-top: 6px; font-size: 6.5pt;">
                Dokumen ini dibuat secara otomatis pada {{ $generated_at->format('d F Y H:i') }} WIB
                oleh {{ $generated_by_name }}
            </p>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer-text">
        <p>Padang, {{ $generated_at->format('d F Y') }}</p>
        <p>PT. ....................................................................</p>
    </div>


</body>
</html>
