@extends('layouts.main')

@section('title', 'Detail Kontrak')

@section('content')
    <div class="container-fluid py-3">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                <i class="fas fa-file-contract me-2"></i>
                Detail Kontrak: {{ $nilaiKontrak->paket->paket }}
            </h4>
            <div>
                <a href="{{ route('kalkulator.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('paket.pdf.download', $nilaiKontrak->paket_id) }}" class="btn btn-success" target="_blank">
                    <i class="fas fa-download me-1"></i> Download PDF
                </a>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Ringkasan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <p class="mb-2"><strong>Paket:</strong> {{ $nilaiKontrak->paket->paket }}</p>
                                <p class="mb-2"><strong>Unit Kerja:</strong> {{ $nilaiKontrak->paket->unitKerja->unit_kerja ?? '-' }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-2"><strong>Periode:</strong> {{ \Carbon\Carbon::parse($nilaiKontrak->periode)->format('F Y') }}</p>
                                <p class="mb-2"><strong>Dihitung:</strong> {{ $nilaiKontrak->calculated_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-2"><strong>UMP Sumbar:</strong> Rp {{ number_format($nilaiKontrak->ump_sumbar, 0, ',', '.') }}</p>
                                <p class="mb-2"><strong>Kuota Paket:</strong> {{ $nilaiKontrak->kuota_paket }} orang</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-2"><strong>Karyawan Aktif:</strong> {{ $nilaiKontrak->jumlah_karyawan_aktif }} orang</p>
                                <p class="mb-2"><strong>Total Karyawan:</strong> {{ $nilaiKontrak->jumlah_karyawan_total }} orang</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Nilai Kontrak -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-success text-center">
                    <h1 class="display-4 mb-0">
                        <i class="fas fa-money-bill-wave"></i> 
                        Rp {{ number_format($nilaiKontrak->total_nilai_kontrak, 0, ',', '.') }}
                    </h1>
                    <p class="mb-0 mt-2">Total Nilai Kontrak per Bulan</p>
                </div>
            </div>
        </div>

        <!-- Breakdown Pengawas vs Pelaksana -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-primary shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user-tie"></i> Pengawas</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Jumlah</strong></td>
                                <td class="text-end">{{ $nilaiKontrak->jumlah_pengawas }} orang</td>
                            </tr>
                            <tr>
                                <td><strong>Total Nilai</strong></td>
                                <td class="text-end">Rp {{ number_format($nilaiKontrak->total_pengawas, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Rata-rata per Orang</strong></td>
                                <td class="text-end">
                                    Rp {{ $nilaiKontrak->jumlah_pengawas > 0 ? number_format($nilaiKontrak->total_pengawas / $nilaiKontrak->jumlah_pengawas, 0, ',', '.') : 0 }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-secondary shadow">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Pelaksana</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Jumlah</strong></td>
                                <td class="text-end">{{ $nilaiKontrak->jumlah_pelaksana }} orang</td>
                            </tr>
                            <tr>
                                <td><strong>Total Nilai</strong></td>
                                <td class="text-end">Rp {{ number_format($nilaiKontrak->total_pelaksana, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Rata-rata per Orang</strong></td>
                                <td class="text-end">
                                    Rp {{ $nilaiKontrak->jumlah_pelaksana > 0 ? number_format($nilaiKontrak->total_pelaksana / $nilaiKontrak->jumlah_pelaksana, 0, ',', '.') : 0 }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison with Previous Period -->
        @if($previousNilai)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Perbandingan dengan Periode Sebelumnya</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-2"><strong>Periode Sebelumnya:</strong> {{ \Carbon\Carbon::parse($previousNilai->periode)->format('F Y') }}</p>
                                <p class="mb-2"><strong>Nilai Sebelumnya:</strong> Rp {{ number_format($previousNilai->total_nilai_kontrak, 0, ',', '.') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>Periode Sekarang:</strong> {{ \Carbon\Carbon::parse($nilaiKontrak->periode)->format('F Y') }}</p>
                                <p class="mb-2"><strong>Nilai Sekarang:</strong> Rp {{ number_format($nilaiKontrak->total_nilai_kontrak, 0, ',', '.') }}</p>
                            </div>
                            <div class="col-md-4">
                                @php
                                    $delta = $nilaiKontrak->total_nilai_kontrak - $previousNilai->total_nilai_kontrak;
                                    $percentage = $previousNilai->total_nilai_kontrak > 0 ? ($delta / $previousNilai->total_nilai_kontrak) * 100 : 0;
                                @endphp
                                <p class="mb-2"><strong>Selisih:</strong> 
                                    <span class="{{ $delta >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $delta >= 0 ? '+' : '' }}Rp {{ number_format($delta, 0, ',', '.') }}
                                    </span>
                                </p>
                                <p class="mb-2"><strong>Persentase:</strong> 
                                    <span class="{{ $percentage >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $percentage >= 0 ? '+' : '' }}{{ number_format($percentage, 2) }}%
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Detail Karyawan -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Detail Breakdown per Karyawan</h5>
                    </div>
                    <div class="card-body">
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
                                        <td class="text-end"><strong>{{ number_format($karyawan['total'] ?? 0, 0, ',', '.') }}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 d-flex gap-2">
            <a href="{{ route('kalkulator.history', $nilaiKontrak->paket_id) }}" class="btn btn-info">
                <i class="fas fa-history"></i> Lihat History Perubahan
            </a>
            <a href="{{ route('paket.tagihan', $nilaiKontrak->paket_id) }}" class="btn btn-primary">
                <i class="fas fa-file-invoice"></i> Lihat Tagihan Lengkap
            </a>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.datatable').DataTable({
                processing: true,
                serverSide: false,
                pageLength: 25
            });
        });
    </script>
@endsection
