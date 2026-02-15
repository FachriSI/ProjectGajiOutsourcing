@extends('layouts.main')

@section('title', 'UMP')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-warning border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit me-2 text-warning"></i> Update UMP</h1>
        <p class="text-muted small mb-0 mt-1">Perbarui informasi data upah minimum</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-warning border-4">
                <div class="card-body">
                    <h5 class="card-title text-warning fw-bold mb-4">
                        <i class="fas fa-edit me-2"></i>Edit Data UMP
                    </h5>

                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
                        action="/update-ump/{{$data->id}}">
                        @csrf
                        <div class="mb-3">
                            <label for="ump" class="form-label fw-bold text-dark">UMP/UMK {{ $data->lokasi }}</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control uang" name="ump" id="ump" value="{{ $data->ump}}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tahun" class="form-label fw-bold text-dark">Tahun</label>
                            <select class="form-select" name="tahun" id="tahun">
                                @php
                                    $currentYear = date('Y');
                                    $startYear = 2022;
                                    $endYear = $currentYear + 5;
                                @endphp
                                @for ($i = $startYear; $i <= $endYear; $i++)
                                    <option value="{{ $i }}" {{ (isset($data->tahun) && $data->tahun == $i) ? 'selected' : '' }}>
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/ump" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning text-white px-4">
                                <i class="fas fa-save me-2"></i>Update UMP
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