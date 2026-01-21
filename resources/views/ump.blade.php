@extends('layouts.main')

@section('title', 'UMP')

@section('content')
    <h3 class="mt-4">UMP</h3>
    <div class="mb-3">
        <a href="/gettambah-ump-tahunan" class="btn btn-primary">Tambah UMP tahunan</a>
        <!-- <a href="/gettambah-ump" class="btn btn-primary">Tambah UMP </a> -->

    </div>
    <table class="table datatable">
        <thead>
            <tr>
                <th>No.</th>
                <th>Lokasi</th>
                <th>UMP</th>
                <th>Tahun</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->lokasi }}</td>
                    <td> Rp{{ number_format($item->ump, 0, ',', '.') }}</td>
                    <td>{{ $item->tahun }}</td>

                    <td>
                        <a href="/getupdate-ump/{{ $item->id }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ url('delete-ump', $item->id) }}" class="btn btn-sm btn-danger btn-delete"
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