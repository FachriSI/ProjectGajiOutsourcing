@extends('layouts.main')
@section('title', 'Tambah Jabatan')
@section('content')
    <h3 class="mt-4">Tambah Jabatan</h3>
    <div class="card mb-4">
        <div class="card-body">
            <form action="/tambah-jabatan" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Jabatan</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="tunjangan" class="form-label">Tunjangan Jabatan</label>
                    <input type="number" class="form-control" id="tunjangan" name="tunjangan" value="0">
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="/jabatan" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection