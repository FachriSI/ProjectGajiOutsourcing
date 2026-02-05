@extends('layouts.main')

@section('title', 'Kontrak')

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h3>Nilai Kontrak</h3>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="fas fa-file-excel"></i> Export Laporan
        </button>
    </div>
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
        <div class="col-md-5">
            <div class="card shadow">
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
                        </div>
                    </form>

                    <hr>

                    <form action="{{ route('kalkulator.recalculate') }}" method="POST"
                        onsubmit="return confirm('Hitung ulang semua paket? Proses ini bisa memakan waktu.')">
                        @csrf
                        <input type="hidden" name="periode" value="{{ $currentPeriode }}">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-sync"></i> Hitung Ulang Semua Paket
                            </button>
                        </div>
                    </form>
                </div>
            </div>


        </div>

        <!-- Result Display (AJAX) -->
        <div class="col-md-7">
            <div class="card shadow h-100" id="resultCard" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Hasil Perhitungan</h5>
                </div>
                <div class="card-body">
                    <div id="loadingSpinner" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Memuat...</span>
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
                                        <p class="mb-1"><strong>Karyawan Aktif:</strong> <span id="karyawanAktif">0</span>
                                        </p>
                                        <p class="mb-1"><strong>Total Karyawan:</strong> <span id="karyawanTotal">0</span>
                                        </p>
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
                                <i class="fas fa-history"></i> Lihat Riwayat
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Card (Always Visible) -->
            <div class="card shadow h-100">
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
                    <h5 class="modal-title" id="exportModalLabel"><i class="fas fa-file-excel"></i> Export Laporan Kontrak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('kalkulator.export') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Periode -->
                        <div class="mb-3">
                            <label for="exportPeriode" class="form-label">Periode</label>
                            <input type="month" name="periode" id="exportPeriode" class="form-control" value="{{ $currentPeriode }}" required>
                        </div>

                        <!-- Scope Selection -->
                        <div class="mb-3">
                            <label class="form-label">Lingkup Laporan</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="scope" id="scopeAll" value="all" checked onchange="togglePaketSelect(this.value)">
                                <label class="form-check-label" for="scopeAll">
                                    Semua Paket
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="scope" id="scopeSingle" value="single" onchange="togglePaketSelect(this.value)">
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
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="paket_nama" id="colPaket" checked>
                                        <label class="form-check-label" for="colPaket">Nama Paket</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="unit_kerja" id="colUnit" checked>
                                        <label class="form-check-label" for="colUnit">Unit Kerja</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="periode" id="colPeriode" checked>
                                        <label class="form-check-label" for="colPeriode">Periode</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="total_nilai_kontrak" id="colTotal" checked>
                                        <label class="form-check-label" for="colTotal">Total Nilai</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="ump_sumbar" id="colUmp">
                                        <label class="form-check-label" for="colUmp">UMP</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="jumlah_karyawan_total" id="colKaryawan" checked>
                                        <label class="form-check-label" for="colKaryawan">Total Karyawan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="jumlah_pengawas" id="colJmlPengawas">
                                        <label class="form-check-label" for="colJmlPengawas">Jml Pengawas</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="jumlah_pelaksana" id="colJmlPelaksana">
                                        <label class="form-check-label" for="colJmlPelaksana">Jml Pelaksana</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="total_pengawas" id="colBiayaPengawas">
                                        <label class="form-check-label" for="colBiayaPengawas">Biaya Pengawas</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="total_pelaksana" id="colBiayaPelaksana">
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
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="upah_pokok" id="colUpah">
                                        <label class="form-check-label small" for="colUpah">Upah Pokok</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="tj_tetap" id="colTjTetap">
                                        <label class="form-check-label small" for="colTjTetap">Tj. Tetap</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="tj_tidak_tetap" id="colTjTidakTetap">
                                        <label class="form-check-label small" for="colTjTidakTetap">Tj. Tidak Tetap</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="tj_lokasi" id="colTjLokasi">
                                        <label class="form-check-label small" for="colTjLokasi">Tj. Lokasi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="bpjs_kesehatan" id="colBpjsKes">
                                        <label class="form-check-label small" for="colBpjsKes">BPJS Kese.</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="bpjs_ketenagakerjaan" id="colBpjsTk">
                                        <label class="form-check-label small" for="colBpjsTk">BPJS TK</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="kompensasi" id="colKompen">
                                        <label class="form-check-label small" for="colKompen">Kompensasi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="uang_jasa" id="colUangJasa">
                                        <label class="form-check-label small" for="colUangJasa">Uang Jasa</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="lembur" id="colLembur">
                                        <label class="form-check-label small" for="colLembur">Lembur</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-file-export"></i> Export Excel</button>
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
                    "loadingRecords": "Sedang memuat...",
                    "processing": "Sedang memproses...",
                    "search": "Cari:",
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
                $('#resultCard').show();
                $('#resultContent').hide();
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
                        $('#resultCard').hide();
                    }
                });
            });
        });
    </script>
@endsection