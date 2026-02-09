@extends('layouts.main')

@section('title', 'Lihat Tagihan - ' . $boq['paket']->paket)

@section('content')
    <style>
        @media print {
            /* Set page size and margins */
            @page {
                size: A4 portrait;
                margin: 10mm 8mm;
            }

            /* Hide non-essential elements */
            .btn, button, .d-flex.justify-content-between,
            nav, .navbar, .sidebar, footer {
                display: none !important;
            }

            /* Remove container padding */
            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Compact card styling */
            .card {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
            }

            .card-header {
                padding: 4px 8px !important;
                font-size: 11pt !important;
                page-break-after: avoid;
            }

            .card-body {
                padding: 6px 8px !important;
            }

            /* Reduce font sizes */
            h4 { font-size: 11pt !important; margin: 0 0 4px 0 !important; }
            h5 { font-size: 10pt !important; margin: 0 !important; }
            h6 { font-size: 9pt !important; margin: 4px 0 2px 0 !important; }
            body, p, td, th { font-size: 8pt !important; line-height: 1.2 !important; }
            small, .small { font-size: 7pt !important; }
            .fst-italic { font-size: 7pt !important; }

            /* Compact table styling */
            table {
                font-size: 8pt !important;
                margin: 0 !important;
                width: 100% !important;
            }

            .table-borderless {
                margin-bottom: 6px !important;
            }

            .table-borderless td {
                padding: 1px 4px !important;
            }

            th, td {
                padding: 2px 4px !important;
                line-height: 1.1 !important;
            }

            /* Reduce spacing between sections */
            .mb-3, .mb-4 { margin-bottom: 4px !important; }
            .mt-3, .mt-4 { margin-top: 6px !important; }
            .py-3 { padding-top: 0 !important; padding-bottom: 0 !important; }

            /* Prevent page breaks inside tables */
            table, tr, td, th {
                page-break-inside: avoid;
            }

            /* Compact info section */
            .table.table-borderless {
                width: 100% !important;
                margin-bottom: 4px !important;
            }

            /* Reduce table row heights */
            tbody tr {
                height: auto !important;
            }

            /* Make text more compact */
            .ps-4 { padding-left: 8px !important; }
            .text-end, .text-center { padding: 2px 4px !important; }

            /* Adjust colored backgrounds for print */
            tr[style*="background-color"] {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Remove extra spacing from notes */
            .fst-italic p {
                margin-bottom: 2px !important;
                line-height: 1.1 !important;
            }

            /* Compact second table */
            .table-responsive {
                margin-top: 4px !important;
            }

            /* Ensure content fits on one page */
            .card-body > * {
                margin-bottom: 3px !important;
            }

            /* Optimize strong/bold elements */
            strong { font-weight: 600; }
        }
    </style>

    <div class="container-fluid py-3">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                <i class="fas fa-file-invoice-dollar me-2"></i>
                Tagihan BOQ: {{ $boq['paket']->paket }}
            </h4>
            <div>
                <a href="{{ route('kalkulator.show', ['paket_id' => $boq['paket']->paket_id, 'periode' => \Carbon\Carbon::parse($boq['nilai_kontrak']->periode)->format('Y-m')]) }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('kalkulator.cetak-thr', ['paket_id' => $boq['paket']->paket_id, 'periode' => \Carbon\Carbon::parse($boq['nilai_kontrak']->periode)->format('Y-m')]) }}" class="btn btn-warning me-2" target="_blank">
                    <i class="fas fa-print me-1"></i> Cetak THR
                </a>
                <a href="{{ route('paket.pdf.download', $boq['paket']->paket_id) }}" class="btn btn-success">
                    <i class="fas fa-download me-1"></i> Download PDF
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card shadow">
            <div class="card-header text-white text-center" style="background-color: #8B0000;">
                <h5 class="mb-0">BOQ Jasa Pekerjaan Penunjang Operasional di PT Semen Padang ({{ $boq['tahun'] }})</h5>
            </div>
            <div class="card-body">
                <!-- Info Section -->
                <table class="table table-borderless mb-4" style="width: auto;">
                    <tr>
                        <td class="fw-bold text-danger" style="width: 200px;">Vendor</td>
                        <td>: {{ $boq['vendor'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-danger">Jumlah Pekerja Alih Daya</td>
                        <td>: {{ $boq['jumlah_pekerja'] }} orang</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-danger">Jangka Waktu Kontrak</td>
                        <td>: 36 bulan</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-danger">Pekerjaan POS (Paket)</td>
                        <td>: {{ $boq['paket']->paket }}</td>
                    </tr>
                </table>

                <!-- Main BOQ Table -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr style="background-color: #D8BFD8;">
                                <th style="width: 55%;">KOMPONEN UPAH</th>
                                <th class="text-center" style="width: 22%;">Pengawas</th>
                                <th class="text-center" style="width: 23%;">Pelaksana</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- I. UPAH PEKERJA ALIH DAYA -->
                            <tr style="background-color: #FAFAD2;">
                                <td><strong>I. UPAH PEKERJA ALIH DAYA</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="ps-4">a. Upah Pokok</td>
                                <td class="text-end">{{ number_format($boq['pengawas']['upah_pokok'], 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($boq['pelaksana']['upah_pokok'], 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">b. Tunjangan Tetap</td>
                                <td class="text-end">{{ number_format($boq['pengawas']['tj_tetap'], 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($boq['pelaksana']['tj_tetap'], 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">c. Tunjangan Tidak Tetap</td>
                                <td class="text-end">{{ number_format($boq['pengawas']['tj_tidak_tetap'], 0, ',', '.') }}
                                </td>
                                <td class="text-end">{{ number_format($boq['pelaksana']['tj_tidak_tetap'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4">d. Tunjangan Lokasi (*diluar Padang)</td>
                                <td class="text-end">{{ number_format($boq['pengawas']['tj_lokasi'], 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($boq['pelaksana']['tj_lokasi'], 0, ',', '.') }}</td>
                            </tr>

                            <!-- II. UPAH NORMATIF -->
                            <tr style="background-color: #FAFAD2;">
                                <td><strong>II. UPAH NORMATIF (Sesuai UU)</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="ps-4">a. BPJS Kesehatan</td>
                                <td class="text-end">{{ number_format($boq['pengawas']['bpjs_kesehatan'], 0, ',', '.') }}
                                </td>
                                <td class="text-end">{{ number_format($boq['pelaksana']['bpjs_kesehatan'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4">b. BPJS Ketenagakerjaan</td>
                                <td class="text-end">
                                    {{ number_format($boq['pengawas']['bpjs_ketenagakerjaan'], 0, ',', '.') }}</td>
                                <td class="text-end">
                                    {{ number_format($boq['pelaksana']['bpjs_ketenagakerjaan'], 0, ',', '.') }}</td>
                            </tr>

                            <!-- III. UANG KOMPENSASI -->
                            <tr style="background-color: #FAFAD2;">
                                <td><strong>III. UANG KOMPENSASI (Sesuai PP No 35 Th 2021)</strong></td>
                                <td class="text-end">{{ number_format($boq['pengawas']['kompensasi'], 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($boq['pelaksana']['kompensasi'], 0, ',', '.') }}</td>
                            </tr>

                            <!-- Nilai Kontrak per orang -->
                            <tr style="background-color: #FFE4E1;">
                                <td><strong>Nilai Kontrak 1 orang Pekerja Alih Daya perbulan</strong></td>
                                <td class="text-end">
                                    {{ $boq['pengawas']['count'] > 0 ? number_format($boq['pengawas']['nilai_kontrak'] / $boq['pengawas']['count'], 0, ',', '.') : 0 }}
                                </td>
                                <td class="text-end">
                                    {{ $boq['pelaksana']['count'] > 0 ? number_format($boq['pelaksana']['nilai_kontrak'] / $boq['pelaksana']['count'], 0, ',', '.') : 0 }}
                                </td>
                            </tr>

                            <!-- IV. PEKERJAAN TAMBAH LEMBUR -->
                            <tr style="background-color: #FAFAD2;">
                                <td><strong>IV. PEKERJAAN TAMBAH LEMBUR (Sesuai UU)</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="ps-4">a. Tarif Lembur perjam</td>
                                <td class="text-end">
                                    {{ $boq['pengawas']['count'] > 0 ? number_format(($boq['pengawas']['lembur'] / $boq['pengawas']['count']) / 2, 0, ',', '.') : 0 }}
                                </td>
                                <td class="text-end">
                                    {{ $boq['pelaksana']['count'] > 0 ? number_format(($boq['pelaksana']['lembur'] / $boq['pelaksana']['count']) / 2, 0, ',', '.') : 0 }}
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4">b. Alokasi Jam Lembur (Pembayaran sesuai realisasi)</td>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                            </tr>

                            <!-- Nilai Lembur per orang -->
                            <tr style="background-color: #FFE4E1;">
                                <td><strong>Nilai Lembur 1 orang Pekerja Alih Daya perbulan</strong></td>
                                <td class="text-end">
                                    {{ $boq['pengawas']['count'] > 0 ? number_format($boq['pengawas']['lembur'] / $boq['pengawas']['count'], 0, ',', '.') : 0 }}
                                </td>
                                <td class="text-end">
                                    {{ $boq['pelaksana']['count'] > 0 ? number_format($boq['pelaksana']['lembur'] / $boq['pelaksana']['count'], 0, ',', '.') : 0 }}
                                </td>
                            </tr>

                            <!-- Summary Section -->
                            <tr style="background-color: #FFE4E1;">
                                <td><strong>Jumlah Pekerja Alih Daya (sesuai Kontrak)</strong></td>
                                <td class="text-center">{{ $boq['pengawas']['count'] }}</td>
                                <td class="text-center">{{ $boq['pelaksana']['count'] }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4"><strong>Nilai Kontrak Pekerja Alih Daya 1 bulan</strong></td>
                                <td colspan="2" class="text-end">
                                    {{ number_format($boq['pengawas']['nilai_kontrak'] + $boq['pelaksana']['nilai_kontrak'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Nilai Fee Kontrak Pekerja Alih Daya 1 bulan (10%)</strong></td>
                                <td colspan="2" class="text-end">
                                    {{ number_format(($boq['pengawas']['nilai_kontrak'] + $boq['pelaksana']['nilai_kontrak']) * 0.1, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr style="background-color: #FFE4E1;">
                                <td class="ps-4"><strong>Total Nilai Kontrak Pekerja Alih Daya 1 bulan</strong></td>
                                <td colspan="2" class="text-end">
                                    {{ number_format(($boq['pengawas']['nilai_kontrak'] + $boq['pelaksana']['nilai_kontrak']) * 1.1, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4"><strong>Nilai Lembur 1 bulan</strong></td>
                                <td colspan="2" class="text-end">
                                    {{ number_format($boq['pengawas']['lembur'] + $boq['pelaksana']['lembur'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4"><strong>Nilai Fee Lembur 1 bulan (2.5%)</strong></td>
                                <td colspan="2" class="text-end">
                                    {{ number_format(($boq['pengawas']['lembur'] + $boq['pelaksana']['lembur']) * 0.025, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr style="background-color: #FFE4E1;">
                                <td><strong>Total Nilai Lembur Pekerja Alih Daya 1 bulan</strong></td>
                                <td colspan="2" class="text-end">
                                    {{ number_format(($boq['pengawas']['lembur'] + $boq['pelaksana']['lembur']) * 1.025, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr style="background-color: #FFE4E1;">
                                <td class="ps-4"><strong>Total Nilai 1 bulan</strong></td>
                                <td colspan="2" class="text-end">
                                    <strong>{{ number_format($boq['total_bulanan'], 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr style="background-color: #D8BFD8;">
                                <td class="ps-4"><strong>Total Nilai 36 bulan</strong></td>
                                <td colspan="2" class="text-end">
                                    <strong>{{ number_format($boq['total_bulanan'] * 36, 0, ',', '.') }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Notes -->
                <div class="mt-3 small fst-italic">
                    <p class="mb-1">*) Nilai tersebut akan disesuaikan apabila ada kenaikan UMP/UMK/UMR</p>
                    <p class="mb-1">*) Nilai tersebut belum termasuk ppn</p>
                    <p class="mb-1">*) Nilai Fee sudah termasuk Pajak Penghasilan (PPH)</p>
                </div>

                <!-- Second Table - Pekerjaan Tambah -->
                <h6 class="mt-4 fw-bold">Apabila terdapat pekerjaan tambah sesuai ketentuan PPK dan Perjanjian, maka:</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr style="background-color: #D8BFD8;">
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
                                <td rowspan="3" class="small align-top">• Nilai sesuai realisasi biaya yang dikeluarkan,
                                    yang disetujui unit kerja dan SDM</td>
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
                            <tr style="background-color: #E6E6FA;">
                                <td colspan="2" class="text-center"><strong>Total</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center"><strong>Fee Pekerjaan Tambah</strong></td>
                                <td></td>
                                <td class="small">• Nilai ini sudah termasuk pajak penghasilan (PPH)</td>
                            </tr>
                            <tr style="background-color: #E6E6FA;">
                                <td colspan="2" class="text-center"><strong>Total Pekerjaan Tambah</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="small fst-italic">
                    <p class="mb-1">*) Nilai tersebut belum termasuk ppn</p>
                </div>
            </div>
        </div>
    </div>
@endsection