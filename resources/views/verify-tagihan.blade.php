@extends('layouts.public')

@section('title', 'Verifikasi Dokumen Tagihan')

@section('content')
    <div class="verification-card">
        <!-- Header -->
        <div class="verification-header {{ $valid ? 'bg-success' : 'bg-danger' }} text-white">
            <h3 class="mb-0">
                <i class="fas {{ $valid ? 'fa-check-circle' : 'fa-times-circle' }} fa-2x mb-3"></i>
                <br>
                {{ $valid ? 'Dokumen Terverifikasi' : 'Verifikasi Gagal' }}
            </h3>
            
            @if($valid)
                <span class="verification-badge bg-light text-success">
                    <i class="fas fa-shield-alt"></i> Dokumen Valid & Asli
                </span>
            @endif
        </div>

        <!-- Body -->
        <div class="verification-body">
            @if($valid)
                <!-- Alert Success -->
                <div class="alert alert-success border-0 mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Dokumen tagihan ini telah terverifikasi dan dinyatakan valid.
                </div>

                <!-- Contract Information -->
                <h5 class="mb-3"><i class="fas fa-file-contract me-2 text-primary"></i>Informasi Kontrak</h5>
                
                <table class="table table-bordered info-table">
                    <tbody>
                        <tr>
                            <th>Nomor Dokumen</th>
                            <td><strong>{{ $tagihan->cetak_id }}</strong></td>
                        </tr>
                        <tr>
                            <th>Paket Pekerjaan</th>
                            <td>{{ $tagihan->paket->paket ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Unit Kerja</th>
                            <td>{{ $tagihan->paket->unitKerja->unit_kerja ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Vendor</th>
                            <td>{{ $vendor ?? '-' }}</td>
                        </tr>
                        @if($nilaiKontrak)
                        <tr>
                            <th>Periode Kontrak</th>
                            <td><strong>{{ \Carbon\Carbon::parse($nilaiKontrak->periode)->format('F Y') }}</strong></td>
                        </tr>
                        @endif
                        <tr>
                            <th>Jumlah Tenaga Kerja</th>
                            <td>
                                {{ $tagihan->jumlah_pengawas }} Pengawas, 
                                {{ $tagihan->jumlah_pelaksana }} Pelaksana
                            </td>
                        </tr>
                        <tr class="table-primary">
                            <th>Nilai Kontrak</th>
                            <td><strong class="text-primary fs-5">Rp {{ number_format($tagihan->total_boq, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th>Tanggal Dokumen</th>
                            <td>{{ \Carbon\Carbon::parse($tagihan->tanggal_cetak)->format('d F Y, H:i') }} WIB</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Download Section -->
                <div class="download-section">
                    <h5 class="mb-3">
                        <i class="fas fa-download me-2"></i>
                        Download Dokumen
                    </h5>
                    <p class="text-muted mb-3">
                        Klik tombol di bawah untuk mengunduh dokumen tagihan asli dalam format PDF
                    </p>
                    <a href="{{ route('tagihan.verify.download', $tagihan->token) }}" 
                       class="btn btn-primary btn-download">
                        <i class="fas fa-file-pdf me-2"></i>
                        Download PDF Original
                    </a>
                </div>

                <!-- Footer Info -->
                <div class="footer-text">
                    <i class="fas fa-lock me-1"></i>
                    Dokumen ini dilindungi dan telah diverifikasi secara digital
                </div>

            @else
                <!-- Error State -->
                <div class="alert alert-danger border-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>{{ $message }}</strong>
                </div>

                <div class="text-center py-4">
                    <i class="fas fa-times-circle text-danger" style="font-size: 64px; opacity: 0.3;"></i>
                    <p class="text-muted mt-4">
                        Token verifikasi yang Anda gunakan tidak valid atau sudah kedaluwarsa.<br>
                        Silakan hubungi pihak terkait untuk mendapatkan dokumen yang valid.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
