@extends('layouts.main')

@section('title', 'Detail Paket')

@section('content')
    @php
        $total_kontrak_all = 0;
        $total_kontrak_tahunan_all = 0;
        $total_thr_bln = 0;
        $total_thr_thn = 0;
        $total_pakaian_all = 0;
        $total_jml_fix_cost = 0;
        $total_fix_cost = 0;
        $total_seluruh_variabel = 0;

        foreach ($data as $item) {
            $ump = $item->lokasi['ump'][0]['ump'] ?? ($item->lokasi['ump']['ump'] ?? 0); // Fix array access if collection
            $ump_sumbar = $item->ump_sumbar ?? 0;
            
            $upah_pokok = $ump_sumbar;
            $tj_umum = 0; // Removed/Merged into Upah Pokok

            $selisih_ump = round($ump - $ump_sumbar);
            $tj_lokasi = $item->kode_lokasi == 12 ? 0 : max($selisih_ump, 300000);

            $tj_jabatan = round($item->tunjangan_jabatan ?? 0);
            $tj_masakerja = round($item->tunjangan_masakerja ?? 0);
            $tj_suai = round($item->tunjangan_penyesuaian ?? 0);
            $tj_harianshift = round($item->harianshift['tunjangan_shift'] ?? 0);
            $tj_resiko = ($item->kode_resiko == 2) ? 0 : round($item->resiko['tunjangan_resiko'] ?? 0);
            $tj_presensi = round($upah_pokok * 0.08);

            $t_tdk_tetap = $tj_suai + $tj_harianshift + $tj_presensi + $tj_resiko;
            $t_tetap = $tj_jabatan + $tj_masakerja;

            $komponen_gaji = $upah_pokok + $t_tetap + $tj_lokasi;
            $bpjs_kesehatan = round(0.04 * $komponen_gaji);
            $bpjs_ketenagakerjaan = round(0.0689 * $komponen_gaji);

            $uang_jasa = $item->perusahaan_id == 38
                ? round(($upah_pokok + $t_tetap + $t_tdk_tetap) / 12)
                : 0;

            $kompensasi = round($komponen_gaji / 12);

            $fix_cost = round($upah_pokok + $t_tetap + $t_tdk_tetap + $bpjs_kesehatan + $bpjs_ketenagakerjaan + $uang_jasa + $kompensasi);
            $fee_fix_cost = round(0.10 * $fix_cost);
            $jumlah_fix_cost = round($fix_cost + $fee_fix_cost);

            $total_fix_cost += $fix_cost;
            $total_jml_fix_cost += $jumlah_fix_cost;

            // Lembur
            $quota_jam_perkalian = 2 * ($item->kuota ?? 0);
            $tarif_lembur = round((($upah_pokok + $t_tetap + $t_tdk_tetap) * 0.75) / 173);
            $nilai_lembur = round($tarif_lembur * $quota_jam_perkalian);
            $fee_lembur = round(0.025 * $nilai_lembur);
            $total_variabel = $nilai_lembur + $fee_lembur;
            $total_seluruh_variabel += $total_variabel;

            $total_kontrak = $jumlah_fix_cost + $total_variabel;
            $total_kontrak_tahunan = $total_kontrak * 12;

            $total_kontrak_all += $total_kontrak;
            $total_kontrak_tahunan_all += $total_kontrak_tahunan;

            // THR
            $thr = round(($upah_pokok + $t_tetap) / 12);
            $fee_thr = round($thr * 0.05);
            $thr_bln = $thr + $fee_thr;
            $thr_thn = $thr_bln * 12;

            $total_thr_bln += $thr_bln;
            $total_thr_thn += $thr_thn;

            // Pakaian
            $pakaian = $item->nilai_jatah ?? 0;
            $fee_pakaian = round(0.05 * $pakaian);
            $total_pakaian = $pakaian + $fee_pakaian;
            $total_pakaian_all += $total_pakaian;
        }
    @endphp

    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h3>Detail {{ $paketList->first()->paket ?? 'Paket' }}</h3>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center">
                <label class="me-2 text-nowrap fw-bold text-muted small text-uppercase">Periode Kontrak:</label>
                <input type="month" name="periode" class="form-control form-control-sm me-2" 
                       value="{{ $selectedPeriode }}" onchange="this.form.submit()">

            </form>

            <form action="{{ route('paket.hitung', $paketList->first()->paket_id) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="periode" value="{{ $selectedPeriode }}">
                <button type="submit" class="btn btn-primary shadow-sm me-2">
                    <i class="fas fa-calculator me-1"></i> Hitung / Refresh
                </button>
            </form>


            <a href="/gettambah-karyawan?paket_id={{ $paketList->first()->paket_id ?? '' }}" class="btn btn-success shadow-sm">
                <i class="fas fa-user-plus me-1"></i> Tambah Karyawan
            </a>
            <a href="/paket" class="btn btn-secondary shadow-sm">Kembali</a>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Annual Stat: Total Kontrak / Tahun -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; background-color: #e0e7ff; color: #4e73df;">
                            <i class="fas fa-star fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Kontrak/Tahun</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_kontrak_tahunan_all, 0, ',', '.') }}</div>

                </div>
            </div>
        </div>

        <!-- Annual Stat: THR / Tahun -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; background-color: #ffe2e5; color: #e74a3b;">
                            <i class="fas fa-gift fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total THR/Tahun</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_thr_thn, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Annual Stat: MCU / Tahun -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; background-color: #e0f7fa; color: #00bcd4;">
                            <i class="fas fa-heartbeat fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total MCU/Tahun</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_mcu_paket, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Annual Stat: Pakaian / Tahun -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; background-color: #fff3cd; color: #f6c23e;">
                            <i class="fas fa-tshirt fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Pakaian/Tahun</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_pakaian_all, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: MONTHLY BREAKDOWN -->
    <div class="row mb-4">
        <!-- Monthly 1: Fix Cost -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; background-color: #ccf6ff; color: #17a2b8;">
                            <i class="fas fa-tags fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Fix Cost/Bln</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_jml_fix_cost, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Monthly 2: Variabel -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; background-color: #e2e3e5; color: #383d41;">
                            <i class="fas fa-chart-area fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Variabel/Bln</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_seluruh_variabel, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Monthly 3: Total Kontrak/Bln -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; background-color: #d1e7dd; color: #0f5132;">
                            <i class="fas fa-file-contract fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Kontrak/Bln</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_kontrak_all, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Monthly 4: THR/Bln -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; background-color: #fff3cd; color: #856404;">
                            <i class="fas fa-hand-holding-usd fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total THR/Bln</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_thr_bln, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>




    <!-- Outer container matches index page style (Shadow & Card) -->
    <div class="card mb-4 border-0 shadow">
        <div class="card-body">
            <table id="datatablesSimple" class="table table-bordered table-hover display nowrap" style="width:100%">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 30px;"></th> <!-- Expand icon -->
                        <th>No.</th>
                        <th>OSIS ID</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Vendor/Perusahaan</th>
                        <th>Aktif Mulai</th>
                        <th>Upah Pokok</th>
                        <th>Tj. Tetap</th>
                        <th>Tj. Tidak Tetap</th>
                        <th>Tj. Lokasi</th>
                        <th>BPJS Kesehatan</th>
                        <th>BPJS Ketenagakerjaan</th>
                        <th>Kompensasi</th>
                        <th>Nilai Kontrak/Orang/Bln</th>
                        <th>Tarif Lembur/Jam</th>
                        <th>Nilai Lembur/Orang/Bln</th>
                        <th>MCU</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                            @php 
                                    $upah_pokok = $item->ump_sumbar;
                                    $tj_umum = 0;
                                    $selisih_ump = round(($item->lokasi['ump']['ump'] ?? 0) - ($item->ump_sumbar ?? 0));
                                    $tj_lokasi = $item->kode_lokasi == 12 ? 0 : max($selisih_ump, 300000);
                                    $tj_jabatan = $item->tunjangan_jabatan;
                                    $tj_suai = $item->tunjangan_penyesuaian;
                                    $tj_resiko = $item->kode_resiko == 2 ? 0 : $item->resiko['tunjangan_resiko'];
                                    $tj_presensi = $upah_pokok * 0.08;
                                    $tj_harianshift = $item->tunjangan_shift ?? 0;
                                    $tj_masakerja = $item->tunjangan_masakerja ?? 0;

                                    $t_tdk_tetap = $tj_suai + $tj_harianshift + $tj_presensi + $tj_resiko;
                                    $t_tetap = $tj_jabatan + $tj_masakerja;

                                    $bpjs_kesehatan = 0.04 * ($upah_pokok + $t_tetap + $tj_lokasi);
                                    $bpjs_ketenagakerjaan = 0.0689 * ($upah_pokok + $t_tetap + $tj_lokasi);

                                    $uang_jasa = ($item->perusahaan_id == 38 ? ($upah_pokok + $t_tetap + $t_tdk_tetap) / 12 : 0);
                                    $kompensasi = ($upah_pokok + $t_tetap + $tj_lokasi) / 12;

                                    $fix_cost = round($upah_pokok + $t_tetap + $t_tdk_tetap + $bpjs_kesehatan + $bpjs_ketenagakerjaan + $uang_jasa + $kompensasi);
                                    $fee_fix_cost = round(0.10 * $fix_cost);
                                    $jumlah_fix_cost = round($fix_cost + $fee_fix_cost);

                                    $quota_jam_perkalian = 2 * $item->kuota;
                                    $tarif_lembur = round((($upah_pokok + $t_tetap + $t_tdk_tetap) * 0.75) / 173);
                                    $nilai_lembur = round($tarif_lembur * $quota_jam_perkalian);
                                    $fee_lembur = 0.025 * $nilai_lembur;
                                    $total_variabel = $nilai_lembur + $fee_lembur;

                                    $total_kontrak = $jumlah_fix_cost + $total_variabel;
                                    $total_kontrak_tahunan = $total_kontrak * 12;

                                    $thr = round(($upah_pokok + $t_tetap) / 12);
                                    $fee_thr = round($thr * 0.05);

                                    // Yearly THR Components
                                    $thr_base_thn = $thr * 12;
                                    $fee_thr_thn = $fee_thr * 12;

                                    $thr_bln = $thr + $fee_thr;
                                    $thr_thn = $thr_bln * 12;

                                    $pakaian = $item->nilai_jatah ?? 0;
                                    $fee_pakaian = round(0.05 * $pakaian);
                                    $total_pakaian = $pakaian + $fee_pakaian;

                                    $mcu = $item->mcu ?? 0;
                                @endphp
           <tr class="parent-row" 
            data-tj-umum="{{ number_format($tj_umum, 0, ',', '.') }}"
            data-tj-jabatan="{{ number_format($tj_jabatan, 0, ',', '.') }}"
            data-tj-masakerja="{{ number_format($tj_masakerja, 0, ',', '.') }}"
            data-tj-suai="{{ number_format($tj_suai, 0, ',', '.') }}"
            data-tj-resiko="{{ number_format($tj_resiko, 0, ',', '.') }}"
            data-tj-shift="{{ number_format($tj_harianshift, 0, ',', '.') }}"
            data-tj-presensi="{{ number_format($tj_presensi, 0, ',', '.') }}"
            data-uang-jasa="{{ number_format($uang_jasa, 0, ',', '.') }}"
            data-fix-cost="{{ number_format($fix_cost, 0, ',', '.') }}"
            data-fee-fix-cost="{{ number_format($fee_fix_cost, 0, ',', '.') }}"
            data-jumlah-fix-cost="{{ number_format($jumlah_fix_cost, 0, ',', '.') }}"
            data-quota-jam="{{ number_format($quota_jam_perkalian, 0, ',', '.') }}"
            data-nilai-lembur="{{ number_format($nilai_lembur, 0, ',', '.') }}"
            data-fee-lembur="{{ number_format($fee_lembur, 0, ',', '.') }}"
            data-total-variabel="{{ number_format($total_variabel, 0, ',', '.') }}"
            data-total-kontrak-bln="{{ number_format($total_kontrak, 0, ',', '.') }}"
            data-total-kontrak-thn="{{ number_format($total_kontrak_tahunan, 0, ',', '.') }}"
            data-thr-base-thn="{{ number_format($thr_base_thn, 0, ',', '.') }}"
            data-fee-thr-thn="{{ number_format($fee_thr_thn, 0, ',', '.') }}"
            data-thr-thn="{{ number_format($thr_thn, 0, ',', '.') }}"
            data-total-pakaian="{{ number_format($total_pakaian, 0, ',', '.') }}">
            
            <td class="details-control" style="cursor: pointer; text-align: center;">
                <button class="btn btn-sm btn-outline-primary toggle-details" style="font-size: 11px; padding: 2px 8px;">
                    <i class="fas fa-plus-circle"></i> Detail
                </button>
            </td>
            <td>{{ $loop->iteration }}</td>
            <td>{{$item->osis_id}}</td>
            <td>{{$item->nama_tk}}</td>
            <td>{{$item->jabatan->nama_jabatan ?? '-'}}</td>
            <td>{{$item->perusahaan}}</td>
            <td>{{$item->aktif_mulai}}</td>
            <td>{{ number_format($upah_pokok, 0, ',', '.') }}</td>
            <td>{{ number_format($t_tetap, 0, ',', '.') }}</td>
            <td>{{ number_format($t_tdk_tetap, 0, ',', '.') }}</td>
            <td>{{ number_format($tj_lokasi, 0, ',', '.') }}</td>
            <td>{{ number_format($bpjs_kesehatan, 0, ',', '.') }}</td>
            <td>{{ number_format($bpjs_ketenagakerjaan, 0, ',', '.') }}</td>
            <td>{{ number_format($kompensasi, 0, ',', '.') }}</td>
            <td>{{ number_format($jumlah_fix_cost, 0, ',', '.') }}</td>
            <td>{{ number_format($tarif_lembur, 0, ',', '.') }}</td>
            <td>{{ number_format($nilai_lembur, 0, ',', '.') }}</td>
            <td>{{ number_format($mcu, 0, ',', '.') }}</td>
        </tr>

                    @endforeach 
            </tbody>
        </table>
        </div>
    </div>

    <script>
    $(document).on('click', '.btn-berhenti', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        // ... stopped logic if needed here ...
    });

    $(document).ready(function () {
    // Function to format child row content
    function formatChildRow(data) {
        return `
            <div style="padding: 20px; background-color: #f8f9fa; border-left: 4px solid #007bff;">
                <h5 class="mb-3"><i class="fas fa-info-circle"></i> Detail Breakdown Komponen</h5>
                
                <div class="row">
                    <!-- Left Column: Tunjangan -->
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-money-bill-wave"></i> Breakdown Tunjangan Tetap & Tidak Tetap</h6>
                        <table class="table table-sm table-bordered">
                            <tr><td><strong>Tj. Umum</strong></td><td class="text-right">Rp${data.tjUmum}</td></tr>
                            <tr><td><strong>Tj. Jabatan</strong></td><td class="text-right">Rp${data.tjJabatan}</td></tr>
                            <tr><td><strong>Tj. Masa Kerja</strong></td><td class="text-right">Rp${data.tjMasakerja}</td></tr>
                            <tr><td><strong>Tj. Penyesuaian</strong></td><td class="text-right">Rp${data.tjSuai}</td></tr>
                            <tr><td><strong>Tj. Resiko</strong></td><td class="text-right">Rp${data.tjResiko}</td></tr>
                            <tr><td><strong>Tj. Shift</strong></td><td class="text-right">Rp${data.tjShift}</td></tr>
                            <tr><td><strong>Tj. Presensi</strong></td><td class="text-right">Rp${data.tjPresensi}</td></tr>
                        </table>
                    </div>
                    
                    <!-- Right Column: Cost & Fee -->
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-calculator"></i> Breakdown Cost & Fee</h6>
                        <table class="table table-sm table-bordered">
                            <tr><td><strong>Uang Jasa</strong></td><td class="text-right">Rp${data.uangJasa}</td></tr>
                            <tr><td><strong>Fix Cost (sebelum fee)</strong></td><td class="text-right">Rp${data.fixCost}</td></tr>
                            <tr><td><strong>Fee Fix Cost (10%)</strong></td><td class="text-right">Rp${data.feeFixCost}</td></tr>
                            <tr><td><strong>Jumlah Fix Cost</strong></td><td class="text-right">Rp${data.jumlahFixCost}</td></tr>
                        </table>
                        
                        <h6 class="text-primary mt-3"><i class="fas fa-clock"></i> Detail Lembur</h6>
                        <table class="table table-sm table-bordered">
                            <tr><td><strong>Quota Jam Perkalian</strong></td><td class="text-right">${data.quotaJam} jam</td></tr>
                            <tr><td><strong>Nilai Lembur</strong></td><td class="text-right">Rp${data.nilaiLembur}</td></tr>
                            <tr><td><strong>Fee Lembur (2.5%)</strong></td><td class="text-right">Rp${data.feeLembur}</td></tr>
                            <tr><td><strong>Total Variabel</strong></td><td class="text-right">Rp${data.totalVariabel}</td></tr>
                        </table>
                    </div>
                </div>
                
                <!-- Bottom Row: Total & THR -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6 class="text-success"><i class="fas fa-file-invoice-dollar"></i> Total Kontrak & Benefit</h6>
                        <table class="table table-sm table-bordered">
                            <tr class="table-info">
                                <td><strong>Total Kontrak/Bulan</strong></td>
                                <td class="text-right"><strong>Rp${data.totalKontrakBln}</strong></td>
                            </tr>
                            <tr class="table-info">
                                <td><strong>Total Kontrak/Tahun</strong></td>
                                <td class="text-right"><strong>Rp${data.totalKontrakThn}</strong></td>
                            </tr>
                            <tr><td><strong>THR Base / Tahun</strong></td><td class="text-right">Rp${data.thrBaseThn}</td></tr>
                            <tr><td><strong>Fee THR (5%) / Tahun</strong></td><td class="text-right">Rp${data.feeThrThn}</td></tr>
                            <tr><td><strong>Total THR/Tahun</strong></td><td class="text-right">Rp${data.thrThn}</td></tr>
                            <tr><td><strong>Total Pakaian/Tahun</strong></td><td class="text-right">Rp${data.totalPakaian}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }

    if (!$.fn.DataTable.isDataTable('#datatablesSimple')) {
        var table = $('#datatablesSimple').DataTable({
            autoWidth: false, 
            order: [[1, 'asc']], // Order by No column
            lengthChange: false,
            columnDefs: [
                { orderable: false, targets: [0, 1] } // Disable sorting for expand icon and No columns
            ],
            language: {
                "search": "",
                "searchPlaceholder": "Cari data...",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            initComplete: function () {
                const tableApi = this.api();
                const container = $(tableApi.table().container());
                const infoDiv = container.find('.dataTables_info');

                // Create the checkbox HTML with separator
                const switchId = 'showAllSwitch_paketdetail';
                const checkboxHtml = `
                    <div class="d-inline-block me-2" style="vertical-align: middle;">
                        <div class="form-check d-inline-block me-2">
                            <input class="form-check-input btn-show-all-switch" type="checkbox" id="${switchId}" style="cursor: pointer;">
                            <label class="form-check-label small fw-bold text-muted" for="${switchId}" style="cursor: pointer;">Tampilkan semua</label>
                        </div>
                        <span class="text-muted me-2">|</span>
                    </div>
                `;

                // Create a wrapper for same-line alignment without affecting siblings (pagination)
                const flexWrapper = $('<div class="d-flex align-items-center flex-wrap mt-2"></div>');
                infoDiv.before(flexWrapper);
                flexWrapper.append(checkboxHtml).append(infoDiv);
                
                infoDiv.addClass('mb-0 ms-1');
                infoDiv.css('padding-top', '0'); // Reset padding to align with checkbox

                container.on('change', '.btn-show-all-switch', function () {
                    if (this.checked) {
                        tableApi.page.len(-1).draw();
                    } else {
                        tableApi.page.len(10).draw();
                    }
                });
            }
        });

        // Fix Numbering (1, 2, 3...) regardless of sort/search
        table.on('order.dt search.dt', function () {
            table.column(1, {search:'applied', order:'applied'}).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        // Expand/Collapse row functionality
        $('#datatablesSimple tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var button = $(this).find('button.toggle-details');
            var icon = button.find('i');

            if (row.child.isShown()) {
                // Close this row
                row.child.hide();
                tr.removeClass('shown');
                icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                button.removeClass('btn-outline-danger').addClass('btn-outline-primary');
                button.html('<i class="fas fa-plus-circle"></i> Detail');
            } else {
                // Open this row
                var data = {
                    tjUmum: tr.attr('data-tj-umum'),
                    tjJabatan: tr.attr('data-tj-jabatan'),
                    tjMasakerja: tr.attr('data-tj-masakerja'),
                    tjSuai: tr.attr('data-tj-suai'),
                    tjResiko: tr.attr('data-tj-resiko'),
                    tjShift: tr.attr('data-tj-shift'),
                    tjPresensi: tr.attr('data-tj-presensi'),
                    uangJasa: tr.attr('data-uang-jasa'),
                    fixCost: tr.attr('data-fix-cost'),
                    feeFixCost: tr.attr('data-fee-fix-cost'),
                    jumlahFixCost: tr.attr('data-jumlah-fix-cost'),
                    quotaJam: tr.attr('data-quota-jam'),
                    nilaiLembur: tr.attr('data-nilai-lembur'),
                    feeLembur: tr.attr('data-fee-lembur'),
                    totalVariabel: tr.attr('data-total-variabel'),
                    totalKontrakBln: tr.attr('data-total-kontrak-bln'),
                    totalKontrakThn: tr.attr('data-total-kontrak-thn'),
                    thrBaseThn: tr.attr('data-thr-base-thn'),
                    feeThrThn: tr.attr('data-fee-thr-thn'),
                    thrThn: tr.attr('data-thr-thn'),
                    totalPakaian: tr.attr('data-total-pakaian')
                };
                
                row.child(formatChildRow(data)).show();
                tr.addClass('shown');
                icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
                button.removeClass('btn-outline-primary').addClass('btn-outline-danger');
                button.html('<i class="fas fa-minus-circle"></i> Tutup');
            }
        });

        // Wrap ONLY the table with 'table-responsive' so controls stay outside but inside card body
        // Margin-top is adjusted to 10px since card-body already has padding
        $('#datatablesSimple').wrap('<div class="table-responsive" style="border:none; width:100%; margin-top: 10px;"></div>');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('historyChart').getContext('2d');
    const historyData = {!! json_encode($contractHistory ?? []) !!};
    
    // Safety check if no history
    if(historyData.length > 0) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: historyData.map(item => item.period),
                datasets: [{
                    label: 'Nilai Kontrak (Rp)',
                    data: historyData.map(item => item.total),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    pointRadius: 5,
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 7,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value, index, values) {
                                return new Intl.NumberFormat('id-ID', { notation: "compact", compactDisplay: "short" }).format(value);
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
