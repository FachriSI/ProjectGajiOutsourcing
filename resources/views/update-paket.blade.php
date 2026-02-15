@extends('layouts.main')

@section('title', 'Update Paket')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-warning border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit me-2 text-warning"></i> Update Paket</h1>
        <p class="text-muted small mb-0 mt-1">Perbarui informasi data paket</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-warning border-4">
                <div class="card-body">
                    <h5 class="card-title text-warning fw-bold mb-4">
                        <i class="fas fa-edit me-2"></i>Edit Data Paket
                    </h5>
                    
                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/update-paket/{{$dataP->paket_id}}">
                        @csrf
                        <div class="mb-3">
                            <label for="paket" class="form-label fw-bold text-dark">Nama Paket</label>
                            <input type="text" class="form-control" name="paket" id="paket" value="{{$dataP->paket}}">
                        </div>
                        <div class="mb-3">
                            <label for="kuota_paket" class="form-label fw-bold text-dark">Kuota Paket</label>
                            <input type="text" class="form-control" name="kuota_paket" id="kuota_paket" value="{{$dataP->kuota_paket}}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark" for="unit_kerja">Unit kerja</label>
                            <select class="form-select select2" name="unit_kerja" id="unit_kerja">
                                <option selected>Pilih Unit Kerja</option>
                                @foreach ($unit as $item)
                                    <option value="{{ $item->unit_id }}" 
                                        {{ $item->unit_id == $dataP->unit_id ? 'selected' : '' }}>
                                        {{ $item->unit_kerja }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/paket" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning text-white px-4">
                                <i class="fas fa-save me-2"></i>Update Paket
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
                placeholder: "Pilih Unit Kerja",
                allowClear: false,
                width: '100%'
            });
        });
    </script>
@endsection
