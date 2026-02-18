@extends('layouts.main')

@section('title', 'Kontrak')

@section('content')
    <!-- Modern Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-contract me-2 text-primary"></i> Nilai Kontrak</h1>
                <p class="text-muted small mb-0 mt-1">Hitung dan kelola nilai kontrak per paket berdasarkan UMP dan
                    distribusi karyawan</p>
            </div>
            <button type="button" class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal"
                data-bs-target="#exportModal">
                <i class="fas fa-file-excel me-1"></i> Export Laporan
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-light border border-success text-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-light border border-danger text-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Daftar Semua Paket (Moved to Top) -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="card shadow border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-list me-2"></i> Daftar Paket Kontrak</h5>

                    <form action="{{ route('kalkulator.index') }}" method="GET" class="d-flex align-items-center">
                        <label for="filter_periode" class="text-muted me-2 small fw-bold">Filter Periode:</label>
                        <select name="filter_periode" id="filter_periode" class="form-select form-select-sm"
                            onchange="this.form.submit()" style="width: auto;">
                            <option value="">-- Tampilkan Terbaru --</option>
                            @foreach($availablePeriods as $p)
                                <option value="{{ $p }}" {{ $selectedPeriode == $p ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($p)->translatedFormat('F Y') }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover datatable-paket">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Paket</th>
                                    <th>Unit Kerja</th>
                                    <th>Kuota</th>
                                    <th>Nilai Kontrak Per Bulan</th>
                                    <th>Periode</th>
                                    <th>Aksi</th>
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
                                                <strong class="text-primary">
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
                                        @php
                                            // Use actual contract periode if available, otherwise use current periode
                                            $routePeriode = $nilaiKontrak ? \Carbon\Carbon::parse($nilaiKontrak->periode)->format('Y-m') : $currentPeriode;
                                        @endphp
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('kalkulator.show', ['paket_id' => $item->paket_id, 'periode' => $routePeriode]) }}"
                                                    class="btn btn-sm btn-outline-primary shadow-sm" data-bs-toggle="tooltip"
                                                    title="Hitung & Lihat Detail">
                                                    <i class="fas fa-calculator"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">TOTAL KONTRAK SEMUA PAKET PER BULAN:</th>
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

    <!-- Divider Section -->
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="text-gray-800 border-bottom pb-2">
                <i class="fas fa-calculator me-2"></i> Kalkulator Manual
            </h5>
            <p class="text-muted small">Gunakan formulir ini untuk simulasi perhitungan pada periode yang berbeda atau jika
                terdapat perubahan UMP.</p>
        </div>
    </div>

    <div class="row">
        <!-- Form Kalkulator -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom fw-bold text-primary py-2 small">
                    <i class="fas fa-calculator me-1"></i> Form Perhitungan
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('kalkulator.calculate') }}" method="POST" id="formKalkulator">
                        @csrf

                        <div class="mb-2">
                            <label for="paket_id" class="form-label fw-bold small text-uppercase text-muted"
                                style="font-size: 0.75rem;">Pilih Paket <span class="text-danger">*</span></label>
                            <select name="paket_id" id="paket_id" class="form-select form-select-sm select2" required>
                                <option value="">-- Cari Paket --</option>
                                @foreach ($pakets as $paket)
                                    <option value="{{ $paket->paket_id }}">
                                        {{ $paket->paket }} ({{ $paket->unitKerja->unit_kerja ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="periode" class="form-label fw-bold small text-uppercase text-muted"
                                style="font-size: 0.75rem;">Periode <span class="text-danger">*</span></label>
                            <input type="month" name="periode" id="periode" class="form-control form-control-sm"
                                value="{{ $currentPeriode }}" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm py-2 shadow-sm">
                                <i class="fas fa-calculator me-2"></i> Hitung
                            </button>
                        </div>
                    </form>

                    <div class="mt-3 pt-3 border-top text-center">
                        <form action="{{ route('kalkulator.recalculate') }}" method="POST"
                            onsubmit="return confirm('Hitung ulang semua paket? Proses ini bisa memakan waktu.')">
                            @csrf
                            <input type="hidden" name="periode" value="{{ $currentPeriode }}">
                            <button type="submit" class="btn btn-link btn-sm text-secondary text-decoration-none small p-0"
                                style="font-size: 0.8rem;">
                                <i class="fas fa-sync me-1"></i> Hitung Ulang Semua
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Result Display (AJAX) -->
        <div class="col-md-8">
            <!-- Loading Spinner -->
            <div class="card shadow-sm border-0 h-100" id="loadingSpinner" style="display: none;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center"
                    style="min-height: 200px;">
                    <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <p class="mt-2 small text-muted">Menghitung...</p>
                </div>
            </div>

            <!-- Empty State (Information) -->
            <div class="card shadow-sm border-0 h-100 bg-light" id="infoCard">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-3">
                    <div class="mb-2 text-muted">
                        <i class="fas fa-info-circle fa-3x"></i>
                    </div>
                    <h6 class="text-muted">Informasi Perhitungan</h6>
                    <p class="small text-muted mb-0" style="max-width: 400px;">
                        Pilih paket dan periode, lalu klik "Hitung".
                    </p>
                </div>
            </div>

            <!-- Result Card -->
            <div class="card shadow-sm border-primary h-100" id="resultCard" style="display: none;">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i> Hasil Perhitungan</h6>
                </div>
                <div class="card-body">
                    <div id="resultContent">
                        <!-- Total Nilai Kontrak -->
                        <div
                            class="alert alert-light border border-primary text-primary d-flex align-items-center shadow-sm">
                            <div class="display-4 me-3"><i class="fas fa-money-bill-wave"></i></div>
                            <div>
                                <small class="text-uppercase fw-bold opacity-75">Total Nilai Kontrak / Bulan</small>
                                <h2 class="mb-0 fw-bold">Rp <span id="totalNilaiKontrak">0</span></h2>
                            </div>
                        </div>

                        <!-- Breakdown Pengawas vs Pelaksana -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div
                                    class="p-3 border rounded bg-white h-100 position-relative overflow-hidden border-primary">
                                    <div class="position-absolute end-0 top-0 p-3 opacity-25">
                                        <i class="fas fa-user-tie fa-3x text-primary"></i>
                                    </div>
                                    <strong class="text-primary d-block mb-2">Pengawas</strong>
                                    <h5 class="mb-1">Rp <span id="totalPengawas">0</span></h5>
                                    <small class="text-muted"><span id="jumlahPengawas">0</span> Orang</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="p-3 border rounded bg-white h-100 position-relative overflow-hidden border-secondary">
                                    <div class="position-absolute end-0 top-0 p-3 opacity-25">
                                        <i class="fas fa-users fa-3x text-secondary"></i>
                                    </div>
                                    <strong class="text-secondary d-block mb-2">Pelaksana</strong>
                                    <h5 class="mb-1">Rp <span id="totalPelaksana">0</span></h5>
                                    <small class="text-muted"><span id="jumlahPelaksana">0</span> Orang</small>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Grid -->
                        <div class="row small mb-4">
                            <div class="col-6 mb-2">
                                <span class="text-muted">Karyawan Aktif:</span> <strong class="text-dark"
                                    id="karyawanAktif">0</strong>
                            </div>
                            <div class="col-6 mb-2">
                                <span class="text-muted">Total Terdaftar:</span> <strong class="text-dark"
                                    id="karyawanTotal">0</strong>
                            </div>
                            <div class="col-6 mb-2">
                                <span class="text-muted">Kuota Paket:</span> <strong class="text-dark"
                                    id="kuotaPaket">0</strong>
                            </div>
                            <div class="col-6 mb-2">
                                <span class="text-muted">UMP Acuan:</span> <strong class="text-dark">Rp <span
                                        id="umpSumbar">0</span></strong>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="#" id="btnLihatDetail" class="btn btn-outline-info shadow-sm">
                                <i class="fas fa-eye me-1"></i> Lihat Detail Rincian
                            </a>
                            <a href="#" id="btnLihatHistory" class="btn btn-outline-secondary shadow-sm">
                                <i class="fas fa-history me-1"></i> Riwayat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header card-gradient-blue text-dark border-bottom-0 py-3 px-4">
                    <h5 class="modal-title fw-bold" id="exportModalLabel">
                        <i class="fas fa-file-excel me-2 text-primary"></i>Export Laporan Kontrak
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('kalkulator.export') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        
                        <!-- Top Filters: Periode & Scope -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label for="exportPeriode" class="form-label fw-bold text-dark small mb-1">Periode</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0 text-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                    <input type="month" name="periode" id="exportPeriode"
                                        class="form-control border-start-0 ps-0 form-control-sm" value="{{ $currentPeriode }}" required
                                        style="font-weight: 600;">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-bold text-dark small mb-1">Lingkup Laporan</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="w-100 mb-0">
                                            <input type="radio" name="scope" value="all" class="card-input-element"
                                                checked onchange="togglePaketSelect(this.value)">
                                            <div class="card-input d-flex align-items-center justify-content-center py-2 px-3" style="border-radius: 8px; border: 1px solid #dee2e6;">
                                                <i class="fas fa-layer-group me-2 text-secondary"></i>
                                                <span class="fw-bold small">Semua Paket</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="w-100 mb-0">
                                            <input type="radio" name="scope" value="single" class="card-input-element"
                                                onchange="togglePaketSelect(this.value)">
                                            <div class="card-input d-flex align-items-center justify-content-center py-2 px-3" style="border-radius: 8px; border: 1px solid #dee2e6;">
                                                <i class="fas fa-box-open me-2 text-secondary"></i>
                                                <span class="fw-bold small">Per Paket</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paket Select (Hidden by default) -->
                        <div class="mb-3" id="paketSelectDiv" style="display: none;">
                            <label for="exportPaketId" class="form-label fw-bold small mb-1">Pilih Paket</label>
                            <select name="paket_id" id="exportPaketId" class="form-select form-select-sm">
                                <option value="">-- Pilih Paket --</option>
                                @foreach ($pakets as $paket)
                                    <option value="{{ $paket->paket_id }}">{{ $paket->paket }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Column Selection (Compact Grid) -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom fw-bold py-2 px-3 small">
                                <i class="fas fa-columns me-2 text-primary"></i>Konfigurasi Kolom
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-2">
                                    <!-- Kolom Utama & Statistik (Merged) -->
                                    <div class="col-md-12">
                                        <span class="checkbox-group-label small mb-1 pb-1" style="font-size: 0.75rem;">Informasi Umum</span>
                                        <div class="d-flex flex-wrap gap-2">
                                            <div class="form-check form-check-inline me-1">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="paket_nama" id="colPaket" checked>
                                                <label class="form-check-label small" for="colPaket">Nama Paket</label>
                                            </div>
                                            <div class="form-check form-check-inline me-1">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="unit_kerja" id="colUnit" checked>
                                                <label class="form-check-label small" for="colUnit">Unit Kerja</label>
                                            </div>
                                            <div class="form-check form-check-inline me-1">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="periode" id="colPeriode" checked>
                                                <label class="form-check-label small" for="colPeriode">Periode</label>
                                            </div>
                                            <div class="form-check form-check-inline me-1">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="total_nilai_kontrak" id="colTotal" checked>
                                                <label class="form-check-label small" for="colTotal">Total Nilai</label>
                                            </div>
                                            <div class="form-check form-check-inline me-1">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="ump_sumbar" id="colUmp">
                                                <label class="form-check-label small" for="colUmp">UMP</label>
                                            </div>
                                             <div class="form-check form-check-inline me-1">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="jumlah_karyawan_total" id="colKaryawan" checked>
                                                <label class="form-check-label small" for="colKaryawan">Total Karyawan</label>
                                            </div>
                                            <div class="form-check form-check-inline me-1">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="jumlah_pengawas" id="colJmlPengawas">
                                                <label class="form-check-label small" for="colJmlPengawas">Jml Pengawas</label>
                                            </div>
                                            <div class="form-check form-check-inline me-1">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="jumlah_pelaksana" id="colJmlPelaksana">
                                                <label class="form-check-label small" for="colJmlPelaksana">Jml Pelaksana</label>
                                            </div>
                                            <div class="form-check form-check-inline me-1">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="total_pengawas" id="colBiayaPengawas">
                                                <label class="form-check-label small" for="colBiayaPengawas">Biaya Pengawas</label>
                                            </div>
                                            <div class="form-check form-check-inline me-1">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="total_pelaksana" id="colBiayaPelaksana">
                                                <label class="form-check-label small" for="colBiayaPelaksana">Biaya Pelaksana</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Rincian Komponen (Compact Grid) -->
                                <div class="mt-2 p-2 bg-secondary bg-opacity-10 rounded-2">
                                    <span class="checkbox-group-label border-bottom-0 mb-1 small pb-0" style="font-size: 0.75rem;">Rincian Komponen</span>
                                    <div class="row g-1">
                                        <div class="col-md-3 col-6">
                                            <div class="form-check ps-1 mb-0">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="upah_pokok" id="colUpah">
                                                <label class="form-check-label small" style="font-size: 0.75rem;" for="colUpah">Upah Pokok</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="form-check ps-1 mb-0">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="tj_tetap" id="colTjTetap">
                                                <label class="form-check-label small" style="font-size: 0.75rem;" for="colTjTetap">Tj. Tetap</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="form-check ps-1 mb-0">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="tj_tidak_tetap" id="colTjTidakTetap">
                                                <label class="form-check-label small" style="font-size: 0.75rem;" for="colTjTidakTetap">Tj. Tdk Tetap</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="form-check ps-1 mb-0">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="tj_lokasi" id="colTjLokasi">
                                                <label class="form-check-label small" style="font-size: 0.75rem;" for="colTjLokasi">Tj. Lokasi</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 col-6">
                                            <div class="form-check ps-1 mb-0">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="bpjs_kesehatan" id="colBpjsKes">
                                                <label class="form-check-label small" style="font-size: 0.75rem;" for="colBpjsKes">BPJS Kes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="form-check ps-1 mb-0">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="bpjs_ketenagakerjaan" id="colBpjsTk">
                                                <label class="form-check-label small" style="font-size: 0.75rem;" for="colBpjsTk">BPJS TK</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="form-check ps-1 mb-0">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="kompensasi" id="colKompensasi">
                                                <label class="form-check-label small" style="font-size: 0.75rem;" for="colKompensasi">Kompensasi</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="form-check ps-1 mb-0">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="uang_jasa" id="colUangJasa">
                                                <label class="form-check-label small" style="font-size: 0.75rem;" for="colUangJasa">Uang Jasa</label>
                                            </div>
                                        </div>
                                         <div class="col-md-3 col-6">
                                            <div class="form-check ps-1 mb-0">
                                                <input class="form-check-input" type="checkbox" name="columns[]" value="lembur" id="colLembur">
                                                <label class="form-check-label small" style="font-size: 0.75rem;" for="colLembur">Lembur</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top-0 py-2 px-4">
                        <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">
                            <i class="fas fa-file-export me-1"></i>Export
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk AJAX -->
    <script>
        function togglePaketSelect(val) {
            if (val === 'single') {
                $('#paketSelectDiv').show();
                $('#exportPaketId').attr('required', true);
            } else {
                $('#paketSelectDiv').hide();
                $('#exportPaketId').attr('required', false);
            }
        }

        $(document).ready(function () {

            // Initialize DataTable for paket list
            $('.datatable-paket').DataTable({
                processing: true,
                serverSide: false,
                lengthChange: false,
                pageLength: 10,
                ordering: false, // Disable DataTables sorting to use backend sort order
                language: {
                    "decimal": "",
                    "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "loadingRecords": "Memuat...",
                    "processing": "Memproses...",
                    "search": "",
                    "searchPlaceholder": "Cari data...",
                    "zeroRecords": "Tidak ditemukan data yang sesuai",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    },
                    "aria": {
                        "sortAscending": ": aktifkan untuk mengurutkan kolom ke atas",
                        "sortDescending": ": aktifkan untuk mengurutkan kolom ke bawah"
                    }
                },
                initComplete: function () {
                    const tableApi = this.api();
                    const container = $(tableApi.table().container());
                    const infoDiv = container.find('.dataTables_info');

                    // Create the checkbox HTML with separator
                    const switchId = 'showAllSwitch_kalkulator';
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

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // AJAX Calculate
            $('#btnCalculateAjax').click(function () {
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
                $('#resultCard').hide();
                $('#infoCard').hide();
                $('#loadingSpinner').show();

                // AJAX Request
                $.ajax({
                    url: '/api/nilai-kontrak/calculate/' + paketId,
                    method: 'GET',
                    data: { periode: periode },
                    success: function (response) {
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
                            alert('Gagal menghitung: ' + response.message);
                            $('#resultCard').hide();
                        }
                    },
                    error: function (xhr) {
                        var errorMsg = 'Terjadi kesalahan saat menghitung';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                        alert(errorMsg);
                        $('#loadingSpinner').hide();
                        $('#infoCard').show();
                        $('#resultCard').hide();
                    }
                });
            });
        });
    </script>
@endsection