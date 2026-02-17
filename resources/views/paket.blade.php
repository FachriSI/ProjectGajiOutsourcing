@extends('layouts.main')

@section('title', 'Data Paket')

@section('content')
    <!-- Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-box-open me-2 text-primary"></i> Paket</h1>
                <p class="text-muted small mb-0 mt-1">Dashboard dan Detail Paket Kontrak</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/paket/sampah" class="btn btn-outline-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Sampah
                    </a>
                @endif

                <!-- Button Template & Import -->
                <button type="button" class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#templatePaketModal" title="Template & Import Data">
                    <i class="fas fa-file-excel me-1"></i> Import / Template
                </button>

                <a href="/gettambah-paket" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Paket
                </a>
            </div>
        </div>
    </div>

    <!-- Modal Template & Import Paket -->
    <div class="modal fade" id="templatePaketModal" tabindex="-1" aria-labelledby="templatePaketModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="templatePaketModalLabel"><i class="fas fa-file-excel me-2"></i>Template &
                        Import Data Paket</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light border border-primary text-primary">
                        <i class="fas fa-info-circle"></i> Gunakan fitur ini untuk menambah atau mengupdate data paket
                        secara massal.
                        <br><strong>Kolom Template:</strong> Nama Paket, Kuota (Orang), Unit Kerja
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>1. Download Template Paket:</span>
                        <a href="{{ route('template.paket') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>
                    <hr>
                    <form action="{{ url('/import-paket') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="filePaket" class="form-label">2. Upload File Paket (Excel):</label>
                            <input type="file" name="file" id="filePaket" class="form-control" accept=".xlsx, .xls, .csv"
                                required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Import
                                Paket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 1: ANNUAL OVERVIEW -->
    <div class="row mb-4">
        <!-- Annual Stat: Total Kontrak / Tahun -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-light text-primary"
                            style="width: 50px; height: 50px;">
                            <i class="fas fa-file-contract fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Kontrak/Tahun</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_kontrak_tahunan_all, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Annual Stat: THR / Tahun -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-light text-primary"
                            style="width: 50px; height: 50px;">
                            <i class="fas fa-gift fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total THR/Tahun</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_thr_thn, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Annual Stat: MCU / Tahun -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-light text-primary"
                            style="width: 50px; height: 50px;">
                            <i class="fas fa-heartbeat fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total MCU/Tahun</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_mcu_all, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Annual Stat: Pakaian / Tahun -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-light text-primary"
                            style="width: 50px; height: 50px;">
                            <i class="fas fa-tshirt fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Pakaian/Tahun</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_pakaian_all, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: MONTHLY BREAKDOWN -->
    <div class="row mb-4">
        <!-- Monthly 1: Fix Cost -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-light text-primary"
                            style="width: 50px; height: 50px;">
                            <i class="fas fa-tags fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Fix Cost/Bln</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_jml_fix_cost, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Monthly 2: Variabel -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-light text-primary"
                            style="width: 50px; height: 50px;">
                            <i class="fas fa-chart-area fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Variabel/Bln</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_seluruh_variabel, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Monthly 3: Total Kontrak/Bln -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-light text-primary"
                            style="width: 50px; height: 50px;">
                            <i class="fas fa-file-contract fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Kontrak/Bln</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_kontrak_all, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Monthly 4: THR/Bln -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-light text-primary"
                            style="width: 50px; height: 50px;">
                            <i class="fas fa-hand-holding-usd fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total THR/Bln</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_thr_bln, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card-hover {
            transition: transform 0.2s;
            border-radius: 20px !important;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>

    <!-- Data Table -->
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <table class="table table-bordered table-hover display nowrap" id="datatablesSimple" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="5%">No.</th>
                        <th class="text-center">ID Paket</th>
                        <th>Nama Paket</th>
                        <th class="text-center">Kuota (Orang)</th>
                        <th>Unit Kerja</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $item->paket_id }}</span></td>
                            <td class="fw-bold">{{ $item->paket }}</td>
                            <td class="text-center">{{ $item->kuota_paket }}</td>
                            <td>{{ $item->unitKerja->unit_kerja ?? '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ url('/paket/' . $item->paket_id) }}"
                                        class="btn btn-sm btn-outline-info shadow-sm" data-bs-toggle="tooltip"
                                        title="Lihat Paket">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/getupdate-paket/{{ $item->paket_id }}" class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ url('delete-paket', $item->paket_id) }}"
                                        class="btn btn-sm btn-outline-danger btn-delete" data-bs-toggle="tooltip" title="Delete"
                                        onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var table = $('#datatablesSimple').DataTable({
                "lengthChange": false,
                "language": {
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
                },
                "autoWidth": false,
                initComplete: function () {
                    const tableApi = this.api();
                    const container = $(tableApi.table().container());
                    const infoDiv = container.find('.dataTables_info');

                    // Create the checkbox HTML with separator
                    const switchId = 'showAllSwitch_paket';
                    const checkboxHtml = `
                    <div class="d-inline-block me-2" style="vertical-align: middle;">
                        <div class="form-check d-inline-block me-2">
                            <input class="form-check-input btn-show-all-switch" type="checkbox" id="${switchId}" style="cursor: pointer;">
                            <label class="form-check-label small fw-bold text-muted" for="${switchId}" style="cursor: pointer;">Tampilkan
                                semua</label>
                        </div>
                        <span class="text-muted me-2">|</span>
                    </div>
                    `;

                    // Create a wrapper for same-line alignment without affecting siblings (pagination)
                    const flexWrapper = $('<div class="d-flex align-items-center flex-wrap mt-2"></div>');
                    infoDiv.before(flexWrapper);
                    flexWrapper.append(checkboxHtml);
                    flexWrapper.append(infoDiv);

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
        });
    </script>
@endsection