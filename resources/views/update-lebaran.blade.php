@extends('layouts.main')

@section('title', 'Update Data Lebaran')

@section('content')

<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Update Data Lebaran</h5>
            </div>
            <div class="card-body">
                <form method="post" action="{{ url('update-lebaran/' . $data->id) }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tahun" class="form-label">Tahun Masehi</label>
                            <input type="text" class="form-control" name="tahun_display" value="{{ $data->tahun }}" disabled>
                            <input type="hidden" name="tahun" value="{{ $data->tahun }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tahun_hijriyah" class="form-label">Tahun Hijriyah</label>
                            <input type="text" class="form-control" value="{{ $data->tahun_hijriyah }}" disabled>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal Idul Fitri</label>
                        <input type="date" class="form-control" name="tanggal" id="tanggal" value="{{ $data->tanggal->format('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Optional)</label>
                        <textarea class="form-control" name="keterangan" id="keterangan" rows="3">{{ $data->keterangan }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ url('lebaran') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
