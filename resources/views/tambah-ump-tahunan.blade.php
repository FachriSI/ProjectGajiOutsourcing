@extends('layouts.main')

@section('title', 'UMP Tahunan')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-calendar-alt me-2 text-primary"></i> Tambah UMP Tahunan (Massal)
        </h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data UMP untuk semua lokasi secara massal</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Input Data UMP Tahunan
                    </h5>

                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
                        action="/tambah-ump-tahunan">
                        @csrf

                        <div class="mb-4">
                            <label for="tahun" class="form-label fw-bold text-dark">Tahun Berlaku</label>
                            <select class="form-select" name="tahun" id="tahun">
                                @php
                                    $currentYear = date('Y');
                                    $startYear = 2022;
                                    $endYear = $currentYear + 5;
                                @endphp
                                @for ($i = $startYear; $i <= $endYear; $i++)
                                    <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">Daftar Lokasi & Nilai UMP</h6>

                        <div class="row">
                            @foreach ($data as $item)
                                <div class="col-md-6 mb-3">
                                    <label for="ump_{{ $item->kode_lokasi }}"
                                        class="form-label small fw-bold text-muted">UMP/UMK {{ $item->lokasi }}</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control uang" name="ump[{{ $item->kode_lokasi }}]"
                                            id="ump_{{ $item->kode_lokasi }}" placeholder="0">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/ump" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Semua UMP
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.uang').mask('000.000.000.000', { reverse: true });
        });
    </script>
@endsection