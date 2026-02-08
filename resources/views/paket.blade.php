@extends('layouts.main')

@section('title', 'Data Paket')

@section('content')
    <!-- Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-info border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tags me-2 text-info"></i> Data Paket</h1>
                <p class="text-muted small mb-0 mt-1">Dashboard dan Detail Paket Kontrak</p>
            </div>
            <div class="d-flex gap-2">
                @if($hasDeleted)
                    <a href="/paket/sampah" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Sampah
                    </a>
                @endif
                <a href="/gettambah-paket" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Paket
                </a>
            </div>
        </div>
    </div>

    <!-- ROW 1: ANNUAL OVERVIEW -->
    <!-- ROW 1: ANNUAL OVERVIEW -->
    <div class="row mb-4">
        <!-- Main Card: Total Kontrak / Tahun -->
        <div class="col-xl-6 col-12 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-4"
                        style="width: 70px; height: 70px; background-color: #e0e7ff; color: #4e73df; min-width: 70px;">
                        <i class="fas fa-star fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-uppercase fw-bold text-muted small mb-1" style="letter-spacing: 0.5px;">TOTAL
                            KONTRAK / TAHUN</div>
                        <div class="display-6 fw-bold text-dark mb-0">
                            Rp{{ number_format($total_kontrak_tahunan_all, 0, ',', '.') }}</div>
                        <div class="text-muted small mt-2">
                            <i class="fas fa-calendar-alt me-1 text-primary"></i> Tahun {{ date('Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Annual Stat: THR / Tahun -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px; background-color: #ffe2e5; color: #e74a3b;">
                            <i class="fas fa-gift fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total THR/Tahun</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_thr_thn, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Annual Stat: Pakaian / Tahun -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px; background-color: #fff3cd; color: #f6c23e;">
                            <i class="fas fa-tshirt fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Pakaian/Tahun</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_pakaian_all, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: MONTHLY BREAKDOWN -->
    <div class="row mb-4">
        <!-- Monthly 1: Fix Cost -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px; background-color: #ccf6ff; color: #17a2b8;">
                            <i class="fas fa-tags fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Fix Cost/Bln</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_jml_fix_cost, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Monthly 2: Variabel -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px; background-color: #e2e3e5; color: #383d41;">
                            <i class="fas fa-chart-area fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Variabel/Bln</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_seluruh_variabel, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Monthly 3: Total Kontrak/Bln -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px; background-color: #d1e7dd; color: #0f5132;">
                            <i class="fas fa-file-contract fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total Kontrak/Bln</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_kontrak_all, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Monthly 4: THR/Bln -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px; background-color: #fff3cd; color: #856404;">
                            <i class="fas fa-hand-holding-usd fa-lg"></i>
                        </div>
                        <div class="text-uppercase fw-bold text-muted small">Total THR/Bln</div>
                    </div>
                    <div class="h4 fw-bold text-dark mb-0">Rp{{ number_format($total_thr_bln, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }
    </style>

    <!-- Data Table -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-dark text-white py-3">
            <h6 class="m-0 fw-bold"><i class="fas fa-table me-2"></i>Daftar Paket Kontrak</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover display nowrap" id="datatablesSimple" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="5%">No.</th>
                        <th>Nama Paket</th>
                        <th class="text-center">Kuota (Orang)</th>
                        <th>Unit Kerja</th>
                        <th class="text-center">Detail</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $item->paket }}</td>
                            <td class="text-center">{{ $item->kuota_paket }}</td>
                            <td>{{ $item->unit_kerja }}</td>
                            <td class="text-center">
                                <a href="{{ url('/paket/' . $item->paket_id) }}"
                                    class="btn btn-sm btn-info text-white shadow-sm" title="Lihat Detail">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="/getupdate-paket/{{ $item->paket_id }}" class="btn btn-sm btn-warning"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
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