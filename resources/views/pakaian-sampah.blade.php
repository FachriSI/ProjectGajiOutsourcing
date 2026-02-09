@extends('layouts.main')

@section('title', 'Sampah Pakaian')

@section('content')

  <h3 class="mt-4">Sampah Pakaian</h3>
  <div class="alert alert-warning">
    <i class="fas fa-info-circle"></i> Data di bawah ini adalah data yang telah dihapus. Anda dapat memulihkannya kembali.
  </div>
  <div class="d-flex align-items-center mb-3 gap-2">
    <a href="/pakaian" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
  </div>

  <table class="table datatable" id="datatableSimple">
    <thead>
      <tr>
        <th>No.</th>
        <th>Ukuran Baju</th>
        <th>Ukuran Celana</th>
        <th>Baju(cm)</th>
        <th>Celana(cm)</th>
        <th>Dihapus Oleh</th>
        <th>Waktu Hapus</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($data as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->ukuran_baju }}</td>
          <td>{{ $item->ukuran_celana }}</td>
          <td>{{ $item->baju_cm }}</td>
          <td>{{ $item->celana_cm }}</td>
          <td>{{ $item->deleted_by }}</td>
          <td>{{ $item->deleted_at }}</td>
          <td>
            <a href="{{ url('restore-pakaian', $item->pakaian_id) }}" class="btn btn-sm btn-success"
              data-bs-toggle="tooltip" data-bs-placement="top" title="Pulihkan">
              <i class="fas fa-trash-restore"></i> Pulihkan
            </a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

@endsection