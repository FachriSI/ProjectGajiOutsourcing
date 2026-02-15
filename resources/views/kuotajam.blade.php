@extends('layouts.main')
@section('title', 'Kuota Jam')
@section('content')
    <!-- Modern Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-hourglass-half me-2 text-primary"></i> Kuota Jam</h1>
                <p class="text-muted small mb-0 mt-1">Kelola kuota jam lembur karyawan</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/kuotajam/sampah" class="btn btn-outline-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Sampah
                    </a>
                @endif
                <a href="/gettambah-kuotajam" class="btn btn-primary shadow-sm">
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
                            <th>Karyawan</th>
                            <th class="text-center">Kuota (Jam)</th>
                            <th class="text-center">Tanggal Mulai</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-light text-primary me-2">
                                            <i class="fas fa-user-clock"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $item->nama }}</span>
                                            <small class="text-muted">ID: {{ $item->karyawan_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill px-3">{{ $item->kuota }} Jam</span>
                                </td>
                                <td class="text-center">
                                    <div class="small text-dark">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        @if($item->beg_date && $item->beg_date != '0000-00-00')
                                            {{ \Carbon\Carbon::parse($item->beg_date)->translatedFormat('d F Y') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="/getupdate-kuotajam/{{ $item->kuota_id }}"
                                        class="btn btn-sm btn-outline-secondary shadow-sm" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ url('delete-kuotajam', $item->kuota_id) }}"
                                        class="btn btn-sm btn-outline-danger shadow-sm btn-delete" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Delete"
                                        onclick="return confirm('Hapus data kuota jam ini?')">
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

        .bg-danger-light {
            background-color: rgba(231, 74, 59, 0.1);
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