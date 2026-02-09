@extends('layouts.main')
@section('title', 'Pakaian')
@section('content')
    <!-- Modern Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tshirt me-2" style="color: #6f42c1;"></i> Pakaian</h1>
                <p class="text-muted small mb-0 mt-1">Kelola data jatah pakaian dinas karyawan</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/pakaian/sampah" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Data Sampah
                    </a>
                @endif
                <a href="/gettambah-pakaian" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Pakaian
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
                            <th>Karyawan</th>
                            <th class="text-end">Nilai Jatah</th>
                            <th class="text-center">Ukuran Baju</th>
                            <th class="text-center">Ukuran Celana</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-purple-light text-purple me-2">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $item->nama }}</span>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($item->nilai_jatah, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $item->ukuran_baju }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $item->ukuran_celana }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="/getupdate-pakaian/{{ $item->pakaian_id }}" class="btn btn-sm btn-warning"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ url('delete-pakaian', $item->pakaian_id) }}"
                                            class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Delete"
                                            onclick="return confirm('Hapus data pakaian ini?')">
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

        .text-purple {
            color: #6f42c1 !important;
        }

        .bg-purple-light {
            background-color: rgba(111, 66, 193, 0.1);
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