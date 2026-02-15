@extends('layouts.main')
@section('title', 'Tambah Masa Kerja')
@section('content')
    <h3 class="mt-4">Tambah Masa Kerja</h3>
    <div class="card mb-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-medal me-2"></i>Data Masa Kerja Baru</h6>
                </div>
                <div class="card-body">
                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-masakerja">
                        @csrf
                        <div class="mb-3">
                            <label for="karyawan_id" class="form-label fw-bold">Karyawan <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="karyawan_id" id="karyawan_id" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($karyawan as $k)
                                    <option value="{{ $k->karyawan_id }}">{{ $k->nama_tk }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tunjangan_masakerja" class="form-label fw-bold">Tunjangan Masa Kerja (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control uang" name="tunjangan_masakerja" id="tunjangan_masakerja" placeholder="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="beg_date" class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                             <input type="date" class="form-control" name="beg_date" id="beg_date" required>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/masakerja" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Masa Kerja
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('.uang').mask('000.000.000.000', {reverse: true});
        });
    </script>
@endsection