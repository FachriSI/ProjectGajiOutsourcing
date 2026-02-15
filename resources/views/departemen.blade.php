@extends('layouts.main')
@section('title', 'Departemen')
@section('content')
    <!-- Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-building me-2 text-primary"></i> Departemen</h1>
                <p class="text-muted small mb-0 mt-1">Kelola data departemen perusahaan</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/departemen/sampah" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Sampah
                    </a>
                @endif

                <!-- Button Template & Import -->
                <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal"
                    title="Template & Import Data">
                    <i class="fas fa-file-excel me-1"></i> Import / Template
                </button>

                <a href="/gettambah-departemen" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Departemen
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
    <div class="card shadow border-0 mb-4">

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered datatable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No.</th>
                            <th>Departemen</th>
                            <th class="text-center">Is SI</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $item->departemen }}</td>
                                <td class="text-center">
                                    @if($item->is_si == 1)
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i> Ya</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-times me-1"></i> Tidak</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="/getupdate-departemen/{{ $item->departemen_id }}"
                                        class="btn btn-sm btn-warning shadow-sm" data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ url('delete-departemen', $item->departemen_id) }}"
                                        class="btn btn-sm btn-danger shadow-sm btn-delete" data-bs-toggle="tooltip" title="Delete"
                                        onclick="return confirm('Yakin ingin menghapus data ini?')">
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
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="importModalLabel"><i class="fas fa-file-excel me-2"></i>Template & Import
                        Data Departemen</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Info Box -->
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>Gunakan fitur ini untuk menambah atau mengupdate data
                        departemen secara massal.
                        <br><strong>Kolom Template:</strong> Nama Departemen, Is SI (1=Ya, 0=Tidak)
                    </div>

                    <!-- 1. Download Template -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold">1. Download Template Departemen:</span>
                        <a href="{{ route('template.departemen') }}" class="btn btn-outline-success">
                            <i class="fas fa-download me-1"></i> Download Template
                        </a>
                    </div>

                    <!-- 2. Upload File -->
                    <form action="/import-departemen" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <span class="fw-bold">2. Upload File Departemen (Excel):</span>
                        </div>
                        <div class="mb-3">
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload me-1"></i> Import Departemen
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