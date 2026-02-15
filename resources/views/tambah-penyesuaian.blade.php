@extends('layouts.main')

@section('title', 'Tambah Penyesuaian')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-sliders-h me-2 text-primary"></i> Tambah Penyesuaian</h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data tunjangan penyesuaian</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Data Penyesuaian Baru
                    </h5>

                    <form action="/tambah-penyesuaian" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Keterangan</label>
                            <input type="text" class="form-control" name="keterangan" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Tunjangan Penyesuaian</label>
                            <input type="number" class="form-control" name="tunjangan" value="0">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/penyesuaian" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Penyesuaian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection