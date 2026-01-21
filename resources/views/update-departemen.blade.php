@extends('layouts.main')

@section('title', 'Update Departemen')

@section('content')

    <h3 class="mt-4">Update Departemen</h3>

    <div class="card mb-4">
        <div class="card-body">
            <form action="/update-departemen/{{ $dataD->departemen_id }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Departemen</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="{{ $dataD->departemen }}" required>
                </div>
                <div class="mb-3">
                    <label for="is_si" class="form-label">Is SI</label>
                    <select class="form-select" id="is_si" name="is_si">
                        <option value="0" {{ $dataD->is_si == 0 ? 'selected' : '' }}>Tidak</option>
                        <option value="1" {{ $dataD->is_si == 1 ? 'selected' : '' }}>Ya</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="/departemen" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

@endsection