@extends('layouts.main')

@section('title', 'Tambah Departemen')

@section('content')

    <h3 class="mt-4">Tambah Departemen</h3>

    <div class="card mb-4">
        <div class="card-body">
            <form action="/tambah-departemen" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Departemen</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="is_si" class="form-label">Is SI</label>
                    <select class="form-select" id="is_si" name="is_si">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="/departemen" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

@endsection