@extends('layouts.main')

@section('title', 'Unit Kerja')

@section('content')
    <h3 class="mt-4">Unit Kerja</h3>
    <div class="d-flex align-items-center mb-3 text-center gap-2">
    <a href="/gettambah-unit" class="btn btn-primary">Tambah Data</a>
    @if($hasDeleted)
      <a href="/unit-kerja/sampah" class="btn btn-secondary"><i class="fas fa-trash-restore"></i> Sampah</a>
    @endif
  </div>
    <table class="table datatable">
        <thead>
            <tr>
                <th>No.</th>
                <th>ID Unit</th>
                <th>Unit Kerja</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->unit_id }}</td>
                    <td>{{ $item->unit_kerja }}</td>

                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="/getupdate-unit/{{ $item->unit_id }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ url('delete-unit', $item->unit_id) }}" class="btn btn-sm btn-danger btn-delete"
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