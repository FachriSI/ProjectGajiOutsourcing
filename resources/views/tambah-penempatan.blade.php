@extends('layouts.main')

@section('title', 'Tambah Penempatan')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-check me-2 text-primary"></i> Tambah Penempatan</h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data penempatan karyawan</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Data Penempatan Baru
                    </h5>

                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
                        action="/tambah-penempatan">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_tk" class="form-label fw-bold text-dark">Nama Tenaga Kerja</label>
                            <select class="form-select select2" name="nama_tk" id="nama_tk">
                                <option selected>Pilih Tenaga Kerja</option>
                                @foreach ($dataK as $item)
                                    <option value="{{$item->karyawan_id}}">{{$item->nama_tk}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="unit_kerja" class="form-label fw-bold text-dark">Unit Kerja</label>
                            <select class="form-select select2" name="unit_kerja" id="unit_kerja">
                                <option selected>Pilih Unit Kerja</option>
                                @foreach ($dataU as $item)
                                    <option value="{{$item->unit_id}}">{{$item->unit_kerja}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bidang" class="form-label fw-bold text-dark">Bidang Kerja</label>
                            <select class="form-select select2" name="bidang" id="bidang">
                                <option selected>Pilih Bidang</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="area" class="form-label fw-bold text-dark">Area Kerja</label>
                            <select class="form-select select2" name="area" id="area">
                                <option selected>Pilih Area</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_bekerja" class="form-label fw-bold text-dark">Tanggal Bekerja</label>
                            <input type="date" class="form-control" name="tanggal_bekerja" id="tanggal_bekerja">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/penempatan" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Penempatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Inisialisasi Select2
            $('.select2').select2({ width: '100%' });

            $('#nama_tk').select2({
                placeholder: "Pilih Tenaga Kerja",
                allowClear: false,
                width: '100%'
            });

            // Ketika Unit dipilih
            $('#unit_kerja').change(function () {
                var unit_id = $(this).val();
                $('#bidang').html('<option selected>Pilih Bidang</option>'); // Reset bidang
                $('#area').html('<option selected>Pilih Area</option>'); // Reset area

                if (unit_id) {
                    $.ajax({
                        url: '/get-bidang/' + unit_id, // Endpoint mengambil bidang berdasarkan unit
                        type: 'GET',
                        success: function (data) {
                            if (data.length > 0) {
                                $.each(data, function (key, value) {
                                    $('#bidang').append('<option value="' + value.bidang_id + '">' + value.bidang + '</option>');
                                });
                            }
                        }
                    });
                }
            });

            // Ketika Bidang dipilih
            $('#bidang').change(function () {
                var bidang_id = $(this).val();
                $('#area').html('<option selected>Pilih Area</option>'); // Reset area

                if (bidang_id) {
                    $.ajax({
                        url: '/get-area/' + bidang_id, // Endpoint mengambil area berdasarkan bidang
                        type: 'GET',
                        success: function (data) {
                            if (data.length > 0) {
                                $.each(data, function (key, value) {
                                    $('#area').append('<option value="' + value.area_id + '">' + value.area + '</option>');
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection