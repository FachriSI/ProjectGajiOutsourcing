@extends('layouts.main')

@section('title', 'Detail Karyawan')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user me-2 text-primary"></i> Detail Karyawan</h1>
        <p class="text-muted small mb-0 mt-1">Informasi lengkap data karyawan</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-id-card me-2"></i>Data Personal
                    </h5>

                    <div class="mb-3">
                        <label for="osis_id" class="form-label fw-bold text-dark">OSIS ID</label>
                        <input type="text" class="form-control" name="osis_id" id="osis_id" value="{{$dataM->osis_id}}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nomor-ktp" class="form-label fw-bold text-dark">Nomor KTP</label>
                        <input type="text" class="form-control" name="ktp" id="nomor-ktp" value="{{$dataM->ktp}}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label fw-bold text-dark">Nama Tenaga Kerja</label>
                        <input type="text" class="form-control fw-bold text-primary" name="nama" id="nama" value="{{$dataM->nama_tk}}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" for="perusahaan">Vendor/Perusahaan</label>
                        <select class="form-select" aria-label="Default select example" name="perusahaan" disabled>
                            <option selected>Pilih Perusahaan</option>
                            @foreach ($dataP as $item)
                                <option value="{{ $item->perusahaan_id }}" 
                                    {{ $item->perusahaan_id == $dataM->perusahaan_id ? 'selected' : '' }}>
                                    {{ $item->perusahaan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal-lahir" class="form-label fw-bold text-dark">Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal-lahir" value="{{$dataM->tanggal_lahir}}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label fw-bold text-dark">Alamat</label>
                        <input type="text" class="form-control" name="alamat" id="alamat" value="{{$dataM->alamat}}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="kota" class="form-label fw-bold text-dark">Asal</label>
                        <input type="text" class="form-control" name="kota" id="kota" value="{{$dataM->asal}}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Jenis Kelamin</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="laki" name="jenis_kelamin" value="L" disabled
                                {{ $dataM->jenis_kelamin == 'L' ? 'checked' : '' }}>
                            <label class="form-check-label" for="laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="perempuan" name="jenis_kelamin" value="P" disabled
                                {{ $dataM->jenis_kelamin == 'P' ? 'checked' : '' }}>
                            <label class="form-check-label" for="perempuan">Perempuan</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="agama" class="form-label fw-bold text-dark">Agama</label>
                        <select class="form-select" aria-label="Default select example" name="agama" disabled>
                            <option disabled>Pilih...</option>
                            <option value="islam" {{ $dataM->agama == 'islam' ? 'selected' : '' }}>Islam</option>
                            <option value="kristen" {{ $dataM->agama == 'kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="hindu" {{ $dataM->agama == 'hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="buddha" {{ $dataM->agama == 'buddha' ? 'selected' : '' }}>Buddha</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold text-dark">Status</label>
                        <select class="form-select" aria-label="Default select example" name="status" id="status" disabled>
                            <option selected>Pilih...</option>
                            <option value="S" {{ $dataM->status == 'S' ? 'selected' : '' }}>Single</option>
                            <option value="M" {{ $dataM->status == 'M' ? 'selected' : '' }}>Menikah</option>
                            <option value="D" {{ $dataM->status == 'D' ? 'selected' : '' }}>Duda</option>
                            <option value="J" {{ $dataM->status == 'J' ? 'selected' : '' }}>Janda</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tahun_pensiun" class="form-label fw-bold text-dark">Tahun Pensiun</label>
                        <input type="text" class="form-control" name="tahun_pensiun" id="tahun_pensiun" value="{{$dataM->tahun_pensiun}}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_pensiun" class="form-label fw-bold text-dark">Tanggal Pensiun</label>
                        <input type="text" class="form-control" name="tanggal_pensiun" id="tanggal_pensiun" value="{{$dataM->tanggal_pensiun}}" readonly>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="/karyawan" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection