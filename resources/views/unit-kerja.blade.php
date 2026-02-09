@extends('layouts.main')
@section('title', 'Unit Kerja')
@section('content')
    <!-- Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-5" style="border-color: #6f42c1 !important;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-sitemap me-2" style="color: #6f42c1;"></i> Unit Kerja
                </h1>
                <p class="text-muted small mb-0 mt-1">Kelola data unit kerja dan organisasi</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/unit-kerja/sampah" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Sampah
                    </a>
                @endif
                <a href="/gettambah-unit" class="btn btn-primary shadow-sm"
                    style="background-color: #6f42c1; border-color: #6f42c1;">
                    <i class="fas fa-plus me-1"></i> Tambah Data
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

    <!-- Data Table -->
    <div class="card shadow border-0 mb-4">

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
                                    <div class="btn-group" role="group">
                                        <a href="/getupdate-unit/{{ $item->unit_id }}" class="btn btn-sm btn-warning"
                                            data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ url('delete-unit', $item->unit_id) }}"
                                            class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip" title="Delete"
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