@extends('layouts.main')

@section('title', 'UMP')

@section('content')
    <!-- Modern Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-money-bill-wave me-2 text-primary"></i> Data UMP</h1>
                <p class="text-muted small mb-0 mt-1">Kelola data Upah Minimum Provinsi (UMP) per lokasi dan tahun</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/ump/sampah" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Data Sampah
                    </a>
                @endif
                <a href="/gettambah-ump" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tambah UMP
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
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
                            <th>Lokasi</th>
                            <th class="text-end">UMP</th>
                            <th class="text-center">Tahun</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-primary-light text-primary me-2">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $item->lokasi }}</span>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($item->ump, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark">{{ $item->tahun }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="/getupdate-ump/{{ $item->id }}" class="btn btn-sm btn-warning"
                                            data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ url('delete-ump', $item->id) }}" class="btn btn-sm btn-danger btn-delete"
                                            data-bs-toggle="tooltip" title="Hapus"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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

        .bg-primary-light {
            background-color: rgba(78, 115, 223, 0.1);
        }
    </style>

    <script>
        $(document).ready(function () {
            // Enable tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endsection