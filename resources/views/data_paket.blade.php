@extends('layouts.main')
@section('title', 'Paket')
@section('content')
    <!-- Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-warning border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-box me-2 text-warning"></i> Paket</h1>
                <p class="text-muted small mb-0 mt-1">Kelola data paket dan kuota</p>
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

    <!-- Data Table -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-dark text-white py-3">
            <h6 class="m-0 fw-bold"><i class="fas fa-table me-2"></i>Daftar Paket</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered datatable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No.</th>
                            <th>Paket ID</th>
                            <th>Kuota (Orang)</th>
                            <th>Unit Kerja</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $item->paket }}</td>
                                <td>{{ $item->kuota_paket }}</td>
                                <td>{{ $item->unit_kerja }}</td>
                                <td class="text-center">

                                        
                                    <div class="btn-group" role="group">
                                        <!-- Button Add Employee -->
                                        <button type="button" class="btn btn-sm btn-success btn-add-employee" 
                                            data-paket-id="{{ $item->paket_id }}"
                                            data-paket-nama="{{ $item->paket }}"
                                            data-bs-toggle="tooltip" title="Tambah Karyawan">
                                            <i class="fas fa-user-plus"></i>
                                        </button>

                                        <a href="/getupdate-paket/{{ $item->paket_id }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ url('delete-paket', $item->paket_id) }}" class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Yakin ingin menghapus data ini?')">
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
    </div>

    <!-- Modal Tambah Karyawan -->
    <div class="modal fade" id="modalAddEmployee" tabindex="-1" aria-labelledby="modalAddEmployeeLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ url('/tambah-karyawan-paket') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="modalAddEmployeeLabel">Tambah Karyawan ke Paket</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="paket_id_add" id="inputPaketId">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Paket</label>
                            <input type="text" class="form-control" id="displayPaketNama" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="selectKaryawan" class="form-label fw-bold">Pilih Karyawan</label>
                            <small class="text-muted d-block mb-2">*Hanya karyawan yang belum memiliki paket</small>
                            <select class="form-select" name="karyawan_id" id="selectKaryawan" required>
                                <option value="" selected disabled>-- Pilih Karyawan --</option>
                                @if(isset($availableKaryawan) && count($availableKaryawan) > 0)
                                    @foreach($availableKaryawan as $karyawan)
                                        <option value="{{ $karyawan->karyawan_id }}">{{ $karyawan->nama_tk }} ({{ $karyawan->osis_id }})</option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Tidak ada karyawan tersedia</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

             // Handle Add Employee Button Click
             $('.btn-add-employee').on('click', function() {
                var paketId = $(this).data('paket-id');
                var paketNama = $(this).data('paket-nama');

                $('#inputPaketId').val(paketId);
                $('#displayPaketNama').val(paketNama);
                
                var modal = new bootstrap.Modal(document.getElementById('modalAddEmployee'));
                modal.show();
            });
        });
    </script>
@endsection