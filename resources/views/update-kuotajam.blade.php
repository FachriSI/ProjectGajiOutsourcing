@extends('layouts.main')

@section('title', 'Update Kuota Jam')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-warning border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit me-2 text-warning"></i> Update Kuota Jam</h1>
        <p class="text-muted small mb-0 mt-1">Perbarui informasi data kuota jam</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-warning border-4">
                <div class="card-body">
                    <h5 class="card-title text-warning fw-bold mb-4">
                        <i class="fas fa-edit me-2"></i>Edit Data Kuota Jam
                    </h5>

                    <form action="/update-kuotajam/{{ $dataK->kuota_id }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Karyawan</label>
                            <select class="form-select select2" name="karyawan_id" required>
                                @foreach ($karyawan as $k)
                                    <option value="{{ $k->karyawan_id }}" {{ $k->karyawan_id == $dataK->karyawan_id ? 'selected' : '' }}>{{ $k->nama_tk }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Kuota (Jam)</label>
                            <input type="number" class="form-control" name="kuota" value="{{ $dataK->kuota }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="beg_date" value="{{ $dataK->beg_date }}">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/kuotajam" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning text-white px-4">
                                <i class="fas fa-save me-2"></i>Update Kuota
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
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    </script>
@endsection