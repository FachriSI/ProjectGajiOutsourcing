@extends('layouts.main')

@section('title', 'Sampah Kuota Jam')

@section('content')

  <h3 class="mt-4">Sampah Kuota Jam</h3>
  <div class="alert alert-warning">
      <i class="fas fa-info-circle"></i> Data di bawah ini adalah data yang telah dihapus. Anda dapat memulihkannya kembali.
  </div>
  <div class="d-flex align-items-center mb-3 gap-2">
    <a href="/kuotajam" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
  </div>

  <table class="table datatable">
    <thead>
      <tr>
        <th>No.</th>
        <th>Karyawan</th>
        <th>Kuota</th>
        <th>Tanggal Mulai</th>
        <th>Dihapus Oleh</th>
        <th>Waktu Hapus</th>
        <th class="text-center">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($data as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->nama }}</td>
          <td>{{ $item->kuota }}</td>
           <td>{{ $item->beg_date }}</td>
          <td>{{ $item->deleted_by }}</td>
          <td>{{ $item->deleted_at }}</td>
          <td class="text-center">
            <a href="{{ url('restore-kuotajam', $item->kuota_id) }}" class="btn btn-sm btn-success"
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
