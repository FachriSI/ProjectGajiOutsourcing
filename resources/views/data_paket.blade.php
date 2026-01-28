@extends('layouts.main')

@section('title', 'Paket')

@section('content')
    <h3 class="mt-4">Paket</h3>
    <div class="d-flex align-items-center mb-3 text-center gap-2">
        <a href="/gettambah-paket" class="btn btn-primary">Tambah Paket</a>
        @if($hasDeleted)
            <a href="/paket/sampah" class="btn btn-secondary"><i class="fas fa-trash-restore"></i> Sampah</a>
        @endif
    </div>
    <table class="table datatable">
        <thead>
            <tr>
                <th>No.</th>
                <th>Paket ID</th>
                <th>Kuota (Orang)</th>
                <th>Unit Kerja</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->paket }}</td>
                    <td>{{ $item->kuota_paket }}</td>
                    <td>{{ $item->unit_kerja }}</td>

                    <td>
                        <a href="/getupdate-paket/{{ $item->paket_id }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ url('delete-paket', $item->paket_id) }}" class="btn btn-sm btn-danger btn-delete"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection