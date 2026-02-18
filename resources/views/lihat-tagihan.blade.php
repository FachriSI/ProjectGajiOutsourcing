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
            <h4 class="mb-0 text-gray-800">
                <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
                Tagihan BOQ: <span class="text-primary">{{ $boq['paket']->paket }}</span>
            </h4>
            <div>
                <a href="{{ route('kalkulator.show', ['paket_id' => $boq['paket']->paket_id, 'periode' => \Carbon\Carbon::parse($boq['nilai_kontrak']->periode)->format('Y-m')]) }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('kalkulator.cetak-thr', ['paket_id' => $boq['paket']->paket_id, 'periode' => \Carbon\Carbon::parse($boq['nilai_kontrak']->periode)->format('Y-m')]) }}" class="btn btn-outline-primary me-2" target="_blank">
                    <i class="fas fa-print me-1"></i> Cetak THR
                </a>
                <a href="{{ route('paket.pdf.download', $boq['paket']->paket_id) }}" class="btn btn-primary" target="_blank">
                    <i class="fas fa-print me-1"></i> Cetak PDF
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom border-primary border-3 py-3 text-center">
                <h5 class="mb-0 fw-bold text-dark">BOQ Jasa Pekerjaan Penunjang Operasional di PT Semen Padang ({{ $boq['tahun'] }})</h5>
            </div>
            <div class="card-body p-4">
                <!-- Info Section -->
                <table class="table table-borderless mb-4" style="width: auto;">
                    <tr>
                        <td class="fw-bold text-muted text-uppercase small" style="width: 200px;">Vendor</td>
                        <td class="fw-bold text-dark">: {{ $boq['vendor'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted text-uppercase small">Jumlah Pekerja Alih Daya</td>
                        <td class="fw-bold text-dark">: {{ $boq['jumlah_pekerja'] }} orang</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted text-uppercase small">Jangka Waktu Kontrak</td>
                        <td class="fw-bold text-dark">: 36 bulan</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted text-uppercase small">Pekerjaan POS (Paket)</td>
                        <td class="fw-bold text-primary">: {{ $boq['paket']->paket }}</td>
                    </tr>
                </table>

                <!-- Main BOQ Table -->
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-uppercase text-secondary small py-3" style="width: 55%;">KOMPONEN UPAH</th>
                                <th class="text-center text-uppercase text-secondary small py-3" style="width: 22%;">Pengawas</th>
                                <th class="text-center text-uppercase text-secondary small py-3" style="width: 23%;">Pelaksana</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- I. UPAH PEKERJA ALIH DAYA -->
                            <tr class="bg-light">
                                <td colspan="3" class="fw-bold text-primary py-2">I. UPAH PEKERJA ALIH DAYA</td>
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
                            <tr class="bg-light">
                                <td colspan="3" class="fw-bold text-primary py-2">II. UPAH NORMATIF (Sesuai UU)</td>
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
                            <tr class="bg-light">
                                <td class="fw-bold text-primary py-2">III. UANG KOMPENSASI (Sesuai PP No 35 Th 2021)</td>
                                <td class="text-end">{{ number_format($boq['pengawas']['kompensasi'], 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($boq['pelaksana']['kompensasi'], 0, ',', '.') }}</td>
                            </tr>

                            <!-- Nilai Kontrak per orang -->
                            <tr>
                                <td class="fw-bold text-dark">Nilai Kontrak 1 orang Pekerja Alih Daya perbulan</td>
                                <td class="text-end fw-bold">
                                    {{ $boq['pengawas']['count'] > 0 ? number_format($boq['pengawas']['nilai_kontrak'] / $boq['pengawas']['count'], 0, ',', '.') : 0 }}
                                </td>
                                <td class="text-end fw-bold">
                                    {{ $boq['pelaksana']['count'] > 0 ? number_format($boq['pelaksana']['nilai_kontrak'] / $boq['pelaksana']['count'], 0, ',', '.') : 0 }}
                                </td>
                            </tr>

                            <!-- IV. PEKERJAAN TAMBAH LEMBUR -->
                            <tr class="bg-light">
                                <td colspan="3" class="fw-bold text-primary py-2">IV. PEKERJAAN TAMBAH LEMBUR (Sesuai UU)</td>
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
                            <tr>
                                <td class="fw-bold text-dark">Nilai Lembur 1 orang Pekerja Alih Daya perbulan</td>
                                <td class="text-end fw-bold">
                                    {{ $boq['pengawas']['count'] > 0 ? number_format($boq['pengawas']['lembur'] / $boq['pengawas']['count'], 0, ',', '.') : 0 }}
                                </td>
                                <td class="text-end fw-bold">
                                    {{ $boq['pelaksana']['count'] > 0 ? number_format($boq['pelaksana']['lembur'] / $boq['pelaksana']['count'], 0, ',', '.') : 0 }}
                                </td>
                            </tr>

                            <!-- Summary Section -->
                            <tr class="table-light">
                                <td class="fw-bold">Jumlah Pekerja Alih Daya (sesuai Kontrak)</td>
                                <td class="text-center fw-bold">{{ $boq['pengawas']['count'] }}</td>
                                <td class="text-center fw-bold">{{ $boq['pelaksana']['count'] }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted">Nilai Kontrak Pekerja Alih Daya 1 bulan</td>
                                <td colspan="2" class="text-end text-muted">
                                    {{ number_format($boq['pengawas']['nilai_kontrak'] + $boq['pelaksana']['nilai_kontrak'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nilai Fee Kontrak Pekerja Alih Daya 1 bulan (10%)</td>
                                <td colspan="2" class="text-end text-muted">
                                    {{ number_format(($boq['pengawas']['nilai_kontrak'] + $boq['pelaksana']['nilai_kontrak']) * 0.1, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td class="ps-4 fw-bold text-dark">Total Nilai Kontrak Pekerja Alih Daya 1 bulan</td>
                                <td colspan="2" class="text-end fw-bold text-dark">
                                    {{ number_format(($boq['pengawas']['nilai_kontrak'] + $boq['pelaksana']['nilai_kontrak']) * 1.1, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted">Nilai Lembur 1 bulan</td>
                                <td colspan="2" class="text-end text-muted">
                                    {{ number_format($boq['pengawas']['lembur'] + $boq['pelaksana']['lembur'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted">Nilai Fee Lembur 1 bulan (2.5%)</td>
                                <td colspan="2" class="text-end text-muted">
                                    {{ number_format(($boq['pengawas']['lembur'] + $boq['pelaksana']['lembur']) * 0.025, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td class="fw-bold text-dark">Total Nilai Lembur Pekerja Alih Daya 1 bulan</td>
                                <td colspan="2" class="text-end fw-bold text-dark">
                                    {{ number_format(($boq['pengawas']['lembur'] + $boq['pelaksana']['lembur']) * 1.025, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="bg-light border-top border-2">
                                <td class="ps-4 fw-bold text-dark h6 mb-0">Total Nilai 1 bulan</td>
                                <td colspan="2" class="text-end fw-bold text-primary h6 mb-0">
                                    Rp {{ number_format($boq['total_bulanan'], 0, ',', '.') }}</td>
                            </tr>
                            <tr class="bg-white border-top border-primary border-3">
                                <td class="ps-4 fw-bold text-uppercase text-dark h5 mb-0">Total Nilai 36 bulan</td>
                                <td colspan="2" class="text-end fw-bold text-dark h4 mb-0">
                                    Rp {{ number_format($boq['total_bulanan'] * 36, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Notes -->
                <div class="mt-3 small text-muted">
                    <p class="mb-1"><i class="fas fa-info-circle me-1"></i> Nilai tersebut akan disesuaikan apabila ada kenaikan UMP/UMK/UMR</p>
                    <p class="mb-1"><i class="fas fa-info-circle me-1"></i> Nilai tersebut belum termasuk ppn</p>
                    <p class="mb-1"><i class="fas fa-info-circle me-1"></i> Nilai Fee sudah termasuk Pajak Penghasilan (PPH)</p>
                </div>

                <!-- Second Table - Pekerjaan Tambah -->
                <h6 class="mt-5 fw-bold text-dark border-bottom pb-2 mb-3">Apabila terdapat pekerjaan tambah sesuai ketentuan PPK dan Perjanjian, maka:</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center text-uppercase text-secondary small py-2" style="width: 5%;">No.</th>
                                <th class="text-uppercase text-secondary small py-2" style="width: 35%;">Komponen</th>
                                <th class="text-uppercase text-secondary small py-2" style="width: 25%;">Nilai Rupiah</th>
                                <th class="text-uppercase text-secondary small py-2" style="width: 35%;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>Biaya Perjalanan Dinas</td>
                                <td></td>
                                <td rowspan="3" class="small align-top text-muted">• Nilai sesuai realisasi biaya yang dikeluarkan,
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
                            <tr class="bg-light">
                                <td colspan="2" class="text-center fw-bold">Total</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center fw-bold">Fee Pekerjaan Tambah</td>
                                <td></td>
                                <td class="small text-muted">• Nilai ini sudah termasuk pajak penghasilan (PPH)</td>
                            </tr>
                            <tr class="bg-light">
                                <td colspan="2" class="text-center fw-bold">Total Pekerjaan Tambah</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="small text-muted mt-3">
                    <p class="mb-1">*) Nilai tersebut belum termasuk ppn</p>
                </div>
            </div>
        </div>
    </div>
@endsection