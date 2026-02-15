@extends('layouts.main')
@section('title', 'Tambah Kuota Jam')

@section('content')
    <h3 class="mt-4">Tambah Kuota Jam</h3>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="fas fa-hourglass-half me-2"></i>Data Kuota Jam Baru</h6>
                </div>
                <div class="card-body">
                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-kuotajam">
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
                            <label for="kuota" class="form-label fw-bold">Jumlah Kuota (Jam) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="kuota" id="kuota" placeholder="Contoh: 40" min="1" required
                             onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                        </div>
                        <div class="mb-3">
                            <label for="beg_date" class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="beg_date" id="beg_date" required>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/kuotajam" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Kuota
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection