@extends('layouts.main')
@section('title', 'Update Harian/Shift')
@section('content')
    <h3 class="mt-4">Update Harian/Shift</h3>
    <div class="card mb-4">
        <div class="card-body">
            <form action="/update-harianshift/{{ $dataH->kode_harianshift }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Harian/Shift</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="{{ $dataH->harianshift }}"
                        required>
                </div>
                <div class="mb-3">
                    <label for="tunjangan" class="form-label">Tunjangan Shift</label>
                    <input type="number" class="form-control" id="tunjangan" name="tunjangan"
                        value="{{ $dataH->tunjangan_shift }}">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="/harianshift" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection