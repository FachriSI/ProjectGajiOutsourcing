@extends('layouts.main')

@section('title', 'Departemen')

@section('content')

    <div class="d-flex align-items-center mb-3 text-center gap-2">
    <a href="/gettambah-departemen" class="btn btn-primary">Tambah Data</a>
    @if($hasDeleted)
      <a href="/departemen/sampah" class="btn btn-secondary"><i class="fas fa-trash-restore"></i> Sampah</a>
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
                <th>Departemen</th>
                <th>Is SI</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>No.</th>
                <th>Departemen</th>
                <th>Is SI</th>
                <th class="text-center">Aksi</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}.</td>
                    <td>{{ $item->departemen }}</td>
                    <td>{{ $item->is_si == 1 ? 'Ya' : 'Tidak' }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="/getupdate-departemen/{{ $item->departemen_id }}" class="btn btn-sm btn-warning"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ url('delete-departemen', $item->departemen_id) }}" class="btn btn-sm btn-danger btn-delete"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection