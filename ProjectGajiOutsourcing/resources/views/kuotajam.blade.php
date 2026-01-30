@extends('layouts.main')
@section('title', 'Kuota Jam')
@section('content')
    <h3 class="mt-4">Kuota Jam</h3>
    <div class="d-flex align-items-center mb-3 text-center gap-2">
    <a href="/gettambah-kuotajam" class="btn btn-primary">Tambah Data</a>
    @if($hasDeleted)
      <a href="/kuotajam/sampah" class="btn btn-secondary"><i class="fas fa-trash-restore"></i> Sampah</a>
    @endif
  </div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <table class="table datatable">
        <thead>
            <tr>
                <th>No.</th>
                <th>Karyawan</th>
                <th>Kuota</th>
                <th>Tanggal Mulai</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}.</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->kuota }}</td>
                    <td>{{ $item->beg_date }}</td>
                    <td>
                        <a href="/getupdate-kuotajam/{{ $item->kuota_id }}" class="btn btn-sm btn-warning"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="{{ url('delete-kuotajam', $item->kuota_id) }}" class="btn btn-sm btn-danger btn-delete"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection