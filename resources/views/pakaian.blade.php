@extends('layouts.main')
@section('title', 'Pakaian')
@section('content')
    <h3 class="mt-4">Pakaian</h3>
    <a href="/gettambah-pakaian" class="btn btn-primary mb-3">Tambah Data</a>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button"
                class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    <table class="table datatable">
        <thead>
            <tr>
                <th>No.</th>
                <th>Karyawan</th>
                <th>Nilai Jatah</th>
                <th>Ukuran Baju</th>
                <th>Ukuran Celana</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}.</td>
                    <td>{{ $item->nama }}</td>
                    <td>Rp {{ number_format($item->nilai_jatah, 0, ',', '.') }}</td>
                    <td>{{ $item->ukuran_baju }}</td>
                    <td>{{ $item->ukuran_celana }}</td>
                    <td>
                        <a href="/getupdate-pakaian/{{ $item->pakaian_id }}" class="btn btn-sm btn-warning"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="{{ url('delete-pakaian', $item->pakaian_id) }}" class="btn btn-sm btn-danger btn-delete"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection