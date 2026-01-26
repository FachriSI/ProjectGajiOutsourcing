@extends('layouts.main')
@section('title', 'Lokasi')
@section('content')
    <h3 class="mt-4">Lokasi</h3>
    <a href="/gettambah-lokasi" class="btn btn-primary mb-3">Tambah Data</a>
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
                <th>Lokasi</th>
                <th>Jenis</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}.</td>
                    <td>{{ $item->lokasi }}</td>
                    <td>{{ $item->jenis }}</td>
                    <td>
                        <a href="/getupdate-lokasi/{{ $item->kode_lokasi }}" class="btn btn-sm btn-warning"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="{{ url('delete-lokasi', $item->kode_lokasi) }}" class="btn btn-sm btn-danger btn-delete"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection