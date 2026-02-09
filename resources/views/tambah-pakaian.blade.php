@extends('layouts.main')
@section('title', 'Tambah Pakaian')
@section('content')
    <h3 class="mt-4">Tambah Pakaian</h3>
    <div class="card mb-4">
        <div class="card-body">
            <form action="/tambah-pakaian" method="POST">@csrf
                <div class="mb-3"><label class="form-label">Karyawan</label>
                    <select class="form-select select2" name="karyawan_id" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach ($karyawan as $k)<option value="{{ $k->karyawan_id }}">{{ $k->nama_tk }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Nilai Jatah</label><input type="number" class="form-control"
                        name="nilai_jatah" value="0"></div>
                <div class="mb-3"><label class="form-label">Ukuran Baju</label>
                    <select class="form-select select2" name="ukuran_baju">
                        <option value="">-- Pilih Ukuran --</option>
                        @foreach ($masterUkuran as $m)
                            <option value="{{ $m->nama_ukuran }}">{{ $m->nama_ukuran }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Ukuran Celana</label><input type="number" class="form-control"
                        name="ukuran_celana" min="25" max="45"></div>
                <div class="mb-3"><label class="form-label">Tanggal Mulai</label><input type="date" class="form-control"
                        name="beg_date"></div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="/pakaian" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection