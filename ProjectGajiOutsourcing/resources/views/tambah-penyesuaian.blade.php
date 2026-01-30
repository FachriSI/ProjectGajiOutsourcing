@extends('layouts.main')
@section('title', 'Tambah Penyesuaian')
@section('content')
    <h3 class="mt-4">Tambah Penyesuaian</h3>
    <div class="card mb-4">
        <div class="card-body">
            <form action="/tambah-penyesuaian" method="POST">@csrf
                <div class="mb-3"><label class="form-label">Keterangan</label><input type="text" class="form-control"
                        name="keterangan" required></div>
                <div class="mb-3"><label class="form-label">Tunjangan Penyesuaian</label><input type="number"
                        class="form-control" name="tunjangan" value="0"></div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="/penyesuaian" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection