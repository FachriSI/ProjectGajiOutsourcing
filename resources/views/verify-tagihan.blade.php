@extends('layouts.main')

@section('title', 'Verifikasi Tagihan')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header text-center {{ $valid ? 'bg-success' : 'bg-danger' }} text-white">
                        <h4 class="mb-0">
                            <i class="fas {{ $valid ? 'fa-check-circle' : 'fa-times-circle' }} me-2"></i>
                            {{ $valid ? 'Tagihan Terverifikasi' : 'Verifikasi Gagal' }}
                        </h4>
                    </div>
                    <div class="card-body">
                        @if($valid)
                            <div class="alert alert-success">
                                <i class="fas fa-check me-2"></i>{{ $message }}
                            </div>

                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 40%;">ID Cetak</th>
                                        <td>{{ $tagihan->cetak_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Paket</th>
                                        <td>{{ $tagihan->paket->paket ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Vendor</th>
                                        <td>{{ $tagihan->vendor ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Pengawas</th>
                                        <td>{{ $tagihan->jumlah_pengawas }} orang</td>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Pelaksana</th>
                                        <td>{{ $tagihan->jumlah_pelaksana }} orang</td>
                                    </tr>
                                    <tr>
                                        <th>Total BOQ</th>
                                        <td><strong>Rp {{ number_format($tagihan->total_boq, 0, ',', '.') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Cetak</th>
                                        <td>{{ \Carbon\Carbon::parse($tagihan->tanggal_cetak)->format('d F Y H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="text-center mt-4">
                                <p class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Dokumen ini telah diverifikasi dan dinyatakan valid.
                                </p>
                            </div>
                        @else
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}
                            </div>

                            <div class="text-center">
                                <p class="text-muted">
                                    Token yang Anda gunakan tidak valid atau sudah kedaluwarsa.
                                    Silakan hubungi pihak terkait untuk konfirmasi.
                                </p>
                                <a href="/" class="btn btn-primary mt-3">
                                    <i class="fas fa-home me-1"></i> Kembali ke Beranda
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection