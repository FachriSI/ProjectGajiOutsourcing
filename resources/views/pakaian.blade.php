@extends('layouts.main')
@section('title', 'Pakaian')
@section('content')
    <!-- Modern Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tshirt me-2 text-primary"></i> Pakaian</h1>
                <p class="text-muted small mb-0 mt-1">Kelola data jatah pakaian dinas (Global)</p>
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
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Atur Nilai Jatah Pakaian</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        Pengaturan ini akan memperbarui nilai jatah pakaian untuk <strong>semua karyawan aktif</strong>.
                    </div>

                    <form action="{{ url('/pakaian/update-global') }}" method="POST">
                        @csrf

                        <div class="mb-4 text-center">
                            <label class="text-muted mb-1">Nilai Jatah Saat Ini</label>
                            <h2 class="font-weight-bold text-success">Rp {{ number_format($currentNilai, 0, ',', '.') }}
                            </h2>
                        </div>

                        <div class="mb-3">
                            <label for="nilai_jatah" class="form-label">Nilai Jatah Baru (Rp)</label>
                            <input type="number" class="form-control" id="nilai_jatah" name="nilai_jatah"
                                value="{{ $currentNilai }}" required min="0">
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