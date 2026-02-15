@extends('layouts.main')

@section('title', 'Update Data Lebaran')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-warning border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit me-2 text-warning"></i> Update Data Lebaran</h1>
        <p class="text-muted small mb-0 mt-1">Perbarui informasi data lebaran</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-warning border-4">
                <div class="card-body">
                    <h5 class="card-title text-warning fw-bold mb-4">
                        <i class="fas fa-edit me-2"></i>Edit Data Lebaran
                    </h5>

                    <form action="/update-lebaran/{{ $lebaran->id }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="tahun" class="form-label fw-bold text-dark">Tahun (Masehi) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('tahun') is-invalid @enderror" id="tahun"
                                name="tahun" value="{{ old('tahun', $lebaran->tahun) }}" required>
                            @error('tahun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="tanggal" class="form-label fw-bold text-dark">Tanggal Lebaran (Idul Fitri) <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                                name="tanggal" value="{{ old('tanggal', $lebaran->tanggal) }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="tahun_hijriyah" class="form-label fw-bold text-dark">Tahun Hijriyah
                                (Opsional)</label>
                            <input type="text" class="form-control" id="tahun_hijriyah" name="tahun_hijriyah"
                                placeholder="Contoh: 1447 H" value="{{ old('tahun_hijriyah', $lebaran->tahun_hijriyah) }}">
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label fw-bold text-dark">Keterangan (Opsional)</label>
                            <textarea class="form-control" id="keterangan" name="keterangan"
                                rows="3">{{ old('keterangan', $lebaran->keterangan) }}</textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/lebaran" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning text-white px-4">
                                <i class="fas fa-save me-2"></i>Update Lebaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection