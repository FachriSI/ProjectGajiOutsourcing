@extends('layouts.main')

@section('title', 'Sampah Karyawan')

@section('content')

  <h3 class="mt-4">Sampah Karyawan</h3>
  <div class="alert alert-warning">
      <i class="fas fa-info-circle"></i> Data di bawah ini adalah data yang telah dihapus. Anda dapat memulihkannya kembali.
  </div>
  <div class="d-flex align-items-center mb-3 gap-2">
    <a href="/karyawan" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
  </div>

  <table class="table datatable" id="datatableSimple">
    <thead>
      <tr>
        <th>No.</th>
        <th>OSIS ID</th>
        <th>KTP</th>
        <th>Nama</th>
        <th>Perusahaan</th>
        <th>Dihapus Oleh</th>
        <th>Waktu Hapus</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($data as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->osis_id }}</td>
          <td>{{ $item->ktp }}</td>
          <td>{{ $item->nama_tk }}</td>
          <td>{{ $item->perusahaan->perusahaan ?? '-' }}</td>
          <td>{{ $item->deleted_by }}</td>
          <td>{{ $item->deleted_at }}</td>
          <td>
            <a href="{{ url('restore-karyawan', $item->karyawan_id) }}" class="btn btn-sm btn-success"
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
