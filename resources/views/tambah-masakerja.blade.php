@extends('layouts.main')

@section('title', 'Tambah Masa Kerja')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-medal me-2 text-primary"></i> Tambah Masa Kerja</h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data tunjangan masa kerja</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Data Masa Kerja Baru
                    </h5>

                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
                        action="/tambah-masakerja">
                        @csrf
                        <div class="mb-3">
                            <label for="karyawan_id" class="form-label fw-bold text-dark">Karyawan <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" name="karyawan_id" id="karyawan_id" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($karyawan as $k)
                                    <option value="{{ $k->karyawan_id }}">{{ $k->nama_tk }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tunjangan_masakerja" class="form-label fw-bold text-dark">Tunjangan Masa Kerja (Rp)
                                <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control uang" name="tunjangan_masakerja"
                                    id="tunjangan_masakerja" placeholder="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="beg_date" class="form-label fw-bold text-dark">Tanggal Mulai <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="beg_date" id="beg_date" required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/masakerja" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Masa Kerja
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
            $('.select2').select2({
                placeholder: "-- Pilih Karyawan --",
                allowClear: false,
                width: '100%'
            });
        });
    </script>
@endsection