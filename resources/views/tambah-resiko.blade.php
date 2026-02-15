@extends('layouts.main')

@section('title', 'Tambah Resiko')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-exclamation-triangle me-2 text-primary"></i> Tambah Resiko</h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data tunjangan resiko</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Data Resiko Baru
                    </h5>

                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
                        action="/tambah-resiko">
                        @csrf
                        <div class="mb-3">
                            <label for="resiko" class="form-label fw-bold text-dark">Nama Resiko <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="resiko" id="resiko"
                                placeholder="Contoh: Resiko Tinggi, Resiko Sedang" required>
                        </div>

                        <div class="mb-3">
                            <label for="nominal" class="form-label fw-bold text-dark">Nominal (Rp) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control uang" name="nominal" id="nominal" placeholder="0"
                                    required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/resiko" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Resiko
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