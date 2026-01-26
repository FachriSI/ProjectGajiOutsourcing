@extends('layouts.main')

@section('title', 'Paket')

@section('content')
    <h3 class="mt-4">Paket</h3>
    <div class="mb-3">
        <a href="/gettambah-paket" class="btn btn-primary">Tambah Paket</a>
    </div>
    <table class="table datatable">
        <thead>
            <tr>
                <th>No.</th>
                <th>Paket ID</th>
                <th>Kuota</th>
                <th>Unit Kerja</th>
                <th>Action</th>
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
    <script>
        $(document).ready(function () {
            $('.datatable').each(function () {
                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        // Semua fitur default: search, sort, paging aktif
                        processing: true,
                        serverSide: false
                    });
                }
            });
        });
    </script>
@endsection