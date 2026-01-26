@extends('layouts.main')

@section('title', 'Unit Kerja')

@section('content')
    <h3 class="mt-4">Unit Kerja</h3>
    <div class="mb-3">
        <a href="/gettambah-unit" class="btn btn-primary">Tambah Unit</a>
        <!-- <a href="/gettambah-bidang" class="btn btn-success">Tambah Bidang</a>
                <a href="/gettambah-area" class="btn btn-info">Tambah Area</a> -->
    </div>
    <table class="table datatable">
        <thead>
            <tr>
                <th>No.</th>
                <th>ID Unit</th>
                <th>Unit Kerja</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->unit_id }}</td>
                    <td>{{ $item->unit_kerja }}</td>

                    <td>
                        <a href="/getupdate-unit/{{ $item->unit_id }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ url('delete-unit', $item->unit_id) }}" class="btn btn-sm btn-danger btn-delete"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection