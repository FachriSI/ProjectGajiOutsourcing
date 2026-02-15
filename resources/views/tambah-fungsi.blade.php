@extends('layouts.main')

@section('title', 'Tambah Fungsi')

@section('content')

    <h3 class="mt-4">Tambah Fungsi</h3>

    <div class="card mb-4">
        <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Data Fungsi Baru</h6>
                </div>
                <div class="card-body">
                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-fungsi">
                        @csrf
                        <div class="mb-3">
                            <label for="fungsi" class="form-label fw-bold">Nama Fungsi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="fungsi" id="fungsi" placeholder="Contoh: Recruitment, Payroll" required>
                        </div>

                        <div class="mb-3">
                             <label for="departemen_id" class="form-label fw-bold">Pilih Departemen <span class="text-danger">*</span></label>
                            <select class="custom-select select2" name="departemen_id" id="departemen_id" required>
                                <option value="" selected disabled>Pilih Departemen</option>
                                @foreach ($dep as $item)
                                    <option value="{{$item->departemen_id}}">{{$item->departemen}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/fungsi" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Fungsi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection