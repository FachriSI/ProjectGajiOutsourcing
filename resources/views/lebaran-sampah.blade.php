@extends('layouts.main')

@section('title', 'Sampah Data Lebaran')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-danger border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-trash-alt me-2 text-danger"></i> Sampah Data Lebaran</h1>
        <p class="text-muted small mb-0 mt-1">Daftar data lebaran yang telah dihapus (Archive)</p>
    </div>

    <!-- Alert Info -->
    <div class="alert alert-light border border-danger text-danger d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2 fa-lg"></i>
        <div>
            Data di bawah ini adalah data yang telah dihapus. Anda dapat memulihkannya kembali.
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-archive me-2"></i>Arsip Lebaran</h6>
            <a href="/lebaran" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="datatablesSimple" width="100%" cellspacing="0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th>No</th>
                            <th>Tahun</th>
                            <th>Tanggal Masehi</th>
                            <th>Dihapus Pada</th>
                            <th>Dihapus Oleh</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold text-dark">{{ $item->tahun }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}</td>
                                <td class="small">{{ \Carbon\Carbon::parse($item->deleted_at)->translatedFormat('d M Y H:i') }}
                                </td>
                                <td><span class="badge bg-secondary">{{ $item->deleted_by ?? 'System' }}</span></td>
                                <td class="text-center">
                                    <a href="/restore-lebaran/{{ $item->id }}" class="btn btn-outline-primary btn-sm"
                                        title="Restore"
                                        onclick="return confirm('Apakah Anda yakin ingin memulihkan data ini?')">
                                        <i class="fas fa-trash-restore me-1"></i> Restore
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection