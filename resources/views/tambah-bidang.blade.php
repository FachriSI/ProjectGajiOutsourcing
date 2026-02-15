@extends('layouts.main')

@section('title', 'Tambah Bidang')

@section('content')

    <h3 class="mt-4">Tambah Bidang</h3>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-layer-group me-2"></i>Data Bidang Baru</h6>
                </div>
                <div class="card-body">
                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-bidang">
                        @csrf
                        <div class="mb-3">
                            <label for="unit" class="form-label fw-bold">Unit Kerja <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="unit" id="unit" required>
                                <option value="" selected disabled>Pilih Unit Kerja...</option>
                                @foreach ($dataU as $item)
                                    <option value="{{$item->unit_id}}">{{$item->unit_kerja}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bidang" class="form-label fw-bold">Nama Bidang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="bidang" id="bidang" placeholder="Masukkan nama bidang..." required>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/unit-kerja" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Bidang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: $(this).data('placeholder'),
            });
        });
    </script>
@endsection
