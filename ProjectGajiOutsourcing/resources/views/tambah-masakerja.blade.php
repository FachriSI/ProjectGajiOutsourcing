@extends('layouts.main')
@section('title', 'Tambah Masa Kerja')
@section('content')
    <h3 class="mt-4">Tambah Masa Kerja</h3>
    <div class="card mb-4">
        <div class="card-body">
            <form action="/tambah-masakerja" method="POST">@csrf
                <div class="mb-3"><label class="form-label">Karyawan</label>
                    <select class="form-select" name="karyawan_id" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach ($karyawan as $k)<option value="{{ $k->karyawan_id }}">{{ $k->nama_tk }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Tunjangan Masa Kerja</label><input type="number"
                        class="form-control" name="tunjangan_masakerja" required></div>
                <div class="mb-3"><label class="form-label">Tanggal Mulai</label><input type="date" class="form-control"
                        name="beg_date"></div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="/masakerja" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection