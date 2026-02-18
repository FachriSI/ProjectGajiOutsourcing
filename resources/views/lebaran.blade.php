@extends('layouts.main')

@section('title', 'Data Lebaran')

@section('content')

    <!-- Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-calendar-alt me-2 text-primary"></i> Data Lebaran</h1>
                <p class="text-muted small mb-0 mt-1">Kelola data hari raya Idul Fitri untuk perhitungan THR.</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/lebaran/trash" class="btn btn-outline-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Sampah
                    </a>
                @endif

                <a href="/gettambah-lebaran" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Data
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0 mb-4 border-top border-primary border-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable" id="datatableSimple" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="8%" class="text-center">Kode</th>
                            <th>Tahun</th>
                            <th>Tanggal Masehi</th>
                            <th>Tahun Hijriyah</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center text-muted">{{ $item->id }}</td>
                                <td class="fw-bold text-center">{{ $item->tahun }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}</td>
                                <td>{{ $item->tahun_hijriyah ?? '-' }}</td>

                                <td class="text-center">
                                    <a href="/getupdate-lebaran/{{ $item->id }}"
                                        class="btn btn-outline-secondary btn-sm shadow-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/delete-lebaran/{{ $item->id }}" class="btn btn-outline-danger btn-sm shadow-sm"
                                        title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
@endsection