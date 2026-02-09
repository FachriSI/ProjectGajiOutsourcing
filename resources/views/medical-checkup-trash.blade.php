@extends('layouts.main')

@section('title', 'Sampah Data Medical Checkup')

@section('content')

  <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4">
      <h3 class="h3 mb-2 text-gray-800"><i class="fas fa-trash-alt me-2 text-secondary"></i> Sampah Data Medical Checkup</h3>
      <div class="alert alert-warning mb-0">
        <i class="fas fa-info-circle"></i> Data di bawah ini adalah data yang telah dihapus. Anda dapat memulihkannya kembali.
      </div>
  </div>

  <div class="d-flex align-items-center mb-3 gap-2">
    <a href="{{ route('medical-checkup') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
  </div>

  <div class="card shadow border-0 mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table datatable table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead>
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
                <td class="text-center fw-bold text-success">
                    Rp {{ number_format($item->biaya, 0, ',', '.') }}
                </td>
                <td class="text-center">{{ $item->deleted_by ?? 'System' }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>
                <td class="text-center">
                    {{-- Use the route defined in web.php, assuming standard naming or check grep result --}}
                  <a href="{{ url('restore-medical-checkup/' . $item->id) }}" class="btn btn-sm btn-success" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="Pulihkan">
                    <i class="fas fa-trash-restore"></i> Pulihkan
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
