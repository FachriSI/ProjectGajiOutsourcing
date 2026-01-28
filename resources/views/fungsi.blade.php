@extends('layouts.main')

@section('title', 'Fungsi')

@section('content')

    <h3 class="mt-4">Fungsi</h3>
    <div class="d-flex align-items-center mb-3 text-center gap-2">
    <a href="/gettambah-fungsi" class="btn btn-primary">Tambah Data</a>
    @if($hasDeleted)
      <a href="/fungsi/sampah" class="btn btn-secondary"><i class="fas fa-trash-restore"></i> Sampah</a>
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
                <th>Fungsi</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>No.</th>
                <th>Fungsi</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}.</td>
                    <td>{{ $item->fungsi }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>
                        <a href="/getupdate-fungsi/{{ $item->kode_fungsi }}" class="btn btn-sm btn-warning"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ url('delete-fungsi', $item->kode_fungsi) }}" class="btn btn-sm btn-danger btn-delete"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection