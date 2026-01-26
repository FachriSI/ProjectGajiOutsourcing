@extends('layouts.main')

@section('title', 'Karyawan')

@section('content')
    <h3 class="mt-4">Update Perusahaan</h3>
    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
        action="/update-perusahaan/{{$dataP->perusahaan_id}}">
        <!-- Input Date Range -->
        @csrf
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Perusahaan</label>
            <input type="text" class="form-control" name="nama" id="nama" value="{{$dataP->perusahaan}}" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" name="alamat" id="alamat" rows="3">{{$dataP->alamat}}</textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="cp" class="form-label">Contact Person (CP)</label>
                <input type="text" class="form-control" name="cp" id="cp" value="{{$dataP->cp}}">
            </div>
            <div class="col-md-6 mb-3">
                <label for="cp_jab" class="form-label">Jabatan CP</label>
                <input type="text" class="form-control" name="cp_jab" id="cp_jab" value="{{$dataP->cp_jab}}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="cp_telp" class="form-label">No. Telepon CP</label>
                <input type="text" class="form-control" name="cp_telp" id="cp_telp" value="{{$dataP->cp_telp}}">
            </div>
            <div class="col-md-6 mb-3">
                <label for="cp_email" class="form-label">Email CP</label>
                <input type="email" class="form-control" name="cp_email" id="cp_email" value="{{$dataP->cp_email}}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="id_mesin" class="form-label">ID Mesin</label>
                <input type="text" class="form-control" name="id_mesin" id="id_mesin" value="{{$dataP->id_mesin}}">
            </div>
            <div class="col-md-6 mb-3">
                <label for="deleted_data" class="form-label">Deleted Data</label>
                <input type="text" class="form-control" name="deleted_data" id="deleted_data"
                    value="{{$dataP->deleted_data}}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="tkp" class="form-label">TKP</label>
                <input type="text" class="form-control" name="tkp" id="tkp" value="{{$dataP->tkp}}">
            </div>
            <div class="col-md-6 mb-3">
                <label for="npp" class="form-label">NPP</label>
                <input type="text" class="form-control" name="npp" id="npp" value="{{$dataP->npp}}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection