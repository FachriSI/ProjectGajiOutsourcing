@extends('layouts.main')
@section('title', 'Tambah Resiko')
@section('content')
    <h3 class="mt-4">Tambah Resiko</h3>
    <div class="card mb-4">
        <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Data Resiko Baru</h6>
                </div>
                <div class="card-body">
                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-resiko">
                        @csrf
                        <div class="mb-3">
                            <label for="resiko" class="form-label fw-bold">Nama Resiko <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="resiko" id="resiko" placeholder="Contoh: Resiko Tinggi, Resiko Sedang" required>
                        </div>

                         <div class="mb-3">
                            <label for="nominal" class="form-label fw-bold">Nominal (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control uang" name="nominal" id="nominal" placeholder="0" required>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/resiko" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Resiko
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