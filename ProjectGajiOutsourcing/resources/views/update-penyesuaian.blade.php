@extends('layouts.main')
@section('title', 'Update Penyesuaian')
@section('content')
    <h3 class="mt-4">Update Penyesuaian</h3>
    <div class="card mb-4">
        <div class="card-body">
            <form action="/update-penyesuaian/{{ $dataP->kode_suai }}" method="POST">@csrf
                <div class="mb-3"><label class="form-label">Keterangan</label><input type="text" class="form-control"
                        name="keterangan" value="{{ $dataP->keterangan }}" required></div>
                <div class="mb-3"><label class="form-label">Tunjangan Penyesuaian</label><input type="number"
                        class="form-control" name="tunjangan" value="{{ $dataP->tunjangan_penyesuaian }}"></div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="/penyesuaian" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection