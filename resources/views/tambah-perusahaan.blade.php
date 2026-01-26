@extends('layouts.main')

@section('title', 'Perusahaan')

@section('content')
    <h3 class="mt-4">Tambah Perusahaan</h3>
    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-perusahaan">
        <!-- Input Date Range -->
        @csrf
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Perusahaan</label>
            <input type="text" class="form-control" name="nama" id="nama" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" name="alamat" id="alamat" rows="3"></textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="cp" class="form-label">Contact Person (CP)</label>
                <input type="text" class="form-control" name="cp" id="cp">
            </div>
            <div class="col-md-6 mb-3">
                <label for="cp_jab" class="form-label">Jabatan CP</label>
                <input type="text" class="form-control" name="cp_jab" id="cp_jab">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="cp_telp" class="form-label">No. Telepon CP</label>
                <input type="text" class="form-control" name="cp_telp" id="cp_telp">
            </div>
            <div class="col-md-6 mb-3">
                <label for="cp_email" class="form-label">Email CP</label>
                <input type="email" class="form-control" name="cp_email" id="cp_email">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="id_mesin" class="form-label">ID Mesin</label>
                <input type="text" class="form-control" name="id_mesin" id="id_mesin">
            </div>
            <!-- Deleted Data biasanya tidak diinput manual saat create, default aktif -->
            <div class="col-md-6 mb-3">
                <label for="tkp" class="form-label">TKP</label>
                <input type="text" class="form-control" name="tkp" id="tkp">
            </div>
        </div>
        <div class="mb-3">
            <label for="npp" class="form-label">NPP</label>
            <input type="text" class="form-control" name="npp" id="npp">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

@endsection