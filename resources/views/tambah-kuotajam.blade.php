@extends('layouts.main')

@section('title', 'Tambah Kuota Jam')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-hourglass-half me-2 text-primary"></i> Tambah Kuota Jam</h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data kuota jam baru</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Data Kuota Jam Baru
                    </h5>

                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
                        action="/tambah-kuotajam">
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
                            <label for="kuota" class="form-label fw-bold text-dark">Jumlah Kuota (Jam) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="kuota" id="kuota" placeholder="Contoh: 40"
                                min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="beg_date" class="form-label fw-bold text-dark">Tanggal Mulai <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="beg_date" id="beg_date" required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/kuotajam" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Kuota
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
                width: '100%',
                placeholder: "-- Pilih Karyawan --"
            });
        });
    </script>
@endsection