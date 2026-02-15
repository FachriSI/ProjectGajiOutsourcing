@extends('layouts.main')

@section('title', 'Tambah Departemen')

@section('content')

    <h3 class="mt-4">Tambah Departemen</h3>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>Data Departemen Baru</h6>
                </div>
                <div class="card-body">
                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-departemen">
                        @csrf
                        <div class="mb-3">
                            <label for="departemen" class="form-label fw-bold">Nama Departemen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="departemen" id="departemen" placeholder="Contoh: HRD, Finance, IT" required>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/departemen" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Departemen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection