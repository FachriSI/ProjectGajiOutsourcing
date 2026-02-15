@extends('layouts.main')

@section('title', 'Tambah Pakaian')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tshirt me-2 text-primary"></i> Tambah Pakaian</h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data pakaian karyawan</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Data Pakaian Baru
                    </h5>

                    <form action="/tambah-pakaian" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Karyawan <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="karyawan_id" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($karyawan as $k)
                                    <option value="{{ $k->karyawan_id }}">{{ $k->nama_tk }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Nilai Jatah</label>
                            <input type="number" class="form-control" name="nilai_jatah" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Ukuran Baju</label>
                            <select class="form-select select2" name="ukuran_baju">
                                <option value="">-- Pilih Ukuran --</option>
                                @foreach ($masterUkuran as $m)
                                    <option value="{{ $m->nama_ukuran }}">{{ $m->nama_ukuran }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Ukuran Celana</label>
                            <input type="number" class="form-control" name="ukuran_celana" min="25" max="45">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="beg_date">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/pakaian" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Pakaian
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
                placeholder: "Pilih...",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection