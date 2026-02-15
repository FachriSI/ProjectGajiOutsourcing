@extends('layouts.main')

@section('title', 'Tambah Bidang')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-layer-group me-2 text-primary"></i> Tambah Bidang</h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data bidang baru</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Data Bidang Baru
                    </h5>

                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
                        action="/tambah-bidang">
                        @csrf
                        <div class="mb-3">
                            <label for="unit" class="form-label fw-bold text-dark">Unit Kerja <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" name="unit" id="unit" required>
                                <option value="" selected disabled>Pilih Unit Kerja...</option>
                                @foreach ($dataU as $item)
                                    <option value="{{$item->unit_id}}">{{$item->unit_kerja}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bidang" class="form-label fw-bold text-dark">Nama Bidang <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="bidang" id="bidang"
                                placeholder="Masukkan nama bidang..." required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/unit-kerja" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Bidang
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
                theme: "bootstrap-5",
                width: '100%',
                placeholder: "Pilih...",
            });
        });
    </script>
@endsection