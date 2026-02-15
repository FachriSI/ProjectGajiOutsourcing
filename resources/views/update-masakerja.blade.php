@extends('layouts.main')

@section('title', 'Update Masa Kerja')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-warning border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit me-2 text-warning"></i> Update Masa Kerja</h1>
        <p class="text-muted small mb-0 mt-1">Perbarui informasi data tunjangan masa kerja</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-warning border-4">
                <div class="card-body">
                    <h5 class="card-title text-warning fw-bold mb-4">
                        <i class="fas fa-edit me-2"></i>Edit Data Masa Kerja
                    </h5>

                    <form action="/update-masakerja/{{ $dataM->id }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Karyawan</label>
                            <select class="form-select select2" name="karyawan_id" required>
                                @foreach ($karyawan as $k)<option value="{{ $k->karyawan_id }}" {{ $k->karyawan_id == $dataM->karyawan_id ? 'selected' : '' }}>{{ $k->nama_tk }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Tunjangan Masa Kerja</label>
                            <input type="number" class="form-control" name="tunjangan_masakerja"
                                value="{{ $dataM->tunjangan_masakerja }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="beg_date" value="{{ $dataM->beg_date }}">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/masakerja" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning text-white px-4">
                                <i class="fas fa-save me-2"></i>Update Masa Kerja
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>
@endsection