@extends('layouts.main')

@section('title', 'Tambah Fungsi')

@section('content')

    <h3 class="mt-4">Tambah Fungsi</h3>

    <div class="card mb-4">
        <div class="card-body">
            <form action="/tambah-fungsi" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Fungsi</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="/fungsi" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

@endsection