@extends('layouts.main')

@section('title', 'Update Data Lebaran')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4"><i class="fas fa-calendar-check me-2"></i>Update Data Lebaran</h1>
        
        <div class="card mb-4 shadow-sm border-0 mt-3">
            <div class="card-header">
                <i class="fas fa-edit me-1"></i>
                Form Update Lebaran
            </div>
            <div class="card-body">
                <form action="/update-lebaran/{{ $lebaran->id }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun (Masehi)</label>
                        <input type="number" class="form-control @error('tahun') is-invalid @enderror" id="tahun" name="tahun" value="{{ old('tahun', $lebaran->tahun) }}" required>
                        @error('tahun')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal Lebaran (Idul Fitri)</label>
                        <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $lebaran->tanggal) }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="tahun_hijriyah" class="form-label">Tahun Hijriyah (Opsional)</label>
                        <input type="text" class="form-control" id="tahun_hijriyah" name="tahun_hijriyah" placeholder="Contoh: 1447 H" value="{{ old('tahun_hijriyah', $lebaran->tahun_hijriyah) }}">
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $lebaran->keterangan) }}</textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save me-1"></i> Update</button>
                    <a href="/lebaran" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
