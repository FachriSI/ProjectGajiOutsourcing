@extends('layouts.main')
@section('title', 'Data Lebaran')
@section('content')
    <!-- Modern Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-calendar-day me-2 text-success"></i> Data Lebaran</h1>
                <p class="text-muted small mb-0 mt-1">Kelola data pemetaan kalender Hijriyah ke Masehi untuk THR</p>
            </div>
            <div class="d-flex gap-2">
                @if(isset($hasDeleted) && $hasDeleted)
                    <a href="{{ url('lebaran/trash') }}" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Data Sampah
                    </a>
                @endif
                <!-- Button removed as data is system-generated -->
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

    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered datatable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No.</th>
                            <th class="text-center">Tahun Masehi</th>
                            <th class="text-center">Tahun Hijriyah</th>
                            <th class="text-center">Tanggal Idul Fitri (1 Syawal)</th>
                            <th class="text-center">Keterangan</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center fw-bold">{{ $item->tahun }}</td>
                                <td class="text-center">{{ $item->tahun_hijriyah }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ url('getupdate-lebaran/' . $item->id) }}" class="btn btn-sm btn-warning"
                                            data-bs-toggle="tooltip" title="Edit Tanggal">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ url('delete-lebaran/' . $item->id) }}"
                                            class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
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
@endsection
