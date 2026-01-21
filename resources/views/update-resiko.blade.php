@extends('layouts.main')
@section('title', 'Update Resiko')
@section('content')
    <h3 class="mt-4">Update Resiko</h3>
    <div class="card mb-4">
        <div class="card-body">
            <form action="/update-resiko/{{ $dataR->kode_resiko }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Resiko</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="{{ $dataR->resiko }}" required>
                </div>
                <div class="mb-3">
                    <label for="tunjangan" class="form-label">Tunjangan Resiko</label>
                    <input type="number" class="form-control" id="tunjangan" name="tunjangan"
                        value="{{ $dataR->tunjangan_resiko }}">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="/resiko" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection