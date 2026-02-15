@extends('layouts.main')

@section('title', 'Tambah Departemen')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-building me-2 text-primary"></i> Tambah Departemen</h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data departemen baru</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Data Departemen Baru
                    </h5>

                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
                        action="/tambah-departemen">
                        @csrf
                        <div class="mb-3">
                            <label for="departemen" class="form-label fw-bold text-dark">Nama Departemen <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="departemen" id="departemen"
                                placeholder="Contoh: HRD, Finance, IT" required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/departemen" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Departemen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection