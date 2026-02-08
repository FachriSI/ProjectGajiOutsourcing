@extends('layouts.main')
@section('title', 'Medical Checkup')
@section('content')
    <!-- Modern Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-heartbeat me-2 text-danger"></i> Medical Checkup</h1>
                <p class="text-muted small mb-0 mt-1">Kelola data riwayat kesehatan karyawan</p>
            </div>
            <div class="d-flex gap-2">
                <!-- Buttons (Placeholder functionality for now) -->
                @if(isset($hasDeleted) && $hasDeleted)
                    <a href="{{ route('medical-checkup.trash') }}" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-trash-restore me-1"></i> Data Sampah
                    </a>
                @endif
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus me-1"></i> Tambah Data
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered {{ (isset($data) && count($data) > 0) ? 'datatable' : '' }}"
                    id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No.</th>
                            <th class="text-center">Biaya Medical Checkup</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($data) && count($data) > 0)
                            @foreach ($data as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center fw-bold text-success">
                                        Rp {{ number_format($item->biaya, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button onclick="editData({{ $item->id }})" class="btn btn-sm btn-warning"
                                                data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="{{ route('medical-checkup.destroy', $item->id) }}"
                                                class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                        <i class="fas fa-clipboard-list fa-3x mb-3 text-gray-300"></i>
                                        <p class="mb-0 fw-bold">Belum ada data Medical Checkup</p>
                                        <small>Data akan muncul di sini setelah ditambahkan.</small>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Data Biaya MCU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('medical-checkup.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="biaya" class="form-label">Biaya Medical Checkup</label>
                            <input type="number" class="form-control" id="biaya" name="biaya" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Data Biaya MCU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Action will be set dynamically -->
                <form id="editForm" method="POST">
                    @csrf
                    <!-- No PUT Spoofing here if route assumes POST, checking web.php... it used POST. Correct. -->
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_biaya" class="form-label">Biaya Medical Checkup</label>
                            <input type="number" class="form-control" id="edit_biaya" name="biaya" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .icon-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .bg-danger-light {
            background-color: rgba(231, 74, 59, 0.1);
        }
    </style>

    <script>
        $(document).ready(function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });

        function editData(id) {
            // Fetch data via AJAX
            $.get('/getupdate-medical-checkup/' + id, function (data) {
                $('#edit_biaya').val(data.biaya);
                $('#editForm').attr('action', '/update-medical-checkup/' + id);
                var myModal = new bootstrap.Modal(document.getElementById('editModal'));
                myModal.show();
            });
        }
    </script>
@endsection