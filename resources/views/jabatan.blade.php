@extends('layouts.main')
@section('title', 'Tunjangan Jabatan')
@section('content')
    <h3 class="mt-4">Tunjangan Jabatan</h3>
    <a href="/gettambah-jabatan" class="btn btn-primary mb-3">Tambah Data</a>
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
                <th>Jabatan</th>
                <th>Tunjangan Jabatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}.</td>
                    <td>{{ $item->jabatan }}</td>
                    <td>Rp {{ number_format($item->tunjangan_jabatan, 0, ',', '.') }}</td>
                    <td>
                        <a href="/getupdate-jabatan/{{ $item->kode_jabatan }}" class="btn btn-sm btn-warning"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="{{ url('delete-jabatan', $item->kode_jabatan) }}" class="btn btn-sm btn-danger btn-delete"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection