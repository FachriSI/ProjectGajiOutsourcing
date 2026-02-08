@extends('layouts.main')
@section('title', 'Harian/Shift')
@section('content')
    <!-- Modern Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-clock me-2 text-info"></i> Harian/Shift</h1>
                <p class="text-muted small mb-0 mt-1">Kelola data tunjangan harian dan shift</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/harianshift/sampah" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Data Sampah
                    </a>
                @endif
                <a href="/gettambah-harianshift" class="btn btn-primary shadow-sm">
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

    <div class="card shadow border-0 mb-4">

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered datatable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No.</th>
                            <th>Harian/Shift</th>
                            <th class="text-end">Tunjangan Shift</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-info-light text-info me-2">
                                            <i class="fas fa-business-time"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $item->harianshift }}</span>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($item->tunjangan_shift, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="/getupdate-harianshift/{{ $item->kode_harianshift }}"
                                            class="btn btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ url('delete-harianshift', $item->kode_harianshift) }}"
                                            class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Delete"
                                            onclick="return confirm('Hapus data harian/shift ini?')">
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

    <style>
        .icon-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .bg-info-light {
            background-color: rgba(54, 185, 204, 0.1);
        }
    </style>

    <script>
        $(document).ready(function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endsection