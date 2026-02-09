@extends('layouts.main')

@section('title', 'Kontrak')

@section('content')
    <!-- Modern Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-contract me-2 text-primary"></i> Nilai Kontrak</h1>
                <p class="text-muted small mb-0 mt-1">Hitung dan kelola nilai kontrak per paket berdasarkan UMP dan
                    distribusi karyawan</p>
            </div>
            <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-file-excel me-1"></i> Export Laporan
            </button>
        </div>
    </div>

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
        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom fw-bold text-primary py-3">
                    <i class="fas fa-calculator me-1"></i> Form Perhitungan
                </div>
                <div class="card-body">
                    <form action="{{ route('kalkulator.calculate') }}" method="POST" id="formKalkulator">
                        @csrf

                        <div class="mb-3">
                            <label for="paket_id" class="form-label fw-bold small text-uppercase text-muted">Pilih Paket
                                <span class="text-danger">*</span></label>
                            <select name="paket_id" id="paket_id" class="form-select select2" required>
                                <option value="">-- Cari Paket --</option>
                                @foreach ($pakets as $paket)
                                    <option value="{{ $paket->paket_id }}">
                                        {{ $paket->paket }} ({{ $paket->unitKerja->unit_kerja ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="periode" class="form-label fw-bold small text-uppercase text-muted">Periode <span
                                    class="text-danger">*</span></label>
                            <input type="month" name="periode" id="periode" class="form-control"
                                value="{{ $currentPeriode }}" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 shadow-sm">
                                <i class="fas fa-calculator me-2"></i> Hitung Nilai Kontrak
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 pt-4 border-top text-center">
                        <small class="text-muted d-block mb-3">Opsi Lanjutan</small>
                        <form action="{{ route('kalkulator.recalculate') }}" method="POST"
                            onsubmit="return confirm('Hitung ulang semua paket? Proses ini bisa memakan waktu.')">
                            @csrf
                            <input type="hidden" name="periode" value="{{ $currentPeriode }}">
                            <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                <i class="fas fa-sync me-1"></i> Hitung Ulang Semua Paket
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Result Display (AJAX) -->
        <div class="col-md-7">
            <!-- Loading Spinner -->
            <div class="card shadow-sm border-0 h-100" id="loadingSpinner" style="display: none;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center"
                    style="min-height: 300px;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <p class="mt-3 text-muted">Sedang menghitung nilai kontrak...</p>
                </div>
            </div>

            <!-- Empty State (Information) -->
            <div class="card shadow-sm border-0 h-100 bg-light" id="infoCard">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-5">
                    <div class="mb-3 text-muted">
                        <i class="fas fa-info-circle fa-4x"></i>
                    </div>
                    <h5 class="text-muted">Informasi Perhitungan</h5>
                    <p class="small text-muted mb-0" style="max-width: 400px;">
                        Pilih paket dan periode di sebelah kiri, lalu klik "Hitung" untuk melihat rincian nilai kontrak.
                        Data dihitung berdasarkan UMP tahun berjalan dan distribusi karyawan.
                    </p>
                </div>
            </div>

            <!-- Result Card -->
            <div class="card shadow-sm border-primary h-100" id="resultCard" style="display: none;">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> Hasil Perhitungan</h5>
                </div>
                <div class="card-body">
                    <div id="resultContent">
                        <!-- Total Nilai Kontrak -->
                        <div class="alert alert-success d-flex align-items-center shadow-sm border-0">
                            <div class="display-4 me-3"><i class="fas fa-money-bill-wave"></i></div>
                            <div>
                                <small class="text-uppercase fw-bold opacity-75">Total Nilai Kontrak / Bulan</small>
                                <h2 class="mb-0 fw-bold">Rp <span id="totalNilaiKontrak">0</span></h2>
                            </div>
                        </div>

                        <!-- Breakdown Pengawas vs Pelaksana -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light h-100 position-relative overflow-hidden">
                                    <div class="position-absolute end-0 top-0 p-3 opacity-25">
                                        <i class="fas fa-user-tie fa-3x text-primary"></i>
                                    </div>
                                    <strong class="text-primary d-block mb-2">Pengawas</strong>
                                    <h5 class="mb-1">Rp <span id="totalPengawas">0</span></h5>
                                    <small class="text-muted"><span id="jumlahPengawas">0</span> Orang</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light h-100 position-relative overflow-hidden">
                                    <div class="position-absolute end-0 top-0 p-3 opacity-25">
                                        <i class="fas fa-users fa-3x text-success"></i>
                                    </div>
                                    <strong class="text-success d-block mb-2">Pelaksana</strong>
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
                            <a href="#" id="btnLihatDetail" class="btn btn-info shadow-sm text-white">
                                <i class="fas fa-eye me-1"></i> Lihat Detail Rincian
                            </a>
                            <a href="#" id="btnLihatHistory" class="btn btn-secondary shadow-sm">
                                <i class="fas fa-history me-1"></i> Riwayat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Semua Paket -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
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
                        <table class="table table-bordered table-striped datatable-paket">
                            <thead class="table-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Paket</th>
                                    <th>Unit Kerja</th>
                                    <th>Kuota</th>
                                    <th>Total Nilai Kontrak</th>
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
                                        @php
                                            // Use actual contract periode if available, otherwise use current periode
                                            $routePeriode = $nilaiKontrak ? \Carbon\Carbon::parse($nilaiKontrak->periode)->format('Y-m') : $currentPeriode;
                                        @endphp
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('kalkulator.show', ['paket_id' => $item->paket_id, 'periode' => $routePeriode]) }}"
                                                    class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                    title="Hitung & Lihat Detail">
                                                    <i class="fas fa-calculator"></i>
                                                </a>
                                                @if($nilaiKontrak)
                                                    <a href="{{ route('paket.tagihan', $item->paket_id) }}"
                                                        class="btn btn-sm btn-info" data-bs-toggle="tooltip"
                                                        title="Lihat Tagihan BOQ">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('paket.pdf.download', $item->paket_id) }}"
                                                        class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Unduh PDF"
                                                        target="_blank">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="{{ route('kalkulator.history', $item->paket_id) }}"
                                                        class="btn btn-sm btn-secondary" data-bs-toggle="tooltip"
                                                        title="Lihat Riwayat">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-sm btn-secondary" disabled data-bs-toggle="tooltip"
                                                        title="Hitung kontrak terlebih dahulu">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary" disabled data-bs-toggle="tooltip"
                                                        title="Hitung kontrak terlebih dahulu">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary" disabled data-bs-toggle="tooltip"
                                                        title="Hitung kontrak terlebih dahulu">
                                                        <i class="fas fa-history"></i>
                                                    </button>
                                                @endif
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

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="exportModalLabel"><i class="fas fa-file-excel"></i> Export Laporan Kontrak
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('kalkulator.export') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Periode -->
                        <div class="mb-3">
                            <label for="exportPeriode" class="form-label">Periode</label>
                            <input type="month" name="periode" id="exportPeriode" class="form-control"
                                value="{{ $currentPeriode }}" required>
                        </div>

                        <!-- Scope Selection -->
                        <div class="mb-3">
                            <label class="form-label">Lingkup Laporan</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="scope" id="scopeAll" value="all" checked
                                    onchange="togglePaketSelect(this.value)">
                                <label class="form-check-label" for="scopeAll">
                                    Semua Paket
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="scope" id="scopeSingle" value="single"
                                    onchange="togglePaketSelect(this.value)">
                                <label class="form-check-label" for="scopeSingle">
                                    Per Paket
                                </label>
                            </div>
                        </div>

                        <!-- Paket Select (Hidden by default) -->
                        <div class="mb-3" id="paketSelectDiv" style="display: none;">
                            <label for="exportPaketId" class="form-label">Pilih Paket</label>
                            <select name="paket_id" id="exportPaketId" class="form-select">
                                <option value="">-- Pilih Paket --</option>
                                @foreach ($pakets as $paket)
                                    <option value="{{ $paket->paket_id }}">{{ $paket->paket }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr>

                        <!-- Column Selection -->
                        <div class="mb-3">
                            <label class="form-label">Pilih Kolom</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="paket_nama"
                                            id="colPaket" checked>
                                        <label class="form-check-label" for="colPaket">Nama Paket</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="unit_kerja"
                                            id="colUnit" checked>
                                        <label class="form-check-label" for="colUnit">Unit Kerja</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="periode"
                                            id="colPeriode" checked>
                                        <label class="form-check-label" for="colPeriode">Periode</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]"
                                            value="total_nilai_kontrak" id="colTotal" checked>
                                        <label class="form-check-label" for="colTotal">Total Nilai</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="ump_sumbar"
                                            id="colUmp">
                                        <label class="form-check-label" for="colUmp">UMP</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]"
                                            value="jumlah_karyawan_total" id="colKaryawan" checked>
                                        <label class="form-check-label" for="colKaryawan">Total Karyawan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]"
                                            value="jumlah_pengawas" id="colJmlPengawas">
                                        <label class="form-check-label" for="colJmlPengawas">Jml Pengawas</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]"
                                            value="jumlah_pelaksana" id="colJmlPelaksana">
                                        <label class="form-check-label" for="colJmlPelaksana">Jml Pelaksana</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]"
                                            value="total_pengawas" id="colBiayaPengawas">
                                        <label class="form-check-label" for="colBiayaPengawas">Biaya Pengawas</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]"
                                            value="total_pelaksana" id="colBiayaPelaksana">
                                        <label class="form-check-label" for="colBiayaPelaksana">Biaya Pelaksana</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">Rincian Komponen (Bobot)</label>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="upah_pokok"
                                            id="colUpah">
                                        <label class="form-check-label small" for="colUpah">Upah Pokok</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="tj_tetap"
                                            id="colTjTetap">
                                        <label class="form-check-label small" for="colTjTetap">Tj. Tetap</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]"
                                            value="tj_tidak_tetap" id="colTjTidakTetap">
                                        <label class="form-check-label small" for="colTjTidakTetap">Tj. Tidak Tetap</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="tj_lokasi"
                                            id="colTjLokasi">
                                        <label class="form-check-label small" for="colTjLokasi">Tj. Lokasi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]"
                                            value="bpjs_kesehatan" id="colBpjsKes">
                                        <label class="form-check-label small" for="colBpjsKes">BPJS Kese.</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]"
                                            value="bpjs_ketenagakerjaan" id="colBpjsTk">
                                        <label class="form-check-label small" for="colBpjsTk">BPJS TK</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="kompensasi"
                                            id="colKompen">
                                        <label class="form-check-label small" for="colKompen">Kompensasi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="uang_jasa"
                                            id="colUangJasa">
                                        <label class="form-check-label small" for="colUangJasa">Uang Jasa</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="lembur"
                                            id="colLembur">
                                        <label class="form-check-label small" for="colLembur">Lembur</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-file-export"></i> Export
                            Excel</button>
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
                order: [[1, 'asc']], // Sort by paket name
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