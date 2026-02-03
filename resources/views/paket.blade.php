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

    <!-- CARD UTAMA - Total Kontrak/Tahun -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg overflow-hidden position-relative" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">

                <div class="card-body p-4 position-relative">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                                    <i class="fas fa-star fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <div class="text-white fw-bold" style="font-size: 1.1rem;">TOTAL KONTRAK / TAHUN</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                            <div class="display-4 fw-bold text-white mb-0" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                                Rp{{ number_format($total_kontrak_tahunan_all, 0, ',', '.') }}
                            </div>
                            <div class="text-white-50 mt-1" style="font-size: 0.9rem;">
                                <i class="fas fa-chart-line me-1"></i> Tahun {{ date('Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PEMBANDING BULANAN -->
    <div class="row mb-4">
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

    <!-- PEMBANDING TAHUNAN -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #F093FB 0%, #F5576C 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-white fw-bold mb-2" style="font-size: 0.95rem;">Total THR/Tahun</div>
                            <div class="h4 mb-0 fw-bold text-white">Rp{{ number_format($total_thr_thn, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-gift fa-lg text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px; transition: all 0.3s ease;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #4A5568 0%, #2D3748 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-white fw-bold mb-2" style="font-size: 0.95rem;">Total Pakaian/Tahun</div>
                            <div class="h4 mb-0 fw-bold text-white">Rp{{ number_format($total_pakaian_all, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="fas fa-tshirt fa-lg text-white"></i>
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


    <div class="card mb-4 border-0 shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover display nowrap" id="datatablesSimple" style="width:100%">
                <thead class="thead-dark">
                    <tr>
                        <th>No.</th>
                        <th>Nama Paket</th>
                        <th>Kuota (Orang)</th>
                        <th>Unit Kerja</th>
                        <th class="text-center">Detail</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->paket }}</td>
                            <td>{{ $item->kuota_paket }}</td>
                            <td>{{ $item->unit_kerja }}</td>
                            <td class="text-center">
                                <a href="{{ url('/paket/' . $item->paket_id) }}" class="btn btn-sm btn-info text-white"
                                    title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="/getupdate-paket/{{ $item->paket_id }}" class="btn btn-sm btn-warning"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ url('delete-paket', $item->paket_id) }}" class="btn btn-sm btn-danger btn-delete"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                        <i class="fas fa-trash"></i>
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