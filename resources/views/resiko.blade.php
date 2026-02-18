@extends('layouts.main')
@section('title', 'Tunjangan Risiko')
@section('content')
    <!-- Modern Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-exclamation-triangle me-2 text-primary"></i> Tunjangan
                    Risiko</h1>
                <p class="text-muted small mb-0 mt-1">Kelola data tunjangan berdasarkan tingkat risiko pekerjaan</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/resiko/sampah" class="btn btn-outline-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Sampah
                    </a>
                @endif
                <a href="/gettambah-resiko" class="btn btn-primary shadow-sm">
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

    <div class="card shadow border-0 mb-4 border-top border-primary border-4">

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered datatable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No.</th>
                            <th width="10%" class="text-center">Kode</th>
                            <th>Tingkat Risiko</th>
                            <th class="text-end">Tunjangan Risiko</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center text-muted">{{ $item->kode_resiko }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-light text-primary me-2">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $item->resiko }}</span>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-primary">
                                    Rp {{ number_format($item->tunjangan_resiko, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <a href="/getupdate-resiko/{{ $item->kode_resiko }}"
                                        class="btn btn-sm btn-outline-secondary shadow-sm" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ url('delete-resiko', $item->kode_resiko) }}"
                                        class="btn btn-sm btn-outline-danger shadow-sm btn-delete" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Delete"
                                        onclick="return confirm('Hapus data risiko ini?')">
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

        .bg-warning-light {
            background-color: rgba(246, 194, 62, 0.1);
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