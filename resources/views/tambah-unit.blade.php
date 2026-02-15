@extends('layouts.main')

@section('title', 'Unit Kerja')

@section('content')

<h3 class="mt-4">Tambah Unit Kerja</h3>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-network-wired me-2"></i>Data Fungsi Kerja Baru</h6>
                </div>
                <div class="card-body">
                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-unit">
                        @csrf
                        <div class="mb-3">
                            <label for="unit_kerja" class="form-label fw-bold">Nama Fungsi Kerja <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="unit_kerja" id="unit_kerja" placeholder="Contoh: Unit A, Unit B" required>
                        </div>

                         <div class="mb-3">
                             <label for="fungsi_id" class="form-label fw-bold">Pilih Fungsi <span class="text-danger">*</span></label>
                            <select class="custom-select select2" name="fungsi_id" id="fungsi_id" required>
                                <option value="" selected disabled>Pilih Fungsi</option>
                                @foreach ($fungsi as $item)
                                    <option value="{{$item->fungsi_id}}">{{$item->fungsi}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="/unit" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Fungsi Kerja
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
