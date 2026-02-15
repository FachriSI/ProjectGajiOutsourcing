@extends('layouts.main')

@section('title', 'Update Penyesuaian')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-warning border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit me-2 text-warning"></i> Update Penyesuaian</h1>
        <p class="text-muted small mb-0 mt-1">Perbarui informasi data tunjangan penyesuaian</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-warning border-4">
                <div class="card-body">
                    <h5 class="card-title text-warning fw-bold mb-4">
                        <i class="fas fa-edit me-2"></i>Edit Data Penyesuaian
                    </h5>

                    <form action="/update-penyesuaian/{{ $dataP->kode_suai }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Keterangan</label>
                            <input type="text" class="form-control" name="keterangan" value="{{ $dataP->keterangan }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Tunjangan Penyesuaian</label>
                            <input type="number" class="form-control" name="tunjangan"
                                value="{{ $dataP->tunjangan_penyesuaian }}">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/penyesuaian" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning text-white px-4">
                                <i class="fas fa-save me-2"></i>Update Penyesuaian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection