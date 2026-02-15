@extends('layouts.main')
@section('title', 'Tambah Jabatan')
@section('content')
    <h3 class="mt-4">Tambah Jabatan</h3>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-briefcase me-2"></i>Data Jabatan Baru</h6>
                        </div>
                        <div class="card-body">
                            <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-jabatan">
                                @csrf
                                <div class="mb-3">
                                    <label for="jabatan" class="form-label fw-bold">Nama Jabatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="jabatan" id="jabatan" placeholder="Contoh: Staff IT, HR Manager" required>
                                </div>

                                <div class="mb-3">
                                    <label for="tunjangan" class="form-label fw-bold">Tunjangan Jabatan (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control uang" name="tunjangan" id="tunjangan" placeholder="0" required>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="/jabatan" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Jabatan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
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