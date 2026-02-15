@extends('layouts.main')

@section('title', 'Update Resiko')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-warning border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit me-2 text-warning"></i> Update Resiko</h1>
        <p class="text-muted small mb-0 mt-1">Perbarui informasi data tunjangan resiko</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-warning border-4">
                <div class="card-body">
                    <h5 class="card-title text-warning fw-bold mb-4">
                        <i class="fas fa-edit me-2"></i>Edit Data Resiko
                    </h5>

                    <form action="/update-resiko/{{ $dataR->kode_resiko }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label fw-bold text-dark">Nama Resiko <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" value="{{ $dataR->resiko }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="tunjangan" class="form-label fw-bold text-dark">Tunjangan Resiko</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control uang" id="tunjangan" name="tunjangan"
                                    value="{{ $dataR->tunjangan_resiko }}">
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/resiko" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning text-white px-4">
                                <i class="fas fa-save me-2"></i>Update Resiko
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