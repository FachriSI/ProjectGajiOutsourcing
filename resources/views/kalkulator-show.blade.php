@extends('layouts.main')

@section('title', 'Detail Kontrak')

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h3>Detail Nilai Kontrak</h3>
        <a href="{{ route('paket.tagihan', ['id' => $nilaiKontrak->paket_id, 'periode' => \Carbon\Carbon::parse($nilaiKontrak->periode)->format('Y-m')]) }}" class="btn btn-primary">
            <i class="fas fa-file-invoice-dollar me-2"></i>Lihat Tagihan Lengkap
        </a>
    </div>
    <p class="text-muted">Rincian lengkap perhitungan nilai kontrak untuk paket {{ $nilaiKontrak->paket->paket }}</p>

    <div class="container-fluid px-0">
        <!-- Header Actions -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <p class="mb-2"><strong>Unit Kerja:</strong></p>
                        <p class="text-muted">{{ $nilaiKontrak->paket->unitKerja->unit_kerja ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-2"><strong>Periode:</strong></p>
                        <p class="text-muted">{{ \Carbon\Carbon::parse($nilaiKontrak->periode)->format('F Y') }}</p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-2"><strong>Tanggal Perhitungan:</strong></p>
                        <p class="text-muted">{{ $nilaiKontrak->calculated_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-2"><strong>UMP Sumbar {{ $nilaiKontrak->tahun }}:</strong></p>
                        <p class="text-primary fw-bold">Rp {{ number_format($nilaiKontrak->ump_sumbar, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Nilai Kontrak -->
        <div class="card shadow mb-4">
            <div class="card-body text-center py-4" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%);">
                <h6 class="text-white mb-2 text-uppercase" style="letter-spacing: 1px;">
                    <i class="fas fa-money-bill-wave me-2"></i>Total Nilai Kontrak per Bulan
                </h6>
                <h1 class="display-3 text-white fw-bold mb-0">
                    Rp {{ number_format($nilaiKontrak->total_nilai_kontrak, 0, ',', '.') }}
                </h1>
                <div class="mt-3">
                    <span class="badge bg-light text-success px-3 py-2">
                        <i class="fas fa-calendar-check me-1"></i> 
                        Periode: {{ \Carbon\Carbon::parse($nilaiKontrak->periode)->format('F Y') }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Parameter Cards -->
        @php
            // Calculate components for cards
            $breakdown = $nilaiKontrak->breakdown_json ?? [];
            
            // 1. Total Fix Cost (Total Nilai Kontrak from breakdown is fix cost before variabel)
            // Or better: Fix Cost = Total - Variabel
            $variabelCost = ($breakdown['pengawas']['lembur'] ?? 0) + ($breakdown['pelaksana']['lembur'] ?? 0);
            $fixCost = $nilaiKontrak->total_nilai_kontrak - $variabelCost;
            
            // 2. Variabel Cost (Lembur + Fee Lembur)
            
            // 3. Total Kontrak / Bln
            $totalKontrakBln = $nilaiKontrak->total_nilai_kontrak;
            
            // 4. Total Kontrak / Thn
            $totalKontrakThn = $totalKontrakBln * 12;
            
            // 5. THR / Bln
            // THR = (Upah Pokok + Tj. Tetap + Tj. Lokasi) / 12 * 1.05
            $thrPengawas = (($breakdown['pengawas']['upah_pokok'] ?? 0) + ($breakdown['pengawas']['tj_tetap'] ?? 0) + ($breakdown['pengawas']['tj_lokasi'] ?? 0)) / 12 * 1.05;
            $thrPelaksana = (($breakdown['pelaksana']['upah_pokok'] ?? 0) + ($breakdown['pelaksana']['tj_tetap'] ?? 0) + ($breakdown['pelaksana']['tj_lokasi'] ?? 0)) / 12 * 1.05;
            $totalThrBln = $thrPengawas + $thrPelaksana;
            
            // 6. THR / Thn
            $totalThrThn = $totalThrBln * 12;
            
            // 7. Pakaian / Thn
            // 600,000 + 5% fee = 630,000 per orang
            $pakaianPerOrg = 630000;
            $totalPakaianThn = $pakaianPerOrg * $nilaiKontrak->jumlah_karyawan_total;
        @endphp

        <div class="row mb-4">
            <!-- Row 1: Monthly Stats -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #3DD9E2 0%, #17a2b8 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="text-white fw-bold mb-2" style="font-size: 0.95rem;">Total Jml Fix Cost/Bln</div>
                                <div class="h4 mb-0 fw-bold text-white">Rp {{ number_format($fixCost, 0, ',', '.') }}</div>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="fas fa-tags fa-lg text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #868e96 0%, #6c757d 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="text-white fw-bold mb-2" style="font-size: 0.95rem;">Total Variabel Cost/Bln</div>
                                <div class="h4 mb-0 fw-bold text-white">Rp {{ number_format($variabelCost, 0, ',', '.') }}</div>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="fas fa-chart-area fa-lg text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #38D39F 0%, #28a745 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="text-white fw-bold mb-2" style="font-size: 0.95rem;">Total Kontrak/Bln</div>
                                <div class="h4 mb-0 fw-bold text-white">Rp {{ number_format($totalKontrakBln, 0, ',', '.') }}</div>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="fas fa-file-contract fa-lg text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #F5A623 0%, #F2994A 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="text-white fw-bold mb-2" style="font-size: 0.95rem;">Total THR/Bln</div>
                                <div class="h4 mb-0 fw-bold text-white">Rp {{ number_format($totalThrBln, 0, ',', '.') }}</div>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="fas fa-hand-holding-usd fa-lg text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Row 2: Annual Stats -->
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card border-0 shadow-lg h-100 card-hover" style="border-radius: 20px; transition: all 0.3s ease;">
                    <div class="card-body p-4 position-relative d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                         <div class="row flex-fill align-items-center">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                        <i class="fas fa-star fa-2x text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="text-white fw-bold" style="font-size: 1rem;">TOTAL KONTRAK / TAHUN</div>
                                        <div class="h3 fw-bold text-white mb-0 mt-1">Rp {{ number_format($totalKontrakThn, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #F093FB 0%, #F5576C 100%);">
                        <div class="d-flex justify-content-between align-items-start h-100 flex-column">
                            <div class="w-100 d-flex justify-content-between mb-2">
                                <div class="text-white fw-bold" style="font-size: 0.95rem;">Total THR/Tahun</div>
                                <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                    <i class="fas fa-gift fa-lg text-white"></i>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="h3 mb-0 fw-bold text-white">Rp {{ number_format($totalThrThn, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #4A5568 0%, #2D3748 100%);">
                        <div class="d-flex justify-content-between align-items-start h-100 flex-column">
                            <div class="w-100 d-flex justify-content-between mb-2">
                                <div class="text-white fw-bold" style="font-size: 0.95rem;">Total Pakaian/Tahun</div>
                                <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                    <i class="fas fa-tshirt fa-lg text-white"></i>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="h3 mb-0 fw-bold text-white">Rp {{ number_format($totalPakaianThn, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown Pengawas vs Pelaksana -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-tie me-2"></i>Pengawas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="mb-0 text-primary">{{ $nilaiKontrak->jumlah_pengawas }}</h2>
                            <span class="text-muted">Orang</span>
                        </div>
                        <hr>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total Nilai:</span>
                                <strong class="text-primary">
                                    Rp {{ number_format($nilaiKontrak->total_pengawas, 0, ',', '.') }}
                                </strong>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Rata-rata/Orang:</span>
                                <strong>
                                    Rp {{ $nilaiKontrak->jumlah_pengawas > 0 ? number_format($nilaiKontrak->total_pengawas / $nilaiKontrak->jumlah_pengawas, 0, ',', '.') : 0 }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>Pelaksana
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="mb-0 text-success">{{ $nilaiKontrak->jumlah_pelaksana }}</h2>
                            <span class="text-muted">Orang</span>
                        </div>
                        <hr>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total Nilai:</span>
                                <strong class="text-success">
                                    Rp {{ number_format($nilaiKontrak->total_pelaksana, 0, ',', '.') }}
                                </strong>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Rata-rata/Orang:</span>
                                <strong>
                                    Rp {{ $nilaiKontrak->jumlah_pelaksana > 0 ? number_format($nilaiKontrak->total_pelaksana / $nilaiKontrak->jumlah_pelaksana, 0, ',', '.') : 0 }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kombinasi: Perbandingan + Statistik Karyawan -->
        <div class="row mb-4">
            @if($previousNilai)
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>Perbandingan dengan Periode Sebelumnya
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="p-3">
                                    <small class="text-muted d-block mb-1">Periode Sebelumnya</small>
                                    <h6 class="mb-2">{{ \Carbon\Carbon::parse($previousNilai->periode)->format('F Y') }}</h6>
                                    <h5 class="text-secondary">Rp {{ number_format($previousNilai->total_nilai_kontrak, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                @php
                                    $delta = $nilaiKontrak->total_nilai_kontrak - $previousNilai->total_nilai_kontrak;
                                    $percentage = $previousNilai->total_nilai_kontrak > 0 ? ($delta / $previousNilai->total_nilai_kontrak) * 100 : 0;
                                @endphp
                                <div class="p-3 bg-light h-100 d-flex flex-column justify-content-center">
                                    <i class="fas fa-{{ $delta >= 0 ? 'arrow-up' : 'arrow-down' }} fa-2x mb-2 text-{{ $delta >= 0 ? 'success' : 'danger' }}"></i>
                                    <h5 class="mb-1 text-{{ $delta >= 0 ? 'success' : 'danger' }}">
                                        {{ $delta >= 0 ? '+' : '' }}Rp {{ number_format(abs($delta), 0, ',', '.') }}
                                    </h5>
                                    <span class="badge bg-{{ $delta >= 0 ? 'success' : 'danger' }}">
                                        {{ $delta >= 0 ? '+' : '' }}{{ number_format($percentage, 2) }}%
                                    </span>
                                    <button class="btn btn-sm btn-outline-{{ $delta >= 0 ? 'success' : 'danger' }} mt-2" data-bs-toggle="modal" data-bs-target="#comparisonDetailModal">
                                        <i class="fas fa-search me-1"></i>Lihat Detail
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <small class="text-muted d-block mb-1">Periode Sekarang</small>
                                    <h6 class="mb-2">{{ \Carbon\Carbon::parse($nilaiKontrak->periode)->format('F Y') }}</h6>
                                    <h5 class="text-primary">Rp {{ number_format($nilaiKontrak->total_nilai_kontrak, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>Statistik Karyawan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-user-check fa-2x text-success mb-2"></i>
                                    <h3 class="mb-0 text-success">{{ $nilaiKontrak->jumlah_karyawan_aktif }}</h3>
                                    <small class="text-muted">Karyawan Aktif</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                    <h3 class="mb-0 text-primary">{{ $nilaiKontrak->jumlah_karyawan_total }}</h3>
                                    <small class="text-muted">Total Karyawan</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-clipboard-list fa-2x text-info mb-2"></i>
                                    <h3 class="mb-0 text-info">{{ $nilaiKontrak->kuota_paket }}</h3>
                                    <small class="text-muted">Kuota Paket</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Detail Karyawan -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">

                    <div class="card-body">
                        <ul class="nav nav-tabs mb-3" id="employeeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">Semua</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="false">Aktif</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="inactive-tab" data-bs-toggle="tab" data-bs-target="#inactive" type="button" role="tab" aria-controls="inactive" aria-selected="false">Tidak Aktif</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="employeeTabsContent">
                            <!-- Tab Semua -->
                            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped datatable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Jabatan</th>
                                                <th>Kategori</th>
                                                <th>Status</th>
                                                <th class="text-end">Upah Pokok</th>
                                                <th class="text-end">Tj. Tetap</th>
                                                <th class="text-end">Tj. Tidak Tetap</th>
                                                <th class="text-end">Tj. Lokasi</th>
                                                <th class="text-end">BPJS Kes</th>
                                                <th class="text-end">BPJS TK</th>
                                                <th class="text-end">Kompensasi</th>
                                                <th class="text-end">Uang Jasa</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($nilaiKontrak->breakdown_json['karyawan'] ?? [] as $index => $karyawan)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $karyawan['nama'] ?? '-' }}</td>
                                                <td>{{ $karyawan['jabatan'] ?? '-' }}</td>
                                                <td>
                                                    <span class="badge {{ $karyawan['kategori'] == 'Pengawas' ? 'bg-primary' : 'bg-secondary' }}">
                                                        {{ $karyawan['kategori'] ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $karyawan['status'] == 'Aktif' ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $karyawan['status'] ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">{{ number_format($karyawan['upah_pokok'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['tj_tetap'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['tj_tidak_tetap'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['tj_lokasi'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['bpjs_kesehatan'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['bpjs_ketenagakerjaan'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['kompensasi'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['uang_jasa'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end"><strong>{{ number_format($karyawan['total'] ?? 0, 0, ',', '.') }}</strong></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab Aktif -->
                            <div class="tab-pane fade" id="active" role="tabpanel" aria-labelledby="active-tab">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped datatable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Jabatan</th>
                                                <th>Kategori</th>
                                                <th>Status</th>
                                                <th class="text-end">Upah Pokok</th>
                                                <th class="text-end">Tj. Tetap</th>
                                                <th class="text-end">Tj. Tidak Tetap</th>
                                                <th class="text-end">Tj. Lokasi</th>
                                                <th class="text-end">BPJS Kes</th>
                                                <th class="text-end">BPJS TK</th>
                                                <th class="text-end">Kompensasi</th>
                                                <th class="text-end">Uang Jasa</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($nilaiKontrak->breakdown_json['karyawan'] ?? [] as $index => $karyawan)
                                            @if(($karyawan['status'] ?? '') == 'Aktif')
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $karyawan['nama'] ?? '-' }}</td>
                                                <td>{{ $karyawan['jabatan'] ?? '-' }}</td>
                                                <td>
                                                    <span class="badge {{ $karyawan['kategori'] == 'Pengawas' ? 'bg-primary' : 'bg-secondary' }}">
                                                        {{ $karyawan['kategori'] ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        {{ $karyawan['status'] ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">{{ number_format($karyawan['upah_pokok'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['tj_tetap'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['tj_tidak_tetap'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['tj_lokasi'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['bpjs_kesehatan'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['bpjs_ketenagakerjaan'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['kompensasi'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['uang_jasa'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end"><strong>{{ number_format($karyawan['total'] ?? 0, 0, ',', '.') }}</strong></td>
                                            </tr>
                                            @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab Tidak Aktif -->
                            <div class="tab-pane fade" id="inactive" role="tabpanel" aria-labelledby="inactive-tab">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped datatable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Jabatan</th>
                                                <th>Kategori</th>
                                                <th>Status</th>
                                                <th class="text-end">Upah Pokok</th>
                                                <th class="text-end">Tj. Tetap</th>
                                                <th class="text-end">Tj. Tidak Tetap</th>
                                                <th class="text-end">Tj. Lokasi</th>
                                                <th class="text-end">BPJS Kes</th>
                                                <th class="text-end">BPJS TK</th>
                                                <th class="text-end">Kompensasi</th>
                                                <th class="text-end">Uang Jasa</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($nilaiKontrak->breakdown_json['karyawan'] ?? [] as $index => $karyawan)
                                            @if(($karyawan['status'] ?? '') != 'Aktif')
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $karyawan['nama'] ?? '-' }}</td>
                                                <td>{{ $karyawan['jabatan'] ?? '-' }}</td>
                                                <td>
                                                    <span class="badge {{ $karyawan['kategori'] == 'Pengawas' ? 'bg-primary' : 'bg-secondary' }}">
                                                        {{ $karyawan['kategori'] ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning">
                                                        {{ $karyawan['status'] ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">{{ number_format($karyawan['upah_pokok'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['tj_tetap'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['tj_tidak_tetap'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['tj_lokasi'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['bpjs_kesehatan'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['bpjs_ketenagakerjaan'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['kompensasi'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($karyawan['uang_jasa'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-end"><strong>{{ number_format($karyawan['total'] ?? 0, 0, ',', '.') }}</strong></td>
                                            </tr>
                                            @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons Removed -->
    </div>




    <!-- Modal Detail Perbandingan -->
    @if($previousNilai)
    <div class="modal fade" id="comparisonDetailModal" tabindex="-1" aria-labelledby="comparisonDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="comparisonDetailModalLabel"><i class="fas fa-balance-scale me-2"></i>Detail Perbandingan Nilai Kontrak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Komponen</th>
                                    <th>{{ \Carbon\Carbon::parse($previousNilai->periode)->format('F Y') }}</th>
                                    <th>{{ \Carbon\Carbon::parse($nilaiKontrak->periode)->format('F Y') }}</th>
                                    <th>Selisih (Rp)</th>
                                    <th>Selisih (%)</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $components = [
                                        'upah_pokok' => 'Upah Pokok',
                                        'tj_tetap' => 'Tunjangan Tetap',
                                        'tj_tidak_tetap' => 'Tunjangan Tidak Tetap',
                                        'tj_lokasi' => 'Tunjangan Lokasi',
                                        'bpjs_kesehatan' => 'BPJS Kesehatan',
                                        'bpjs_ketenagakerjaan' => 'BPJS Ketenagakerjaan',
                                        'kompensasi' => 'Kompensasi',
                                        'uang_jasa' => 'Uang Jasa',
                                        'lembur' => 'Biaya Lembur'
                                    ];

                                    // Helper function inside blade to sum breakdown
                                    $getSum = function($json, $key) {
                                        $pengawas = $json['pengawas'][$key] ?? 0;
                                        $pelaksana = $json['pelaksana'][$key] ?? 0;
                                        return $pengawas + $pelaksana;
                                    };
                                @endphp

                                @foreach($components as $key => $label)
                                    @php
                                        $valPrev = $getSum($previousNilai->breakdown_json ?? [], $key);
                                        $valCurr = $getSum($nilaiKontrak->breakdown_json ?? [], $key);
                                        $diff = $valCurr - $valPrev;
                                        $perc = $valPrev > 0 ? ($diff / $valPrev) * 100 : 0;
                                        
                                        // Logic Keterangan Sederhana
                                        $note = '-';
                                        if ($diff != 0) {
                                            if ($key == 'upah_pokok' && $nilaiKontrak->ump_sumbar != $previousNilai->ump_sumbar) {
                                                $note = 'Perubahan UMP';
                                            } elseif ($nilaiKontrak->jumlah_karyawan_total != $previousNilai->jumlah_karyawan_total) {
                                                $note = 'Jumlah Karyawan Berubah';
                                            } else {
                                                $note = 'Perubahan Komponen/Bobot';
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-start fw-bold">{{ $label }}</td>
                                        <td class="text-end">Rp {{ number_format($valPrev, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($valCurr, 0, ',', '.') }}</td>
                                        <td class="text-end text-{{ $diff >= 0 ? 'success' : 'danger' }}">
                                            {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end text-{{ $diff >= 0 ? 'success' : 'danger' }}">
                                            {{ $diff >= 0 ? '+' : '' }}{{ number_format($perc, 2) }}%
                                        </td>
                                        <td class="text-start text-muted small">{{ $note }}</td>
                                    </tr>
                                @endforeach

                                <!-- Separator -->
                                <tr class="table-active">
                                    <td colspan="6"></td>
                                </tr>
                                <!-- Grand Total -->
                                <tr class="table-primary">
                                    <td class="text-start"><strong>Grand Total</strong></td>
                                    <td class="text-end fw-bold">Rp {{ number_format($previousNilai->total_nilai_kontrak, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($nilaiKontrak->total_nilai_kontrak, 0, ',', '.') }}</td>
                                    @php
                                        $deltaTotal = $nilaiKontrak->total_nilai_kontrak - $previousNilai->total_nilai_kontrak;
                                        $percTotal = $previousNilai->total_nilai_kontrak > 0 ? ($deltaTotal / $previousNilai->total_nilai_kontrak) * 100 : 0;
                                    @endphp
                                    <td class="text-end fw-bold text-{{ $deltaTotal >= 0 ? 'success' : 'danger' }}">
                                        {{ $deltaTotal >= 0 ? '+' : '' }}Rp {{ number_format($deltaTotal, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-bold text-{{ $deltaTotal >= 0 ? 'success' : 'danger' }}">
                                        {{ $deltaTotal >= 0 ? '+' : '' }}{{ number_format($percTotal, 2) }}%
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif

@endsection
