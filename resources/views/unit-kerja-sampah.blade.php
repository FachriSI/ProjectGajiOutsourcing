@extends('layouts.main')

@section('title', 'Sampah Unit Kerja')

@section('content')

  <h3 class="mt-4">Sampah Unit Kerja</h3>
  <div class="alert alert-warning">
      <i class="fas fa-info-circle"></i> Data di bawah ini adalah data yang telah dihapus. Anda dapat memulihkannya kembali.
  </div>
  <div class="d-flex align-items-center mb-3 gap-2">
    <a href="/unit-kerja" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
  </div>

  <table class="table datatable" id="datatableSimple">
    <thead>
      <tr>
        <th>No.</th>
        <th>Unit Kerja</th>
        <th>Dihapus Oleh</th>
        <th>Waktu Hapus</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($data as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->unit_kerja }}</td>
          <td>{{ $item->deleted_by }}</td>
          <td>{{ $item->deleted_at }}</td>
          <td>
            <a href="{{ url('restore-unit', $item->unit_id) }}" class="btn btn-sm btn-success"
              data-bs-toggle="tooltip" data-bs-placement="top" title="Pulihkan">
              <i class="fas fa-trash-restore"></i> Pulihkan
            </a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <script>
    $(document).ready(function () {
      $('.datatable').each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
          $(this).DataTable();
        }
      });
    });
  </script>

@endsection
