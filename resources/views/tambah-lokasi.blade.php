@extends('layouts.main')
@section('title', 'Tambah Lokasi')
@section('content')
    <h3 class="mt-4">Tambah Lokasi</h3>
    <div class="card mb-4">
        <div class="card-body">
            <form action="/tambah-lokasi" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lokasi</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="jenis" class="form-label">Jenis</label>
                    <input type="text" class="form-control" id="jenis" name="jenis">
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="/lokasi" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection