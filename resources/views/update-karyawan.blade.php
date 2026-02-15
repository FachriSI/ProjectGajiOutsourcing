@extends('layouts.main')

@section('title', 'Karyawan')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-warning border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-edit me-2 text-warning"></i> Update Data Karyawan</h1>
        <p class="text-muted small mb-0 mt-1">Perbarui informasi data karyawan</p>
    </div>

    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
        action="/update-karyawan/{{$dataM->karyawan_id}}">
        @csrf
        <div class="row">
            <!-- Kolom Kiri: Data Personal -->
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm border-0 border-top border-warning border-4">
                    <div class="card-body">
                        <h5 class="card-title text-warning fw-bold mb-4">
                            <i class="fas fa-user me-2"></i>Data Personal
                        </h5>

                        <div class="mb-3">
                            <label for="nama" class="form-label fw-bold text-dark">Nama Tenaga Kerja</label>
                            <input type="text" class="form-control" name="nama" id="nama" value="{{$dataM->nama_tk}}"
                                required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="osis_id" class="form-label fw-bold text-dark">OSIS ID</label>
                                <input type="text" class="form-control" name="osis_id" id="osis_id"
                                    value="{{$dataM->osis_id}}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nomor-ktp" class="form-label fw-bold text-dark">Nomor KTP</label>
                                <input type="text" class="form-control" name="ktp" id="nomor-ktp" value="{{$dataM->ktp}}"
                                    autocomplete="off" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal-lahir" class="form-label fw-bold text-dark">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tanggal_lahir" id="tanggal-lahir"
                                value="{{$dataM->tanggal_lahir}}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Jenis Kelamin</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="laki" name="jenis_kelamin" value="L" {{ $dataM->jenis_kelamin == 'L' ? 'checked' : '' }}>
                                <label class="form-check-label" for="laki">Laki-laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="perempuan" name="jenis_kelamin" value="P"
                                    {{ $dataM->jenis_kelamin == 'P' ? 'checked' : '' }}>
                                <label class="form-check-label" for="perempuan">Perempuan</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="agama" class="form-label fw-bold text-dark">Agama</label>
                                <select class="form-select" name="agama" required>
                                    <option disabled>Pilih...</option>
                                    <option value="islam" {{ $dataM->agama == 'islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="kristen" {{ $dataM->agama == 'kristen' ? 'selected' : '' }}>Kristen
                                    </option>
                                    <option value="hindu" {{ $dataM->agama == 'hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="buddha" {{ $dataM->agama == 'buddha' ? 'selected' : '' }}>Buddha</option>
                                    <option value="konghucu" {{ $dataM->agama == 'konghucu' ? 'selected' : '' }}>Konghucu
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-bold text-dark">Status</label>
                                <select class="form-select" name="status" id="status" required>
                                    <option selected disabled>Pilih...</option>
                                    <option value="S" {{ $dataM->status == 'S' ? 'selected' : '' }}>Single</option>
                                    <option value="M" {{ $dataM->status == 'M' ? 'selected' : '' }}>Menikah</option>
                                    <option value="D" {{ $dataM->status == 'D' ? 'selected' : '' }}>Duda</option>
                                    <option value="J" {{ $dataM->status == 'J' ? 'selected' : '' }}>Janda</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label fw-bold text-dark">Alamat</label>
                            <textarea class="form-control" name="alamat" id="alamat" rows="2">{{$dataM->alamat}}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="asal" class="form-label fw-bold text-dark">Asal</label>
                            <input type="text" class="form-control" name="asal" id="asal" value="{{$dataM->asal}}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Data Pekerjaan -->
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm border-0 border-top border-secondary border-4">
                    <div class="card-body">
                        <h5 class="card-title text-secondary fw-bold mb-4">
                            <i class="fas fa-briefcase me-2"></i>Data Pekerjaan
                        </h5>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark" for="perusahaan">Vendor/Perusahaan</label>
                            <select class="form-select select2" name="perusahaan" id="perusahaan" required>
                                <option selected>Pilih Perusahaan</option>
                                @foreach ($dataP as $item)
                                    <option value="{{ $item->perusahaan_id }}" {{ $item->perusahaan_id == $dataM->perusahaan_id ? 'selected' : '' }}>
                                        {{ $item->perusahaan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Hidden inputs kept from original -->
                        <input type="hidden" class="form-control" name="tahun_pensiun" id="tahun_pensiun"
                            value="{{$dataM->tahun_pensiun}}">
                        <input type="hidden" class="form-control" name="tanggal_pensiun" id="tanggal_pensiun"
                            value="{{$dataM->tanggal_pensiun}}">
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning text-white btn-lg shadow-sm">
                        <i class="fas fa-save me-2"></i>Update Data Karyawan
                    </button>
                    <a href="/karyawan" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function () {
            $('#perusahaan').select2({
                placeholder: "Pilih Perusahaan",
                allowClear: false,
                width: '100%'
            });
        });
    </script>
@endsection