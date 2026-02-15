@extends('layouts.main')

@section('title', 'Sampah Perusahaan')

@section('content')

  <!-- Modern Page Header -->
  <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-danger border-5">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-trash-alt me-2 text-danger"></i> Sampah Perusahaan</h1>
    <p class="text-muted small mb-0 mt-1">Daftar data perusahaan yang telah dihapus (Archive)</p>
  </div>

  <!-- Alert Info -->
  <div class="alert alert-light border border-danger text-danger d-flex align-items-center mb-4" role="alert">
    <i class="fas fa-exclamation-triangle me-2 fa-lg"></i>
    <div>
      Data di bawah ini adalah data yang telah dihapus. Anda dapat memulihkannya kembali.
    </div>
  </div>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-archive me-2"></i>Arsip Perusahaan</h6>
      <a href="/perusahaan" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-2"></i>Kembali
      </a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="datatableSimple" width="100%" cellspacing="0">
          <thead class="bg-light text-secondary">
            <tr>
              <th>No.</th>
              <th>Perusahaan</th>
              <th>Alamat</th>
              <th>Dihapus Oleh</th>
              <th>Waktu Hapus</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($data as $item)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="fw-bold text-dark">{{ $item->perusahaan }}</td>
                <td>{{ $item->alamat }}</td>
                <td><span class="badge bg-secondary">{{ $item->deleted_by }}</span></td>
                <td class="small">{{ $item->deleted_at }}</td>
                <td class="text-center">
                  <a href="{{ url('restore-perusahaan', $item->perusahaan_id) }}" class="btn btn-sm btn-outline-primary"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="Pulihkan Data">
                    <i class="fas fa-trash-restore"></i> Restore
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