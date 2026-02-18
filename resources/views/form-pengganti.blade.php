@extends('layouts.main')

@section('title', 'Penempatan')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-clock me-2 text-primary"></i> Tambah Pengganti</h1>
        <p class="text-muted small mb-0 mt-1">Formulir penggantian tenaga kerja</p>
    </div>

    <form id="form-pengganti" class="form-horizontal form-bordered" method="post" enctype="multipart/form-data"
        action="/simpan-pengganti/{{$dataM->karyawan_id}}">
        @csrf

        <div class="card bg-white shadow-sm border-0 mb-4 border-top border-secondary border-4">
            <div class="card-body">
                <h5 class="card-title text-secondary fw-bold mb-3"><i class="fas fa-history me-2"></i>Data Karyawan
                    Sebelumnya</h5>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Nama Karyawan</label>
                        <input type="text" class="form-control bg-light" value="{{ $dataM->nama_tk }}" readonly disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">OSIS ID</label>
                        <input type="text" class="form-control bg-light" value="{{ $dataM->osis_id }}" readonly disabled>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="karyawan_sebelumnya_id" value="{{ $dataM->karyawan_id }}">
        <input type="hidden" name="perusahaan_id" value="{{ $dataM->perusahaan_id }}">
        <input type="hidden" name="paket" value="{{ $paketTerakhir->paket_id ?? '' }}">
        <input type="hidden" name="lokasi" value="{{ $lokasiTerakhir->kode_lokasi ?? '' }}">
        <input type="hidden" name="quota_jam_real" value="{{ $quotaJam->kuota ?? 0 }}">

        <div class="row">
            <!-- Kolom Kiri: Data Personal -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary fw-bold mb-4">
                            <i class="fas fa-user me-2"></i>Data Personal
                        </h5>

                        <div class="mb-3">
                            <label for="nama" class="form-label fw-bold text-dark">Nama Tenaga Kerja <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" id="nama" value="{{ old('nama') }}"
                                required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="osis_id" class="form-label fw-bold text-dark">OSIS ID (Pengganti) <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="osis_id" id="osis_id" maxlength="4"
                                    pattern="\d{4}" title="Harus 4 digit angka"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                    value="{{ old('osis_id') }}" required>
                                <div class="form-text small">Wajib 4 digit angka.</div>
                                <div id="osis-feedback" class="invalid-feedback">
                                    OSIS ID sudah terdaftar.
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ktp" class="form-label fw-bold text-dark">Nomor KTP <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ktp" id="ktp" maxlength="16" pattern="\d{16}"
                                    title="Harus 16 digit angka"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                    value="{{ old('ktp') }}" required>
                                <div class="form-text small">Wajib 16 digit angka.</div>
                                <div id="ktp-feedback" class="invalid-feedback">
                                    Nomor KTP sudah terdaftar.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_lahir" class="form-label fw-bold text-dark">Tanggal Lahir <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir"
                                min="{{ date('Y-m-d', strtotime('-56 years')) }}"
                                max="{{ date('Y-m-d', strtotime('-18 years')) }}" value="{{ old('tanggal_lahir') }}"
                                required>
                            <div id="tgl-feedback" class="invalid-feedback">
                                Usia tidak memenuhi syarat (18-56 tahun).
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Jenis Kelamin <span
                                    class="text-danger">*</span></label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="laki" name="jenis_kelamin" value="L" {{ old('jenis_kelamin') == 'L' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="laki">Laki-laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="perempuan" name="jenis_kelamin" value="P"
                                    {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="perempuan">Perempuan</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="agama" class="form-label fw-bold text-dark">Agama <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="agama" id="agama" required>
                                    <option value="" selected disabled>Pilih...</option>
                                    <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                    <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                    <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                    <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-bold text-dark">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="status" required>
                                    <option value="" selected disabled>Pilih...</option>
                                    <option value="S" {{ old('status') == 'S' ? 'selected' : '' }}>Single</option>
                                    <option value="M" {{ old('status') == 'M' ? 'selected' : '' }}>Menikah</option>
                                    <option value="D" {{ old('status') == 'D' ? 'selected' : '' }}>Duda</option>
                                    <option value="J" {{ old('status') == 'J' ? 'selected' : '' }}>Janda</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label fw-bold text-dark">Alamat</label>
                            <textarea class="form-control" name="alamat" id="alamat" rows="2">{{ old('alamat') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="asal" class="form-label fw-bold text-dark">Asal</label>
                            <input type="text" class="form-control" name="asal" id="asal" value="{{ old('asal') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Data Pekerjaan -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary fw-bold mb-4">
                            <i class="fas fa-briefcase me-2"></i>Data Pekerjaan
                        </h5>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Vendor/Perusahaan</label>
                            <input type="text" class="form-control bg-light" value="{{ $dataM->nama_perusahaan ?? '-' }}"
                                readonly>
                            <div class="form-text">Vendor otomatis mewarisi dari karyawan sebelumnya.</div>
                        </div>

                        <div class="mb-3">
                            <label for="jabatan" class="form-label fw-bold text-dark">Jabatan <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" name="jabatan" id="jabatan" required>
                                <option value="" selected disabled>Pilih Jabatan</option>
                                @foreach ($dataJ as $item)
                                    <option value="{{$item->kode_jabatan}}" {{ old('jabatan') == $item->kode_jabatan ? 'selected' : '' }}>{{$item->jabatan}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_bekerja" class="form-label fw-bold text-dark">Tanggal Bekerja (TMT) <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_bekerja" id="tanggal_bekerja"
                                value="{{ old('tanggal_bekerja', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="harianshift" class="form-label fw-bold text-dark">Harian/Shift <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="harianshift" id="harianshift" required>
                                <option value="" selected disabled>Pilih...</option>
                                <option value="1" {{ old('harianshift') == '1' ? 'selected' : '' }}>Harian</option>
                                <option value="2" {{ old('harianshift') == '2' ? 'selected' : '' }}>Shift</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
            <a href="/penempatan" class="btn btn-outline-secondary me-md-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-2"></i>Submit
            </button>
        </div>
    </form>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            const tglInput = $('#tanggal_lahir');
            const tglFeedback = $('#tgl-feedback');
            const submitBtn = $('button[type="submit"]');

            const osisInput = $('#osis_id');
            const ktpInput = $('#ktp');
            const osisFeedback = $('#osis-feedback');
            const ktpFeedback = $('#ktp-feedback');

            // Data from controller
            const existingOsis = @json($existingOsis ?? []);
            const existingKtp = @json($existingKtp ?? []);

            function validateForm() {
                let isValid = true;

                // Validate OSIS
                const osisVal = osisInput.val();
                if (osisVal.length > 0) {
                    if (existingOsis.includes(parseInt(osisVal)) || existingOsis.includes(osisVal)) {
                        osisInput.addClass('is-invalid').removeClass('is-valid');
                        osisFeedback.show();
                        isValid = false;
                    } else {
                        osisInput.removeClass('is-invalid').addClass('is-valid');
                        osisFeedback.hide();
                    }
                }

                // Validate KTP
                const ktpVal = ktpInput.val();
                if (ktpVal.length > 0) {
                    if (existingKtp.includes(ktpVal) || existingKtp.includes(parseInt(ktpVal))) {
                        ktpInput.addClass('is-invalid').removeClass('is-valid');
                        ktpFeedback.show();
                        isValid = false;
                    } else {
                        ktpInput.removeClass('is-invalid').addClass('is-valid');
                        ktpFeedback.hide();
                    }
                }

                // Validate Date (existing logic)
                const tglVal = tglInput.val();
                if (tglVal) {
                    const selectedDate = new Date(tglVal);
                    const minDateStr = tglInput.attr('min');
                    const maxDateStr = tglInput.attr('max');

                    if (minDateStr && maxDateStr) {
                        const minDate = new Date(minDateStr);
                        const maxDate = new Date(maxDateStr);

                        if (selectedDate < minDate || selectedDate > maxDate) {
                            tglInput.addClass('is-invalid').removeClass('is-valid');
                            tglFeedback.show();
                            isValid = false;
                        } else {
                            tglInput.removeClass('is-invalid').addClass('is-valid');
                            tglFeedback.hide();
                        }
                    }
                }

                // Initial Check for duplicates
                const anyInvalid = $('.is-invalid').length > 0;
                submitBtn.prop('disabled', anyInvalid);
            }

            // Submit Handler with SweetAlert
            $('#form-pengganti').on('submit', function (e) {
                e.preventDefault();

                // Re-validate one last time
                validateForm();
                if (submitBtn.prop('disabled')) {
                    return false;
                }

                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Karyawan lama akan dinonaktifkan dan digantikan dengan data baru ini.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');
                        this.submit();
                    }
                });
            });

            tglInput.on('change', validateForm);
            osisInput.on('input', validateForm);
            ktpInput.on('input', validateForm);
        });
    </script>
@endpush