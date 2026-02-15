@extends('layouts.main')

@section('title', 'Update Perusahaan')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-warning border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit me-2 text-warning"></i> Update Perusahaan</h1>
        <p class="text-muted small mb-0 mt-1">Perbarui informasi data perusahaan</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 mb-4 border-top border-warning border-4">
                <div class="card-body">
                    <h5 class="card-title text-warning fw-bold mb-4">
                        <i class="fas fa-edit me-2"></i>Edit Data Perusahaan
                    </h5>

                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
                        action="/update-perusahaan/{{$dataP->perusahaan_id}}">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label fw-bold text-dark">Nama Perusahaan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" id="nama" value="{{$dataP->perusahaan}}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label fw-bold text-dark">Alamat</label>
                            <textarea class="form-control" name="alamat" id="alamat" rows="3">{{$dataP->alamat}}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cp" class="form-label fw-bold text-dark">Contact Person (CP)</label>
                                <input type="text" class="form-control" name="cp" id="cp" value="{{$dataP->cp}}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cp_jab" class="form-label fw-bold text-dark">Jabatan CP</label>
                                <input type="text" class="form-control" name="cp_jab" id="cp_jab"
                                    value="{{$dataP->cp_jab}}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cp_telp" class="form-label fw-bold text-dark">No. Telepon CP</label>
                                <input type="text" class="form-control" name="cp_telp" id="cp_telp"
                                    value="{{$dataP->cp_telp}}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cp_email" class="form-label fw-bold text-dark">Email CP</label>
                                <input type="email" class="form-control" name="cp_email" id="cp_email"
                                    value="{{$dataP->cp_email}}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_mesin" class="form-label fw-bold text-dark">ID Mesin</label>
                                <input type="text" class="form-control" name="id_mesin" id="id_mesin"
                                    value="{{$dataP->id_mesin}}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="deleted_data" class="form-label fw-bold text-dark">Deleted Data</label>
                                <input type="text" class="form-control" name="deleted_data" id="deleted_data"
                                    value="{{$dataP->deleted_data}}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tkp" class="form-label fw-bold text-dark">TKP</label>
                                <input type="text" class="form-control" name="tkp" id="tkp" value="{{$dataP->tkp}}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="npp" class="form-label fw-bold text-dark">NPP</label>
                                <input type="text" class="form-control" name="npp" id="npp" value="{{$dataP->npp}}">
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/perusahaan" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning text-white px-4">
                                <i class="fas fa-save me-2"></i>Update Perusahaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection