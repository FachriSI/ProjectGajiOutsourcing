@extends('layouts.main')
@section('title', 'Harian/Shift')
@section('content')
    <h3 class="mt-4">Harian/Shift</h3>
    <a href="/gettambah-harianshift" class="btn btn-primary mb-3">Tambah Data</a>
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
                <th>Harian/Shift</th>
                <th>Tunjangan Shift</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}.</td>
                    <td>{{ $item->harianshift }}</td>
                    <td>Rp {{ number_format($item->tunjangan_shift, 0, ',', '.') }}</td>
                    <td>
                        <a href="/getupdate-harianshift/{{ $item->kode_harianshift }}" class="btn btn-sm btn-warning"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="{{ url('delete-harianshift', $item->kode_harianshift) }}"
                            class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Delete"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <script>
        $(document).ready(function () {
            $('.datatable').each(function () {
                if (!$.fn.DataTable.isDataTable(this)) { $(this).DataTable({ processing: true, serverSide: false }); }
            });
        });
    </script>
@endsection