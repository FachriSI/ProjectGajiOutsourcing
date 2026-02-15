@extends('layouts.main')

@section('title', 'Tambah UMP')

@section('content')

    <h3 class="mt-4">Tambah UMP</h3>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Data UMP Baru</h6>
                </div>
                <div class="card-body">
                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-ump">
                        @csrf
                        <div class="mb-3">
                            <label for="kode_lokasi" class="form-label fw-bold">Lokasi <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="kode_lokasi" id="kode_lokasi" required>
                                <option value="" selected disabled>Pilih Lokasi</option>
                                @foreach ($data as $item)
                                    <option value="{{$item->kode_lokasi}}">{{$item->lokasi}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ump" class="form-label fw-bold">Nilai UMP/UMK <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control uang" name="ump" id="ump" placeholder="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tahun" class="form-label fw-bold">Tahun <span class="text-danger">*</span></label>
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
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/ump" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan UMP
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('.uang').mask('000.000.000.000', {reverse: true});
            $('.select2').select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: $(this).data('placeholder'),
            });
        });
    </script>
@endsection
