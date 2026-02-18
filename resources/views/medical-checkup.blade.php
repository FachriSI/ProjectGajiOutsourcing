@extends('layouts.main')
@section('title', 'Medical Checkup')
@section('content')
    <!-- Modern Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-heartbeat me-2 text-primary"></i> Medical Checkup</h1>
                <p class="text-muted small mb-0 mt-1">Kelola data biaya medical checkup (Global)</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0 mb-4 border-top border-primary border-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Atur Biaya Medical Checkup</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border border-primary text-primary">
                        <i class="fas fa-info-circle me-1"></i>
                        Pengaturan ini akan memperbarui biaya medical checkup untuk <strong>semua karyawan aktif</strong>.
                    </div>

                    <form action="{{ url('/medical-checkup/update-global') }}" method="POST">
                        @csrf

                        <div class="mb-4 text-center">
                            <label class="text-muted mb-1">Biaya MCU Saat Ini</label>
                            <h2 class="font-weight-bold text-primary">Rp {{ number_format($currentBiaya, 0, ',', '.') }}
                            </h2>
                        </div>

                        <div class="mb-3">
                            <label for="biaya" class="form-label">Biaya MCU Baru (Rp)</label>
                            <input type="number" class="form-control" id="biaya" name="biaya"
                                value="{{ $currentBiaya }}" required min="0">
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan Global
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
