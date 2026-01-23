<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>BOQ {{ $boq['paket']->paket }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
        }

        .container {
            padding: 15px 20px;
        }

        /* Header */
        .header-title {
            background-color: #8B0000;
            color: white;
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 10px;
        }

        /* Info Section */
        .info-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .info-label {
            color: #8B0000;
            font-weight: bold;
            width: 180px;
        }

        /* Main BOQ Table */
        table.boq-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        table.boq-table th,
        table.boq-table td {
            border: 1px solid #333;
            padding: 3px 5px;
            text-align: left;
            font-size: 9px;
        }

        table.boq-table th {
            background-color: #D8BFD8;
            font-weight: bold;
            text-align: center;
        }

        table.boq-table .section-header {
            background-color: #FAFAD2;
            font-weight: bold;
        }

        table.boq-table .sub-item {
            padding-left: 15px;
        }

        table.boq-table .sub-item-2 {
            padding-left: 25px;
        }

        table.boq-table .total-row {
            background-color: #FFE4E1;
            font-weight: bold;
        }

        table.boq-table .grand-total {
            background-color: #D8BFD8;
            font-weight: bold;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        /* Notes */
        .notes {
            font-size: 8px;
            margin: 8px 0;
        }

        .notes p {
            margin-bottom: 2px;
        }

        /* Second Table - Pekerjaan Tambah */
        .section-title {
            font-weight: bold;
            margin: 15px 0 8px 0;
            font-size: 9px;
        }

        table.tambah-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        table.tambah-table th,
        table.tambah-table td {
            border: 1px solid #333;
            padding: 3px 5px;
            font-size: 9px;
        }

        table.tambah-table th {
            background-color: #D8BFD8;
            font-weight: bold;
            text-align: center;
        }

        table.tambah-table .total-row {
            background-color: #E6E6FA;
            font-weight: bold;
        }

        /* Footer & Signature */
        .footer {
            margin-top: 30px;
        }

        .signature-section {
            width: 100%;
            margin-top: 20px;
        }

        .signature-left {
            float: left;
            width: 50%;
        }

        .signature-right {
            float: right;
            width: 45%;
            text-align: center;
        }

        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            width: 150px;
            display: inline-block;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .qr-section {
            margin-top: 10px;
        }

        .qr-section svg {
            width: 80px;
            height: 80px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Title -->
        <div class="header-title">
            BOQ Jasa Pekerjaan Penunjang Operasional di PT Semen Padang ({{ $boq['tahun'] }})
        </div>

        <!-- Info Section -->
        <table class="info-table">
            <tr>
                <td class="info-label">Vendor</td>
                <td>: {{ $boq['vendor'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="info-label">Jumlah Pekerja Alih Daya</td>
                <td>: {{ $boq['jumlah_pekerja'] }}</td>
            </tr>
            <tr>
                <td class="info-label">Jangka Waktu Kontrak</td>
                <td>: 36 bulan</td>
            </tr>
            <tr>
                <td class="info-label">Pekerjaan POS (Paket ..........)</td>
                <td>: {{ $boq['paket']->paket }}</td>
            </tr>
        </table>

        <!-- Main BOQ Table -->
        <table class="boq-table">
            <thead>
                <tr>
                    <th style="width: 55%;">KOMPONEN UPAH</th>
                    <th style="width: 22%;">Pengawas</th>
                    <th style="width: 23%;">Pelaksana</th>
                </tr>
            </thead>
            <tbody>
                <!-- I. UPAH PEKERJA ALIH DAYA -->
                <tr class="section-header">
                    <td><strong>I. UPAH PEKERJA ALIH DAYA</strong></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="sub-item">a. Upah Pokok</td>
                    <td class="text-right">{{ number_format($boq['pengawas']['upah_pokok'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($boq['pelaksana']['upah_pokok'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="sub-item">b. Tunjangan Tetap</td>
                    <td class="text-right">{{ number_format($boq['pengawas']['tj_tetap'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($boq['pelaksana']['tj_tetap'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="sub-item">c. Tunjangan Tidak Tetap</td>
                    <td class="text-right">{{ number_format($boq['pengawas']['tj_tidak_tetap'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($boq['pelaksana']['tj_tidak_tetap'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="sub-item">d. Tunjangan Lokasi (*diluar Padang)</td>
                    <td class="text-right">{{ number_format($boq['pengawas']['tj_lokasi'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($boq['pelaksana']['tj_lokasi'], 0, ',', '.') }}</td>
                </tr>

                <!-- II. UPAH NORMATIF -->
                <tr class="section-header">
                    <td><strong>II. UPAH NORMATIF (Sesuai UU)</strong></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="sub-item">a. BPJS Kesehatan</td>
                    <td class="text-right">{{ number_format($boq['pengawas']['bpjs_kesehatan'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($boq['pelaksana']['bpjs_kesehatan'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="sub-item">b. BPJS Ketenagakerjaan</td>
                    <td class="text-right">{{ number_format($boq['pengawas']['bpjs_ketenagakerjaan'], 0, ',', '.') }}
                    </td>
                    <td class="text-right">{{ number_format($boq['pelaksana']['bpjs_ketenagakerjaan'], 0, ',', '.') }}
                    </td>
                </tr>

                <!-- III. UANG KOMPENSASI -->
                <tr class="section-header">
                    <td><strong>III. UANG KOMPENSASI (Sesuai PP No 35 Th 2021)</strong></td>
                    <td class="text-right">{{ number_format($boq['pengawas']['kompensasi'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($boq['pelaksana']['kompensasi'], 0, ',', '.') }}</td>
                </tr>

                <!-- Nilai Kontrak per orang -->
                <tr class="total-row">
                    <td><strong>Nilai Kontrak 1 orang Pekerja Alih Daya perbulan</strong></td>
                    <td class="text-right">
                        {{ $boq['pengawas']['count'] > 0 ? number_format($boq['pengawas']['nilai_kontrak'] / $boq['pengawas']['count'], 0, ',', '.') : 0 }}
                    </td>
                    <td class="text-right">
                        {{ $boq['pelaksana']['count'] > 0 ? number_format($boq['pelaksana']['nilai_kontrak'] / $boq['pelaksana']['count'], 0, ',', '.') : 0 }}
                    </td>
                </tr>

                <!-- IV. PEKERJAAN TAMBAH LEMBUR -->
                <tr class="section-header">
                    <td><strong>IV. PEKERJAAN TAMBAH LEMBUR (Sesuai UU)</strong></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="sub-item">a. Tarif Lembur perjam</td>
                    <td class="text-right">
                        {{ $boq['pengawas']['count'] > 0 ? number_format(($boq['pengawas']['lembur'] / $boq['pengawas']['count']) / 2, 0, ',', '.') : 0 }}
                    </td>
                    <td class="text-right">
                        {{ $boq['pelaksana']['count'] > 0 ? number_format(($boq['pelaksana']['lembur'] / $boq['pelaksana']['count']) / 2, 0, ',', '.') : 0 }}
                    </td>
                </tr>
                <tr>
                    <td class="sub-item">b. Alokasi Jam Lembur ..... jam real, ..... jam</td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td class="sub-item-2">perkalian</td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td class="sub-item-2">(Pembayaran sesuai realisasi)</td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                </tr>

                <!-- Nilai Lembur per orang -->
                <tr class="total-row">
                    <td><strong>Nilai Lembur 1 orang Pekerja Alih Daya perbulan</strong></td>
                    <td class="text-right">
                        {{ $boq['pengawas']['count'] > 0 ? number_format($boq['pengawas']['lembur'] / $boq['pengawas']['count'], 0, ',', '.') : 0 }}
                    </td>
                    <td class="text-right">
                        {{ $boq['pelaksana']['count'] > 0 ? number_format($boq['pelaksana']['lembur'] / $boq['pelaksana']['count'], 0, ',', '.') : 0 }}
                    </td>
                </tr>

                <!-- Summary Section -->
                <tr class="total-row">
                    <td><strong>Jumlah Pekerja Alih Daya (sesuai Kontrak)</strong></td>
                    <td class="text-center">{{ $boq['pengawas']['count'] }}</td>
                    <td class="text-center">{{ $boq['pelaksana']['count'] }}</td>
                </tr>
                <tr>
                    <td class="sub-item"><strong>Nilai Kontrak Pekerja Alih Daya 1 bulan</strong></td>
                    <td colspan="2" class="text-right">
                        {{ number_format($boq['pengawas']['nilai_kontrak'] + $boq['pelaksana']['nilai_kontrak'], 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Nilai Fee Kontrak Pekerja Alih Daya 1 bulan (..........)</strong></td>
                    <td colspan="2" class="text-right">
                        {{ number_format(($boq['pengawas']['nilai_kontrak'] + $boq['pelaksana']['nilai_kontrak']) * 0.1, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="total-row">
                    <td class="sub-item"><strong>Total Nilai Kontrak Pekerja Alih Daya 1 bulan</strong></td>
                    <td colspan="2" class="text-right">
                        {{ number_format(($boq['pengawas']['nilai_kontrak'] + $boq['pelaksana']['nilai_kontrak']) * 1.1, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="sub-item"><strong>Nilai Lembur 1 bulan</strong></td>
                    <td colspan="2" class="text-right">
                        {{ number_format($boq['pengawas']['lembur'] + $boq['pelaksana']['lembur'], 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="sub-item"><strong>Nilai Fee Lembur 1 bulan (.........)</strong></td>
                    <td colspan="2" class="text-right">
                        {{ number_format(($boq['pengawas']['lembur'] + $boq['pelaksana']['lembur']) * 0.025, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Nilai Lembur Pekerja Alih Daya 1 bulan</strong></td>
                    <td colspan="2" class="text-right">
                        {{ number_format(($boq['pengawas']['lembur'] + $boq['pelaksana']['lembur']) * 1.025, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="total-row">
                    <td class="sub-item"><strong>Total Nilai 1 bulan</strong></td>
                    <td colspan="2" class="text-right">{{ number_format($boq['total_bulanan'], 0, ',', '.') }}</td>
                </tr>
                <tr class="grand-total">
                    <td class="sub-item"><strong>Total Nilai 36 bulan</strong></td>
                    <td colspan="2" class="text-right">{{ number_format($boq['total_bulanan'] * 36, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Notes -->
        <div class="notes">
            <p>*) Nilai tersebut akan disesuaikan apabila ada kenaikan UMP/UMK/UMR</p>
            <p>*) Nilai tersebut belum termasuk ppn</p>
            <p>*) Nilai Fee sudah termasuk Pajak Penghasilan (PPH)</p>
        </div>

        <!-- Second Table - Pekerjaan Tambah -->
        <p class="section-title">Apabila terdapat pekerjaan tambah sesuai ketentuan PPK dan Perjanjian, maka:</p>

        <table class="tambah-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 35%;">Komponen</th>
                    <th style="width: 25%;">Nilai Rupiah</th>
                    <th style="width: 35%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>Biaya Perjalanan Dinas</td>
                    <td></td>
                    <td rowspan="3" style="vertical-align: top; font-size: 8px;">• Nilai sesuai realisasi biaya yang
                        dikeluarkan, yang disetujui unit kerja dan SDM</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>Biaya Tugas Belajar</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td>dll</td>
                    <td></td>
                </tr>
                <tr class="total-row">
                    <td colspan="2" class="text-center"><strong>Total</strong></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center"><strong>Fee Pekerjaan Tambah</strong></td>
                    <td></td>
                    <td style="font-size: 8px;">• Nilai ini sudah termasuk pajak penghasilan (PPH)</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2" class="text-center"><strong>Total Pekerjaan Tambah</strong></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div class="notes">
            <p>*) Nilai tersebut belum termasuk ppn</p>
        </div>

        <!-- Footer with QR Code and Signature -->
        <div class="footer clearfix">
            <div class="signature-left">
                <div class="qr-section">
                    {!! $qrCode !!}
                    <p style="font-size: 7px; margin-top: 3px;">Scan untuk verifikasi</p>
                    <p style="font-size: 6px; word-break: break-all; max-width: 200px;">Token:
                        {{ substr($token, 0, 20) }}...
                    </p>
                </div>
            </div>
            <div class="signature-right">
                <p>Padang, ........................................ 202</p>
                <br>
                <p>PT ........................................................</p>
                <div class="signature-line"></div>
                <p style="margin-top: 5px;">Direktur</p>
            </div>
        </div>
    </div>
</body>

</html>