@extends('layouts.main')

@section('title', 'Vendor/Perusahaan')

@section('content')

    <h3 class="mt-4">Vendor/Perusahaan</h3>
    <div class="d-flex align-items-center mb-3 gap-2">
        <a href="/gettambah-perusahaan " class="btn btn-primary">Tambah Data</a>

        <!-- Button Template & Import -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importTemplateBaruModal" title="Template & Import Data">
            <i class="fas fa-file-excel fa-lg"></i>
        </button>
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
                            <a href="{{ asset('templates/templatePerusahaan_import.xlsx') }}" class="btn btn-outline-success btn-sm" download>
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
    <div class="table-responsive">
        <table class="table datatable table-striped table-bordered text-nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>No.</th>
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
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}.</td>
                        <td>{{ $item->perusahaan }}</td>
                        <td>{{ $item->alamat }}</td>
                        <td>{{ $item->cp }}</td>
                        <td>{{ $item->cp_jab }}</td>
                        <td>{{ $item->cp_telp }}</td>
                        <td>{{ $item->cp_email }}</td>
                        <td>{{ $item->id_mesin }}</td>
                        <td>{{ $item->deleted_data }}</td>
                        <td>{{ $item->tkp }}</td>
                        <td>{{ $item->npp }}</td>
                        <td>
                            <a href="/getupdate-perusahaan/{{ $item->perusahaan_id }}" class="btn btn-sm btn-warning"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ url('delete-perusahaan', $item->perusahaan_id) }}"
                                class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
            });
        </script>
@endsection