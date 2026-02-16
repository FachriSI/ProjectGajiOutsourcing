<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Kontrak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .validation-card {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .card-header-custom {
            padding: 30px;
            text-align: center;
            color: white;
        }

        .card-header-custom.valid {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-header-custom.invalid {
            background: linear-gradient(135deg, #f857a6 0%, #ff5858 100%);
        }

        .status-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .card-body-custom {
            padding: 40px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #666;
        }

        .info-value {
            font-weight: 600;
            color: #333;
            text-align: right;
        }

        .alert-custom {
            margin-top: 30px;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid;
        }

        .alert-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }

        .alert-warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }

        .alert-danger {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }

        .badge-custom {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .scan-info {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 20px;
        }

        .scan-info i {
            font-size: 40px;
            color: #667eea;
            margin-bottom: 10px;
        }

        .breakdown-section {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .breakdown-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .value-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="validation-card">
            <!-- Header with Status -->
            <div class="card-header-custom {{ $result['valid'] ? 'valid' : 'invalid' }}">
                <div class="status-icon">
                    @if($result['valid'])
                        <i class="fas fa-check-circle"></i>
                    @else
                        <i class="fas fa-times-circle"></i>
                    @endif
                </div>
                <h1 style="font-size: 32px; margin-bottom: 10px;">
                    @if($result['valid'])
                        DOKUMEN VALID
                    @else
                        DOKUMEN TIDAK VALID
                    @endif
                </h1>
                <p style="font-size: 16px; opacity: 0.9;">
                    {{ $result['message'] }}
                </p>
            </div>

            <!-- Body with Contract Details -->
            <div class="card-body-custom">
                @if($result['valid'])
                    @php
                        $validation = $result['validation'];
                        $nilaiKontrak = $result['nilai_kontrak'];
                        $paket = $nilaiKontrak->paket;
                        $metadata = $validation->metadata['snapshot'] ?? [];
                    @endphp

                    <!-- Main Information -->
                    <div class="info-section">
                        @if(($validation->metadata['type'] ?? '') === 'THR')
                            {{-- THR Document --}}
                            @php $thrSnap = $validation->metadata['thr_snapshot'] ?? []; @endphp
                            <h3 style="margin-bottom: 20px; color: #333;">
                                <i class="fas fa-gift"></i> Informasi THR
                            </h3>

                            <div class="info-row">
                                <span class="info-label">Dokumen:</span>
                                <span class="info-value">THR Tahun {{ $validation->metadata['tahun'] ?? $nilaiKontrak->tahun }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Paket:</span>
                                <span class="info-value">{{ $thrSnap['paket'] ?? $paket->paket }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Unit Kerja:</span>
                                <span class="info-value">{{ $thrSnap['unit_kerja'] ?? 'N/A' }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Nama Perusahaan:</span>
                                <span class="info-value">{{ $thrSnap['nama_perusahaan'] ?? '-' }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Jumlah Pekerja:</span>
                                <span class="info-value">{{ $thrSnap['jumlah_pekerja'] ?? '-' }} orang</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Nilai THR:</span>
                                <span class="info-value">Rp {{ number_format($thrSnap['nilai_thr'] ?? 0, 0, ',', '.') }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Fee THR (5%):</span>
                                <span class="info-value">Rp {{ number_format($thrSnap['fee_thr'] ?? 0, 0, ',', '.') }}</span>
                            </div>

                            <div class="info-row" style="background: #f0f8ff; margin-top: 15px; padding: 20px; border-radius: 10px;">
                                <span class="info-label" style="font-size: 18px; color: #667eea;">
                                    <i class="fas fa-money-bill-wave"></i> Total Nilai THR:
                                </span>
                                <span class="info-value" style="font-size: 22px; color: #667eea;">
                                    Rp {{ number_format($thrSnap['total_nilai_thr'] ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        @else
                            {{-- Contract Document --}}
                            <h3 style="margin-bottom: 20px; color: #333;">
                                <i class="fas fa-file-contract"></i> Informasi Kontrak
                            </h3>

                            <div class="info-row">
                                <span class="info-label">Nomor Kontrak:</span>
                                <span class="info-value">CTR-{{ $nilaiKontrak->tahun }}-{{ str_pad($nilaiKontrak->bulan, 2, '0', STR_PAD_LEFT) }}-PKG{{ str_pad($nilaiKontrak->paket_id, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Periode:</span>
                                <span class="info-value">{{ $metadata['periode'] ?? $nilaiKontrak->periode }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Paket:</span>
                                <span class="info-value">{{ $metadata['paket_nama'] ?? $paket->paket }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Unit Kerja:</span>
                                <span class="info-value">{{ $metadata['unit_kerja'] ?? 'N/A' }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Karyawan Aktif:</span>
                                <span class="info-value">{{ $metadata['jumlah_karyawan_aktif'] ?? $nilaiKontrak->jumlah_karyawan_aktif }} orang</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Total Karyawan:</span>
                                <span class="info-value">{{ $metadata['jumlah_karyawan_total'] ?? $nilaiKontrak->jumlah_karyawan_total }} orang</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">UMP Sumbar:</span>
                                <span class="info-value">Rp {{ number_format($metadata['ump_sumbar'] ?? $nilaiKontrak->ump_sumbar, 0, ',', '.') }}</span>
                            </div>

                            <div class="info-row" style="background: #f0f8ff; margin-top: 15px; padding: 20px; border-radius: 10px;">
                                <span class="info-label" style="font-size: 18px; color: #667eea;">
                                    <i class="fas fa-money-bill-wave"></i> Total Nilai Kontrak:
                                </span>
                                <span class="info-value" style="font-size: 22px; color: #667eea;">
                                    Rp {{ number_format($metadata['total_nilai_kontrak'] ?? $nilaiKontrak->total_nilai_kontrak, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Breakdown Summary -->
                    @if(($validation->metadata['type'] ?? '') !== 'THR')
                    <div class="breakdown-section">
                        <div class="breakdown-title">
                            <i class="fas fa-chart-pie"></i> Rincian Nilai Kontrak
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="value-box">
                                    <div style="color: #666; font-size: 14px; margin-bottom: 5px;">
                                        <i class="fas fa-user-tie"></i> Pengawas ({{ $metadata['jumlah_pengawas'] ?? 0 }} orang)
                                    </div>
                                    <div style="font-size: 20px; font-weight: 700; color: #3498db;">
                                        Rp {{ number_format($metadata['total_pengawas'] ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="value-box">
                                    <div style="color: #666; font-size: 14px; margin-bottom: 5px;">
                                        <i class="fas fa-users"></i> Pelaksana ({{ $metadata['jumlah_pelaksana'] ?? 0 }} orang)
                                    </div>
                                    <div style="font-size: 20px; font-weight: 700; color: #27ae60;">
                                        Rp {{ number_format($metadata['total_pelaksana'] ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Validation Info -->
                    <div class="scan-info">
                        <i class="fas fa-shield-alt"></i>
                        <h5>Informasi Validasi</h5>
                        <div style="margin-top: 15px;">
                            <div class="info-row">
                                <span class="info-label">Tanggal Generate:</span>
                                <span class="info-value">{{ $validation->generated_at->format('d F Y, H:i') }} WIB</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Jumlah Validasi:</span>
                                <span class="info-value">{{ $validation->validation_count }} kali</span>
                            </div>
                            @if($validation->expires_at)
                            <div class="info-row">
                                <span class="info-label">Berlaku Hingga:</span>
                                <span class="info-value">{{ $validation->expires_at->format('d F Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status Alert -->
                    @if($result['checksum_match'])
                        <div class="alert-custom alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>Dokumen Asli & Tidak Dimodifikasi</strong><br>
                            Data kontrak sesuai dengan yang tercatat di sistem.
                        </div>
                    @else
                        <div class="alert-custom alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Perhatian!</strong><br>
                            Data kontrak telah berubah sejak PDF dibuat. Ini bisa terjadi jika ada perhitungan ulang atau update data.
                        </div>
                    @endif

                @else
                    <!-- Error Information -->
                    <div class="alert-custom alert-danger">
                        <i class="fas fa-times-circle"></i>
                        <strong>{{ $result['error'] }}</strong><br>
                        {{ $result['message'] }}
                    </div>

                    <div class="scan-info">
                        <i class="fas fa-info-circle"></i>
                        <h5>Bantuan</h5>
                        <p style="margin-top: 10px; color: #666;">
                            Pastikan Anda scan QR code yang benar dari dokumen kontrak resmi.
                            Hubungi administrator jika dokumen Anda seharusnya valid.
                        </p>
                    </div>
                @endif

                <!-- Back Button -->
                <div style="text-align: center; margin-top: 30px;">
                    <a href="/" class="btn btn-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 40px; border-radius: 30px; text-decoration: none;">
                        <i class="fas fa-home"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 30px; color: white;">
            <p style="opacity: 0.8;">
                <i class="fas fa-shield-alt"></i> Sistem Validasi Kontrak Outsourcing
            </p>
            <p style="opacity: 0.6; font-size: 14px;">
                © {{ date('Y') }} - Dokumen terverifikasi dengan QR Code
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
