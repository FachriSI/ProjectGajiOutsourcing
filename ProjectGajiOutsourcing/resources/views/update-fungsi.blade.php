@extends('layouts.main')

@section('title', 'Update Fungsi')

@section('content')

    <h3 class="mt-4">Update Fungsi</h3>

    <div class="card mb-4">
        <div class="card-body">
            <form action="/update-fungsi/{{ $dataF->kode_fungsi }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Fungsi</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="{{ $dataF->fungsi }}" required>
                </div>
                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea class="form-control" id="keterangan" name="keterangan"
                        rows="3">{{ $dataF->keterangan }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="/fungsi" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

@endsection