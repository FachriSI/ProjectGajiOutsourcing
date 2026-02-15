@extends('layouts.main')

@section('title', 'Tambah Penempatan')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-plus me-2 text-primary"></i> Tambah Penempatan (Lengkap)
        </h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data penempatan karyawan lengkap</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Data Karyawan & Penempatan
                    </h5>

                    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
                        action="/tambah-penempatan2">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">Informasi Pribadi</h6>
                                <div class="mb-3">
                                    <label for="osis_id" class="form-label fw-bold text-dark">OSIS ID</label>
                                    <input type="text" class="form-control" name="osis_id" id="osis_id">
                                </div>
                                <div class="mb-3">
                                    <label for="ktp" class="form-label fw-bold text-dark">Nomor KTP</label>
                                    <input type="text" class="form-control" name="ktp" id="ktp">
                                </div>
                                <div class="mb-3">
                                    <label for="nama" class="form-label fw-bold text-dark">Nama Tenaga Kerja</label>
                                    <input type="text" class="form-control" name="nama" id="nama">
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_lahir" class="form-label fw-bold text-dark">Tanggal Lahir</label>
                                    <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark">Jenis Kelamin</label>
                                    <div class="d-flex">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" id="laki" name="jenis_kelamin"
                                                value="L">
                                            <label class="form-check-label" for="laki">Laki-laki</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="perempuan" name="jenis_kelamin"
                                                value="P">
                                            <label class="form-check-label" for="perempuan">Perempuan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="agama" class="form-label fw-bold text-dark">Agama</label>
                                    <select class="form-select select2" name="agama" id="agama">
                                        <option selected>Pilih...</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label fw-bold text-dark">Status</label>
                                    <select class="form-select select2" name="status" id="status">
                                        <option selected>Pilih...</option>
                                        <option value="S">Single</option>
                                        <option value="M">Menikah</option>
                                        <option value="D">Duda</option>
                                        <option value="J">Janda</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label fw-bold text-dark">Alamat</label>
                                    <textarea class="form-control" name="alamat" id="alamat" rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="asal" class="form-label fw-bold text-dark">Asal</label>
                                    <input type="text" class="form-control" name="asal" id="asal">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2">Informasi Pekerjaan</h6>
                                <div class="mb-3">
                                    <label for="perusahaan" class="form-label fw-bold text-dark">Vendor/Perusahaan</label>
                                    <select class="form-select select2" name="perusahaan" id="perusahaan">
                                        <option selected>Pilih Perusahaan</option>
                                        @foreach ($dataP as $item)
                                            <option value="{{$item->perusahaan_id}}">{{$item->perusahaan}}</option>
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
                                        <option value="" selected>Pilih Bidang</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="area" class="form-label fw-bold text-dark">Area Kerja</label>
                                    <select class="form-select select2" name="area" id="area">
                                        <option value="" selected>Pilih Area</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="lokasi" class="form-label fw-bold text-dark">Lokasi</label>
                                    <select class="form-select select2" name="lokasi" id="lokasi">
                                        <option selected>Pilih Lokasi Kerja</option>
                                        @foreach ($dataL as $item)
                                            <option value="{{$item->kode_lokasi}}">{{$item->lokasi}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="jabatan" class="form-label fw-bold text-dark">Jabatan</label>
                                    <select class="form-select select2" name="jabatan" id="jabatan">
                                        <option selected>Pilih Jabatan</option>
                                        @foreach ($dataJ as $item)
                                            <option value="{{$item->kode_jabatan}}">{{$item->jabatan}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="paket" class="form-label fw-bold text-dark">Paket</label>
                                    <select class="form-select select2" name="paket" id="paket">
                                        <option selected>Pilih Paket</option>
                                        @foreach ($dataPk as $item)
                                            <option value="{{$item->kode_paket}}">{{$item->paket}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="harian_shift" class="form-label fw-bold text-dark">Harian/Shift</label>
                                    <select class="form-select select2" name="harian_shift" id="harian_shift">
                                        <option selected>Pilih...</option>
                                        <option value="1">Harian</option>
                                        <option value="2">Shift</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="resiko" class="form-label fw-bold text-dark">Resiko</label>
                                    <select class="form-select select2" name="resiko" id="resiko">
                                        <option value="1">Resiko</option>
                                        <option value="2" selected>Non Resiko</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="suai" class="form-label fw-bold text-dark">Penyesuaian</label>
                                    <select class="form-select select2" name="suai" id="suai">
                                        <option value="10" selected>Pilih Penyesuaian</option>
                                        @foreach ($dataS as $item)
                                            <option value="{{$item->kode_suai}}">{{$item->keterangan}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="quota_jam_real" class="form-label fw-bold text-dark">Quota Jam Real</label>
                                    <input type="text" class="form-control" name="quota_jam_real" id="quota_jam_real">
                                </div>
                                <div class="mb-3">
                                    <label for="tunjangan_masakerja" class="form-label fw-bold text-dark">Tunjangan Masa
                                        Kerja</label>
                                    <input type="text" class="form-control" name="tunjangan_masakerja"
                                        id="tunjangan_masakerja">
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_bekerja" class="form-label fw-bold text-dark">Tanggal
                                        Bekerja</label>
                                    <input type="date" class="form-control" name="tanggal_bekerja" id="tanggal_bekerja">
                                </div>
                            </div>
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

            $('#perusahaan').select2({
                placeholder: "Pilih Perusahaan",
                allowClear: false,
                width: '100%'
            });
            $('#paket').select2({
                placeholder: "Pilih Paket",
                allowClear: false,
                width: '100%'
            });

            // Ketika Unit dipilih
            $('#unit_kerja').change(function () {
                var unit_id = $(this).val();
                $('#bidang').html('<option value="" selected>Pilih Bidang</option>'); // Reset bidang
                $('#area').html('<option value="" selected>Pilih Area</option>'); // Reset area

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
                $('#area').html('<option value="" selected>Pilih Area</option>'); // Reset area

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