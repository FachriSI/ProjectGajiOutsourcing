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
    <div class="row mb-4">
        <!-- Main Card: Total Kontrak / Tahun -->
        <div class="col-xl-6 col-12 mb-3">
            <div class="card border-0 shadow-lg overflow-hidden position-relative h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
                <div class="card-body p-4 position-relative d-flex align-items-center">
                    <div class="row flex-fill align-items-center">
                        <div class="col-lg-7">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                                    <i class="fas fa-star fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <div class="text-white fw-bold" style="font-size: 1.1rem;">TOTAL KONTRAK / TAHUN</div>
                                    <div class="text-white-50 mt-1" style="font-size: 0.9rem;">
                                        <i class="fas fa-chart-line me-1"></i> Tahun {{ date('Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                            <div class="display-6 fw-bold text-white mb-0" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                                Rp{{ number_format($total_kontrak_tahunan_all, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Annual Stat: THR / Tahun -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #F093FB 0%, #F5576C 100%);">
                    <div class="d-flex justify-content-between align-items-start h-100 flex-column">
                        <div class="w-100 d-flex justify-content-between mb-3">
                            <div class="text-white fw-bold" style="font-size: 0.95rem;">Total THR/Tahun</div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="fas fa-gift fa-lg text-white"></i>
                            </div>
                        </div>
                        <div class="mt-auto">
                            <div class="h4 mb-0 fw-bold text-white">Rp{{ number_format($total_thr_thn, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Annual Stat: Pakaian / Tahun -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #4A5568 0%, #2D3748 100%);">
                    <div class="d-flex justify-content-between align-items-start h-100 flex-column">
                        <div class="w-100 d-flex justify-content-between mb-3">
                            <div class="text-white fw-bold" style="font-size: 0.95rem;">Total Pakaian/Tahun</div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="fas fa-tshirt fa-lg text-white"></i>
                            </div>
                        </div>
                        <div class="mt-auto">
                            <div class="h4 mb-0 fw-bold text-white">Rp{{ number_format($total_pakaian_all, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: MONTHLY BREAKDOWN -->
    <div class="row mb-4">
        <!-- Monthly 1: Fix Cost -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #3DD9E2 0%, #17a2b8 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-white fw-bold mb-2" style="font-size: 0.95rem;">Total Fix Cost/Bln</div>
                            <div class="h4 mb-0 fw-bold text-white">Rp{{ number_format($total_jml_fix_cost, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-tags fa-lg text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Monthly 2: Variabel -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #868e96 0%, #6c757d 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-white fw-bold mb-2" style="font-size: 0.95rem;">Total Variabel/Bln</div>
                            <div class="h4 mb-0 fw-bold text-white">Rp{{ number_format($total_seluruh_variabel, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-chart-area fa-lg text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Monthly 3: Total Kontrak/Bln -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #38D39F 0%, #28a745 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-white fw-bold mb-2" style="font-size: 0.95rem;">Total Kontrak/Bln</div>
                            <div class="h4 mb-0 fw-bold text-white">Rp{{ number_format($total_kontrak_all, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-file-contract fa-lg text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Monthly 4: THR/Bln -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #F5A623 0%, #F2994A 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-white fw-bold mb-2" style="font-size: 0.95rem;">Total THR/Bln</div>
                            <div class="h4 mb-0 fw-bold text-white">Rp{{ number_format($total_thr_bln, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-gift fa-lg text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
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
                                <a href="{{ url('/paket/' . $item->paket_id) }}" class="btn btn-sm btn-info text-white shadow-sm"
                                    title="Lihat Detail">
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