@extends('layouts.main')

@section('title', 'Sampah Data Medical Checkup')

@section('content')

  <!-- Modern Page Header -->
  <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-danger border-5">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-trash-alt me-2 text-danger"></i> Sampah Medical Checkup</h1>
    <p class="text-muted small mb-0 mt-1">Daftar data medical checkup yang telah dihapus (Archive)</p>
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
      <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-archive me-2"></i>Arsip Medical Checkup</h6>
      <a href="{{ route('medical-checkup') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-2"></i>Kembali
      </a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover datatable" id="dataTable" width="100%" cellspacing="0">
          <thead class="bg-light text-secondary">
            <tr>
              <th width="5%" class="text-center">No.</th>
              <th class="text-center">Biaya Medical Checkup</th>
              <th class="text-center">Dihapus Oleh</th>
              <th class="text-center">Waktu Hapus</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($data as $item)
              <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center fw-bold text-primary">
                  Rp {{ number_format($item->biaya, 0, ',', '.') }}
                </td>
                <td class="text-center"><span class="badge bg-secondary">{{ $item->deleted_by ?? 'System' }}</span></td>
                <td class="text-center small">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>
                <td class="text-center">
                  <a href="{{ url('restore-medical-checkup/' . $item->id) }}" class="btn btn-sm btn-outline-primary"
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