@extends('layouts.main')
@section('title', 'Update Pakaian')
@section('content')
    <h3 class="mt-4">Update Pakaian</h3>
    <div class="card mb-4">
        <div class="card-body">
            <form action="/update-pakaian/{{ $dataP->pakaian_id }}" method="POST">@csrf
                <div class="mb-3"><label class="form-label">Karyawan</label>
                    <select class="form-select" name="karyawan_id" required>
                        @foreach ($karyawan as $k)<option value="{{ $k->karyawan_id }}" {{ $k->karyawan_id == $dataP->karyawan_id ? 'selected' : '' }}>{{ $k->nama_tk }}</option>@endforeach
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Nilai Jatah</label><input type="number" class="form-control"
                        name="nilai_jatah" value="{{ $dataP->nilai_jatah }}"></div>
                <div class="mb-3"><label class="form-label">Ukuran Baju</label><input type="text" class="form-control"
                        name="ukuran_baju" value="{{ $dataP->ukuran_baju }}"></div>
                <div class="mb-3"><label class="form-label">Ukuran Celana</label><input type="text" class="form-control"
                        name="ukuran_celana" value="{{ $dataP->ukuran_celana }}"></div>
                <div class="mb-3"><label class="form-label">Tanggal Mulai</label><input type="date" class="form-control"
                        name="beg_date" value="{{ $dataP->beg_date }}"></div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="/pakaian" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection