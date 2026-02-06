@extends('layouts.main')

@section('title', 'Vendor/Perusahaan')

@section('content')

    <!-- Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-building me-2 text-primary"></i> Data Vendor/Perusahaan</h1>
                <p class="text-muted small mb-0 mt-1">Manajemen data vendor dan perusahaan mitra.</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/perusahaan/sampah" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Sampah
                    </a>
                @endif

                <!-- Button Template & Import -->
                <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#importTemplateBaruModal" title="Template & Import Data">
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="importTemplateBaruModalLabel"><i class="fas fa-file-import me-2"></i>Import
                        Perusahaan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ url('/import-template-baru') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Gunakan fitur ini untuk mengimport data perusahaan baru.
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>1. Download Template Perusahaan:</span>
                            <a href="{{ route('template.perusahaan') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download"></i> Download Template
                            </a>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label for="file" class="form-label">2. Upload File Excel:</label>
                            <input type="file" name="file" id="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                            <div class="form-text">Pastikan format kolom: No, Nama, Alamat, CP, CPJAB, CPTelp, CPEmail, idMesin, Deleted, TKP, NPP</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning text-white">
                            <i class="fas fa-upload"></i> Import Data
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-dark text-white py-3">
            <h6 class="m-0 fw-bold"><i class="fas fa-table me-2"></i>Daftar Perusahaan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable" id="datatableSimple" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="5%">No.</th>
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
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="/getupdate-perusahaan/{{ $item->perusahaan_id }}" class="btn btn-sm btn-warning"
                                            data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ url('delete-perusahaan', $item->perusahaan_id) }}"
                                            class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip"
                                            title="Hapus">
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
    </div>

    <script>
        $(document).ready(function () {
            $('.datatable').each(function () {
                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        // Semua fitur default: search, sort, paging aktif
                        processing: true,
                        serverSide: false,
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