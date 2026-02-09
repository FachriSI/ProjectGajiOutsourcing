@extends('layouts.main')

@section('title', 'Tambah Data Lebaran')

@section('content')

<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Tambah Data Lebaran</h5>
            </div>
            <div class="card-body">
                <form method="post" action="{{ url('tambah-lebaran') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun</label>
                        <select class="form-select" name="tahun" id="tahun" required>
                            @php
                                $currentYear = date('Y');
                                $startYear = $currentYear - 2;
                                $endYear = $currentYear + 5;
                            @endphp
                            @for ($i = $startYear; $i <= $endYear; $i++)
                                <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('tahun')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal Idul Fitri</label>
                        <input type="date" class="form-control" name="tanggal" id="tanggal" required>
                        @error('tanggal')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Optional)</label>
                        <textarea class="form-control" name="keterangan" id="keterangan" rows="3"></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ url('lebaran') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
