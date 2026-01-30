@extends('layouts.main')
@section('title', 'Tambah Resiko')
@section('content')
    <h3 class="mt-4">Tambah Resiko</h3>
    <div class="card mb-4">
        <div class="card-body">
            <form action="/tambah-resiko" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Resiko</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="tunjangan" class="form-label">Tunjangan Resiko</label>
                    <input type="number" class="form-control" id="tunjangan" name="tunjangan" value="0">
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="/resiko" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection