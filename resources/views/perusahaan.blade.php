@extends('layouts.main')

@section('title', 'Vendor/Perusahaan')

@section('content')

    <!-- Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-building me-2 text-primary"></i> Data Vendor/Perusahaan
                </h1>
                <p class="text-muted small mb-0 mt-1">Manajemen data vendor dan perusahaan mitra.</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/perusahaan/sampah" class="btn btn-outline-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Sampah
                    </a>
                @endif

                <!-- Button Template & Import -->
                <button type="button" class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#importTemplateBaruModal" title="Template & Import Data">
                    <i class="fas fa-file-excel me-1"></i> Import / Template
                </button>

                <a href="/gettambah-perusahaan " class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Perusahaan
                </a>
            </div>
        </div>
    </div>

    <!-- Modal Import Excel Template Baru -->
    <div class="modal fade" id="importTemplateBaruModal" tabindex="-1" aria-labelledby="importTemplateBaruModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header card-gradient-blue text-dark border-bottom-0">
                    <h5 class="modal-title fw-bold" id="importTemplateBaruModalLabel"><i class="fas fa-file-excel me-2 text-primary"></i>Template
                        & Import Data Perusahaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Info Box -->
                    <div class="alert alert-light border border-primary text-primary mb-4">
                        <i class="fas fa-info-circle me-2"></i>Gunakan fitur ini untuk menambah atau mengupdate data
                        perusahaan secara massal.
                        <br><strong>Kolom Template:</strong> No, Nama, Alamat, CP, CP Jabatan, CP Telp, CP Email, ID Mesin,
                        Deleted, TKP, NPP
                    </div>

                    <!-- 1. Download Template -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold">1. Download Template Perusahaan:</span>
                        <a href="{{ route('template.perusahaan') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-1"></i> Download Template
                        </a>
                    </div>

                    <!-- 2. Upload File -->
                    <form action="{{ url('/import-template-baru') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <span class="fw-bold">2. Upload File Perusahaan (Excel):</span>
                        </div>
                        <div class="mb-3">
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-1"></i> Import Perusahaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow border-0 mb-4 border-top border-primary border-4">

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable" id="datatableSimple" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="5%">No.</th>
                            <th class="text-center">ID Perusahaan</th>
                            <th>Perusahaan</th>
                            <th>Alamat</th>
                            <th>CP</th>
                            <th>CP Jabatan</th>
                            <th>CP Telp</th>
                            <th>CP Email</th>
                            <th>ID Mesin</th>
                            <th>Deleted</th>
                            <th>TKP</th>
                            <th>NPP</th>
                            <th class="text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center"><span class="badge bg-secondary">{{ $item->perusahaan_id }}</span></td>
                                <td class="fw-bold">{{ $item->perusahaan }}</td>
                                <td>{{ $item->alamat }}</td>
                                <td>{{ $item->cp }}</td>
                                <td>{{ $item->cp_jab }}</td>
                                <td>{{ $item->cp_telp }}</td>
                                <td>{{ $item->cp_email }}</td>
                                <td>{{ $item->id_mesin }}</td>
                                <td>{{ $item->deleted_data }}</td>
                                <td>{{ $item->tkp }}</td>
                                <td>{{ $item->npp }}</td>
                                <td class="text-center">
                                    <a href="/getupdate-perusahaan/{{ $item->perusahaan_id }}"
                                        class="btn btn-sm btn-outline-secondary shadow-sm" data-bs-toggle="tooltip"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ url('delete-perusahaan', $item->perusahaan_id) }}"
                                        class="btn btn-sm btn-outline-danger shadow-sm btn-delete" data-bs-toggle="tooltip"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.datatable').each(function () {
                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        // Semua fitur default: search, sort, paging aktif
                        processing: true,
                        serverSide: false,
                        lengthChange: false,
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
                        },
                        initComplete: function () {
                            const table = this.api();
                            const container = $(table.table().container());
                            const infoDiv = container.find('.dataTables_info');

                            // Create the checkbox HTML with separator
                            const switchId = 'showAllSwitch_perusahaan';
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
                                    table.page.len(-1).draw();
                                } else {
                                    table.page.len(10).draw();
                                }
                            });
                        }
                    });
                }
            });

            // Initialize Bootstrap Tooltips for links
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection