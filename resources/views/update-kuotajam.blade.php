@extends('layouts.main')
@section('title', 'Update Kuota Jam')
@section('content')
    <h3 class="mt-4">Update Kuota Jam</h3>
    <div class="card mb-4">
        <div class="card-body">
            <form action="/update-kuotajam/{{ $dataK->kuota_id }}" method="POST">@csrf
                <div class="mb-3"><label class="form-label">Karyawan</label>
                    <select class="form-select" name="karyawan_id" required>
                        @foreach ($karyawan as $k)<option value="{{ $k->karyawan_id }}" {{ $k->karyawan_id == $dataK->karyawan_id ? 'selected' : '' }}>{{ $k->nama_tk }}</option>@endforeach
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Kuota</label><input type="number" class="form-control"
                        name="kuota" value="{{ $dataK->kuota }}" required></div>
                <div class="mb-3"><label class="form-label">Tanggal Mulai</label><input type="date" class="form-control"
                        name="beg_date" value="{{ $dataK->beg_date }}"></div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="/kuotajam" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection