@extends('layouts.main')

@section('title', 'Tambah UMP')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-money-bill-wave me-2 text-primary"></i> Tambah UMP</h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data upah minimum provinsi/kota</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Data UMP Baru
                    </h5>

                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
                        action="/tambah-ump">
                        @csrf
                        <div class="mb-3">
                            <label for="kode_lokasi" class="form-label fw-bold text-dark">Lokasi <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" name="kode_lokasi" id="kode_lokasi" required>
                                <option value="" selected disabled>Pilih Lokasi</option>
                                @foreach ($data as $item)
                                    <option value="{{$item->kode_lokasi}}">{{$item->lokasi}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ump" class="form-label fw-bold text-dark">Nilai UMP/UMK <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control uang" name="ump" id="ump" placeholder="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tahun" class="form-label fw-bold text-dark">Tahun <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" name="tahun" id="tahun" required>
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

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/ump" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan UMP
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
            $('.select2').select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: "Pilih...",
            });
        });
    </script>
@endsection