@extends('layouts.main')

@section('title', 'Tambah Jabatan')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-briefcase me-2 text-primary"></i> Tambah Jabatan</h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data jabatan baru</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Data Jabatan Baru
                    </h5>

                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
                        action="/tambah-jabatan">
                        @csrf
                        <div class="mb-3">
                            <label for="jabatan" class="form-label fw-bold text-dark">Nama Jabatan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="jabatan" id="jabatan"
                                placeholder="Contoh: Staff IT, HR Manager" required>
                        </div>

                        <div class="mb-3">
                            <label for="tunjangan" class="form-label fw-bold text-dark">Tunjangan Jabatan (Rp) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control uang" name="tunjangan" id="tunjangan" placeholder="0"
                                    required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/jabatan" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Jabatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.uang').mask('000.000.000.000', { reverse: true });
        });
    </script>
@endsection