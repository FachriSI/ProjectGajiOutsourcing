@extends('layouts.main')
@section('title', 'Unit Kerja')
@section('content')
    <!-- Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-sitemap me-2 text-primary"></i> Unit Kerja
                </h1>
                <p class="text-muted small mb-0 mt-1">Kelola data unit kerja dan organisasi</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/unit-kerja/sampah" class="btn btn-outline-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Sampah
                    </a>
                @endif

                <!-- Button Template & Import -->
                <button type="button" class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#importModal" title="Template & Import Data">
                    <i class="fas fa-file-excel me-1"></i> Import / Template
                </button>

                <a href="/gettambah-unit" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Unit Kerja
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Data Table -->
    <div class="card shadow border-0 mb-4 border-top border-primary border-4">

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered datatable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No.</th>
                            <th width="15%" class="text-center">ID Unit</th>
                            <th>Unit Kerja</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center font-monospace">{{ $item->unit_id }}</td>
                                <td class="fw-bold">{{ $item->unit_kerja }}</td>
                                <td class="text-center">
                                    <a href="/getupdate-unit/{{ $item->unit_id }}"
                                        class="btn btn-sm btn-outline-secondary shadow-sm" data-bs-toggle="tooltip"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ url('delete-unit', $item->unit_id) }}"
                                        class="btn btn-sm btn-outline-danger shadow-sm btn-delete" data-bs-toggle="tooltip"
                                        title="Delete" onclick="return confirm('Yakin ingin menghapus data ini?')">
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

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header card-gradient-blue text-dark border-bottom-0">
                    <h5 class="modal-title fw-bold" id="importModalLabel"><i class="fas fa-file-excel me-2 text-primary"></i>Template & Import
                        Data Unit Kerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Info Box -->
                    <div class="alert alert-light border border-primary text-primary mb-4">
                        <i class="fas fa-info-circle me-2"></i>Gunakan fitur ini untuk menambah atau mengupdate data unit
                        kerja secara massal.
                        <br><strong>Kolom Template:</strong> ID Unit, Nama Unit Kerja
                    </div>

                    <!-- 1. Download Template -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold">1. Download Template Unit Kerja:</span>
                        <a href="{{ route('template.unitkerja') }}" class="btn btn-outline-success">
                            <i class="fas fa-download me-1"></i> Download Template
                        </a>
                    </div>

                    <!-- 2. Upload File -->
                    <form action="/import-unitkerja" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <span class="fw-bold">2. Upload File Unit Kerja (Excel):</span>
                        </div>
                        <div class="mb-3">
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-1"></i> Import Unit Kerja
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endsection