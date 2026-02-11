@extends('layouts.main')

@section('title', 'Sampah Data Lebaran')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4"><i class="fas fa-trash-alt me-2"></i>Sampah Data Lebaran</h1>
        
        <div class="mb-3">
             <a href="/lebaran" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
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

        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-danger text-white">
                <i class="fas fa-table me-1"></i>
                Daftar Sampah Lebaran
            </div>
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tahun</th>
                            <th>Tanggal Masehi</th>
                            <th>Dihapus Pada</th>
                            <th>Dihapus Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $item->tahun }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->deleted_at)->translatedFormat('d M Y H:i') }}</td>
                            <td>{{ $item->deleted_by ?? 'System' }}</td>
                            <td>
                                <a href="/restore-lebaran/{{ $item->id }}" class="btn btn-success btn-sm" title="Restore" onclick="return confirm('Apakah Anda yakin ingin memulihkan data ini?')"><i class="fas fa-trash-restore me-1"></i> Pulihkan</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
