@extends('layouts.main')
@section('title', 'Masa Kerja')
@section('content')
    <!-- Modern Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-calendar-check me-2 text-primary"></i> Masa Kerja</h1>
                <p class="text-muted small mb-0 mt-1">Kelola data tunjangan berdasarkan masa kerja karyawan</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/masakerja/sampah" class="btn btn-outline-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Sampah
                    </a>
                @endif
                <a href="/gettambah-masakerja" class="btn btn-primary shadow-sm">
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
                            <th width="8%" class="text-center">Kode</th>
                            <th>Karyawan</th>
                            <th class="text-end">Tunjangan Masa Kerja</th>
                            <th class="text-center">Tanggal Mulai</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center text-muted">{{ $item->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-light text-primary me-2">
                                            <i class="fas fa-user-clock"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $item->nama }}</span>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-primary">
                                    Rp {{ number_format($item->tunjangan_masakerja, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($item->beg_date)->translatedFormat('d F Y') }}
                                </td>
                                <td class="text-center">
                                    <a href="/getupdate-masakerja/{{ $item->id }}"
                                        class="btn btn-sm btn-outline-secondary shadow-sm" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ url('delete-masakerja', $item->id) }}"
                                        class="btn btn-sm btn-outline-danger shadow-sm btn-delete" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Delete"
                                        onclick="return confirm('Hapus data masa kerja ini?')">
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

        .bg-success-light {
            background-color: rgba(28, 200, 138, 0.1);
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