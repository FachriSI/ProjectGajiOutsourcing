@extends('layouts.main')

@section('title', 'Kontrak')

@section('content')
    <h3 class="mt-4">Nilai Kontrak</h3>
    <p class="text-muted">Hitung dan kelola nilai kontrak per paket berdasarkan UMP dan distribusi karyawan</p>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Form Kalkulator -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-file-contract"></i> Form Perhitungan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('kalkulator.calculate') }}" method="POST" id="formKalkulator">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="paket_id" class="form-label">Pilih Paket <span class="text-danger">*</span></label>
                            <select name="paket_id" id="paket_id" class="form-select" required>
                                <option value="">-- Pilih Paket --</option>
                                @foreach ($pakets as $paket)
                                    <option value="{{ $paket->paket_id }}">
                                        {{ $paket->paket }} ({{ $paket->unitKerja->unit_kerja ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="periode" class="form-label">Periode <span class="text-danger">*</span></label>
                            <input type="month" name="periode" id="periode" class="form-control" 
                                   value="{{ $currentPeriode }}" required>
                            <small class="text-muted">Format: Bulan-Tahun</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calculator"></i> Hitung Nilai Kontrak
                            </button>
                            <button type="button" class="btn btn-success" id="btnCalculateAjax">
                                <i class="fas fa-bolt"></i> Hitung (AJAX)
                            </button>
                        </div>
                    </form>

                    <hr>

                    <form action="{{ route('kalkulator.recalculate') }}" method="POST" 
                          onsubmit="return confirm('Recalculate semua paket? Proses ini bisa memakan waktu.')">
                        @csrf
                        <input type="hidden" name="periode" value="{{ $currentPeriode }}">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-sync"></i> Recalculate Semua Paket
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi</h6>
                </div>
                <div class="card-body">
                    <small>
                        <ul class="mb-0">
                            <li>Nilai kontrak dihitung berdasarkan UMP tahun berjalan</li>
                            <li>Distribusi karyawan sesuai kuota paket</li>
                            <li>Breakdown: Pengawas dan Pelaksana</li>
                            <li>Data otomatis tersimpan untuk tracking</li>
                        </ul>
                    </small>
                </div>
            </div>
        </div>

        <!-- Result Display (AJAX) -->
        <div class="col-md-8">
            <div class="card" id="resultCard" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Hasil Perhitungan</h5>
                </div>
                <div class="card-body">
                    <div id="loadingSpinner" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Menghitung nilai kontrak...</p>
                    </div>

                    <div id="resultContent" style="display: none;">
                        <!-- Total Nilai Kontrak -->
                        <div class="alert alert-success text-center">
                            <h2 class="mb-0">
                                <i class="fas fa-money-bill-wave"></i> 
                                Rp <span id="totalNilaiKontrak">0</span>
                            </h2>
                            <small>Total Nilai Kontrak per Bulan</small>
                        </div>

                        <!-- Breakdown Pengawas vs Pelaksana -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <strong>Pengawas</strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1">
                                            <strong>Jumlah:</strong> <span id="jumlahPengawas">0</span> orang
                                        </p>
                                        <p class="mb-0">
                                            <strong>Total:</strong> Rp <span id="totalPengawas">0</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <strong>Pelaksana</strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1">
                                            <strong>Jumlah:</strong> <span id="jumlahPelaksana">0</span> orang
                                        </p>
                                        <p class="mb-0">
                                            <strong>Total:</strong> Rp <span id="totalPelaksana">0</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Info -->
                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Karyawan Aktif:</strong> <span id="karyawanAktif">0</span></p>
                                        <p class="mb-1"><strong>Total Karyawan:</strong> <span id="karyawanTotal">0</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Kuota Paket:</strong> <span id="kuotaPaket">0</span></p>
                                        <p class="mb-1"><strong>UMP Sumbar:</strong> Rp <span id="umpSumbar">0</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-3 d-flex gap-2">
                            <a href="#" id="btnLihatDetail" class="btn btn-info">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                            <a href="#" id="btnLihatHistory" class="btn btn-secondary">
                                <i class="fas fa-history"></i> Lihat History
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Placeholder saat belum ada hasil -->
            <div class="card" id="placeholderCard">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calculator fa-5x text-muted mb-3"></i>
                    <h5 class="text-muted">Pilih paket dan klik "Hitung Nilai Kontrak"</h5>
                    <p class="text-muted">Hasil perhitungan akan ditampilkan di sini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Semua Paket -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Paket Kontrak</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable-paket">
                            <thead class="table-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Paket</th>
                                    <th>Unit Kerja</th>
                                    <th>Kuota</th>
                                    <th>Total Nilai Kontrak</th>
                                    <th>Periode</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pakets as $item)
                                    @php
                                        $nilaiKontrak = $nilaiKontrakData[$item->paket_id] ?? null;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->paket }}</td>
                                        <td>{{ $item->unitKerja->unit_kerja ?? '-' }}</td>
                                        <td class="text-center">{{ $item->kuota_paket }} orang</td>
                                        <td class="text-end">
                                            @if($nilaiKontrak)
                                                <strong class="text-success">
                                                    Rp {{ number_format($nilaiKontrak->total_nilai_kontrak, 0, ',', '.') }}
                                                </strong>
                                            @else
                                                <span class="text-muted">Belum dihitung</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($nilaiKontrak)
                                                <small>{{ \Carbon\Carbon::parse($nilaiKontrak->periode)->format('M Y') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('kalkulator.show', ['paket_id' => $item->paket_id, 'periode' => $currentPeriode]) }}" 
                                                   class="btn btn-sm btn-primary" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Hitung & Lihat Detail">
                                                    <i class="fas fa-calculator"></i>
                                                </a>
                                                <a href="{{ route('paket.tagihan', $item->paket_id) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Lihat Tagihan BOQ">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('paket.pdf.download', $item->paket_id) }}" 
                                                   class="btn btn-sm btn-success" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Download PDF"
                                                   target="_blank">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <a href="{{ route('kalkulator.history', $item->paket_id) }}" 
                                                   class="btn btn-sm btn-secondary" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Lihat History">
                                                    <i class="fas fa-history"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">TOTAL SEMUA PAKET:</th>
                                    <th class="text-end">
                                        @php
                                            $grandTotal = 0;
                                            foreach ($nilaiKontrakData as $nk) {
                                                if ($nk) {
                                                    $grandTotal += $nk->total_nilai_kontrak;
                                                }
                                            }
                                        @endphp
                                        <strong class="text-primary">
                                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                        </strong>
                                    </th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk AJAX -->
    <script>
        $(document).ready(function() {
            // Initialize DataTable for paket list
            $('.datatable-paket').DataTable({
                processing: true,
                serverSide: false,
                pageLength: 10,
                order: [[1, 'asc']] // Sort by paket name
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // AJAX Calculate
            $('#btnCalculateAjax').click(function() {
                var paketId = $('#paket_id').val();
                var periode = $('#periode').val();

                if (!paketId) {
                    alert('Pilih paket terlebih dahulu!');
                    return;
                }

                if (!periode) {
                    alert('Pilih periode terlebih dahulu!');
                    return;
                }

                // Show loading
                $('#placeholderCard').hide();
                $('#resultCard').show();
                $('#resultContent').hide();
                $('#loadingSpinner').show();

                // AJAX Request
                $.ajax({
                    url: '/api/nilai-kontrak/calculate/' + paketId,
                    method: 'GET',
                    data: { periode: periode },
                    success: function(response) {
                        if (response.success) {
                            // Update UI dengan data
                            $('#totalNilaiKontrak').text(response.data.total_nilai_kontrak);
                            $('#totalPengawas').text(response.data.total_pengawas);
                            $('#totalPelaksana').text(response.data.total_pelaksana);
                            $('#jumlahPengawas').text(response.data.jumlah_pengawas);
                            $('#jumlahPelaksana').text(response.data.jumlah_pelaksana);
                            $('#karyawanAktif').text(response.data.jumlah_karyawan_aktif);
                            $('#karyawanTotal').text(response.data.jumlah_karyawan_total);
                            $('#kuotaPaket').text(response.data.kuota_paket);
                            $('#umpSumbar').text(response.data.ump_sumbar);

                            // Update button links
                            $('#btnLihatDetail').attr('href', '/kalkulator-kontrak/show?paket_id=' + paketId + '&periode=' + periode);
                            $('#btnLihatHistory').attr('href', '/kalkulator-kontrak/history/' + paketId);

                            // Show result
                            $('#loadingSpinner').hide();
                            $('#resultContent').show();
                        } else {
                            alert('Gagal menghitung: ' + response.message);
                            $('#resultCard').hide();
                            $('#placeholderCard').show();
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Terjadi kesalahan saat menghitung';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                        $('#resultCard').hide();
                        $('#placeholderCard').show();
                    }
                });
            });
        });
    </script>
@endsection
