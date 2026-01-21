@extends('layouts.main')
@section('title', 'Update Lokasi')
@section('content')
    <h3 class="mt-4">Update Lokasi</h3>
    <div class="card mb-4">
        <div class="card-body">
            <form action="/update-lokasi/{{ $dataL->kode_lokasi }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lokasi</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="{{ $dataL->lokasi }}" required>
                </div>
                <div class="mb-3">
                    <label for="jenis" class="form-label">Jenis</label>
                    <input type="text" class="form-control" id="jenis" name="jenis" value="{{ $dataL->jenis }}">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="/lokasi" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection