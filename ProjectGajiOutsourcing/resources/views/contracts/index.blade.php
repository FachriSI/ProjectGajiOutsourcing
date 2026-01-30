@extends('layouts.main')

@section('title', 'Daftar Kontrak PDF & QR Code')

@section('content')
    <div class="container-fluid py-3">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                <i class="fas fa-qrcode me-2"></i>
                Daftar Kontrak PDF & QR Code
            </h4>
            <div>
                <a href="{{ route('kalkulator.show') }}?paket_id={{ $nilaiKontrak->paket_id }}&periode={{ $nilaiKontrak->periode }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('contract.pdf.generate', $nilaiKontrak->id) }}" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Generate PDF Baru
                </a>
            </div>
        </div>

        <!-- Contract Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Kontrak</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <p class="mb-2"><strong>Paket:</strong> {{ $nilaiKontrak->paket->paket }}</p>
                                <p class="mb-2"><strong>Periode:</strong> {{ \Carbon\Carbon::parse($nilaiKontrak->periode)->format('F Y') }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-2"><strong>Total Karyawan:</strong> {{ $nilaiKontrak->jumlah_karyawan_total }} orang</p>
                                <p class="mb-2"><strong>UMP Sumbar:</strong> Rp {{ number_format($nilaiKontrak->ump_sumbar, 0, ',', '.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Total Nilai Kontrak:</strong></p>
                                <h3 class="text-success mb-0">Rp {{ number_format($nilaiKontrak->total_nilai_kontrak, 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- List of Generated PDFs -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Daftar PDF yang Telah Di-generate</h5>
                    </div>
                    <div class="card-body">
                        @if($nilaiKontrak->validations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="50">No</th>
                                            <th>Validation Token</th>
                                            <th>Tanggal Generate</th>
                                            <th>Di-generate Oleh</th>
                                            <th>Jumlah Scan</th>
                                            <th>Terakhir Di-scan</th>
                                            <th>Status</th>
                                            <th width="250">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($nilaiKontrak->validations as $index => $validation)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <code>{{ substr($validation->validation_token, 0, 20) }}...</code>
                                            </td>
                                            <td>{{ $validation->generated_at->format('d M Y, H:i') }}</td>
                                            <td>{{ optional($validation->generator)->name ?? 'System' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $validation->validation_count }} kali</span>
                                            </td>
                                            <td>{{ $validation->validated_at ? $validation->validated_at->format('d M Y, H:i') : 'Belum pernah' }}</td>
                                            <td class="text-center">
                                                @if($validation->isExpired())
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle"></i> Expired
                                                    </span>
                                                @elseif(!$validation->is_valid)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-ban"></i> Tidak Valid
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle"></i> Aktif
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    @if($validation->pdf_path)
                                                        <a href="{{ route('contract.pdf.download', $validation->id) }}" 
                                                           class="btn btn-primary" 
                                                           title="Download PDF">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('contract.validate', $validation->validation_token) }}" 
                                                       class="btn btn-info" 
                                                       target="_blank"
                                                       title="Buka Halaman Validasi">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-secondary" 
                                                            onclick="showQrCode('{{ route('contract.validate', $validation->validation_token) }}')"
                                                            title="Lihat QR Code">
                                                        <i class="fas fa-qrcode"></i>
                                                    </button>
                                                    @if($validation->is_valid && !$validation->isExpired())
                                                        <form action="{{ route('contract.validation.invalidate', $validation->id) }}" 
                                                              method="POST" 
                                                              style="display: inline;"
                                                              onsubmit="return confirm('Yakin ingin menonaktifkan validasi ini?')">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-danger" 
                                                                    title="Nonaktifkan">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle fa-3x mb-3"></i>
                                <h5>Belum Ada PDF yang Di-generate</h5>
                                <p>Klik tombol "Generate PDF Baru" untuk membuat kontrak PDF dengan QR Code</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk QR Code -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-qrcode"></i> QR Code Validasi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="qrCodeContainer"></div>
                    <p class="mt-3 mb-0" id="qrCodeUrl"></p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <script>
        function showQrCode(url) {
            // Clear previous QR code
            document.getElementById('qrCodeContainer').innerHTML = '<canvas id="qrCanvas"></canvas>';
            document.getElementById('qrCodeUrl').innerHTML = '<small class="text-muted">' + url + '</small>';
            
            // Generate QR code
            QRCode.toCanvas(document.getElementById('qrCanvas'), url, {
                width: 300,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#ffffff'
                }
            }, function (error) {
                if (error) console.error(error);
            });
            
            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
            modal.show();
        }
    </script>
    @endpush
@endsection
