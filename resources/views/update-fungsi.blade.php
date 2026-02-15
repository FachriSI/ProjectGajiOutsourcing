@extends('layouts.main')

@section('title', 'Update Fungsi')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-warning border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit me-2 text-warning"></i> Update Fungsi</h1>
        <p class="text-muted small mb-0 mt-1">Perbarui informasi data fungsi</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-warning border-4">
                <div class="card-body">
                    <h5 class="card-title text-warning fw-bold mb-4">
                        <i class="fas fa-edit me-2"></i>Edit Data Fungsi
                    </h5>

                    <form action="/update-fungsi/{{ $dataF->kode_fungsi }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label fw-bold text-dark">Nama Fungsi <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" value="{{ $dataF->fungsi }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label fw-bold text-dark">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan"
                                rows="3">{{ $dataF->keterangan }}</textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/fungsi" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning text-white px-4">
                                <i class="fas fa-save me-2"></i>Update Fungsi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection