@extends('layouts.main')

@section('title', 'Data Paket')

@section('content')
    <h3 class="mt-4">Data Paket</h3>
    <div class="d-flex align-items-center mb-3 text-center gap-2">
        <a href="/gettambah-paket" class="btn btn-primary">Tambah Paket</a>
        @if($hasDeleted)
            <a href="/paket/sampah" class="btn btn-secondary"><i class="fas fa-trash-restore"></i> Sampah</a>
        @endif
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">Total Jml Fix Cost/Bln</div>
                <div class="card-footer">
                    Rp{{ number_format($total_jml_fix_cost, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">Total Variabel Cost/Bln</div>
                <div class="card-footer">
                    Rp{{ number_format($total_seluruh_variabel, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">Total Kontrak/Bln</div>
                <div class="card-footer">
                    Rp{{ number_format($total_kontrak_all, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">Total Kontrak/Thn</div>
                <div class="card-footer">
                    Rp{{ number_format($total_kontrak_tahunan_all, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">Total THR/Bln</div>
                <div class="card-footer">
                    Rp{{ number_format($total_thr_bln, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">Total THR/Thn</div>
                <div class="card-footer">
                    Rp{{ number_format($total_thr_thn, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">Total Pakaian/Thn</div>
                <div class="card-footer">
                    Rp{{ number_format($total_pakaian_all, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>


    <div class="card mb-4 border-0 shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover display nowrap" id="datatablesSimple" style="width:100%">
                <thead class="thead-dark">
                    <tr>
                        <th>No.</th>
                        <th>Nama Paket</th>
                        <th>Kuota (Orang)</th>
                        <th>Unit Kerja</th>
                        <th>Detail</th>
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
                                <a href="{{ url('/paket/' . $item->paket_id) }}" class="btn btn-sm btn-info text-white"
                                    title="Lihat Detail">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                            <td>
                                <a href="/getupdate-paket/{{ $item->paket_id }}" class="btn btn-sm btn-warning"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
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
        </div>
    </div>


    <!-- Add jQuery DataTables Script to ensure controls appear and match Detail page styling -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#datatablesSimple').DataTable({
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "paginate": {
                        "first": "Awal",
                        "last": "Akhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                },
                "autoWidth": false
            });
        });
    </script>
@endsection