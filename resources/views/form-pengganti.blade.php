@extends('layouts.main')

@section('title', 'Penempatan')

@section('content')

<h3 class="mt-4">Tambah Pengganti</h3>
<form id="form-pengganti" class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/simpan-pengganti/{{$dataM->karyawan_id}}">
@csrf
    <div class="card bg-light mb-4">
        <div class="card-body">
             <h5 class="card-title text-muted mb-3"><i class="fas fa-user-clock me-2"></i>Data Karyawan Sebelumnya</h5>
             <div class="row">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nama Karyawan</label>
                    <input type="text" class="form-control" value="{{ $dataM->nama_tk }}" readonly disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">OSIS ID</label>
                    <input type="text" class="form-control" value="{{ $dataM->osis_id }}" readonly disabled>
                </div>
             </div>
        </div>
    </div>

    <input type="hidden" name="karyawan_sebelumnya_id" value="{{ $dataM->karyawan_id }}">

    <div class="row">
        <!-- Kolom Kiri: Data Personal -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Data Personal</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nama" class="form-label fw-bold">Nama Tenaga Kerja <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" id="nama" value="{{ old('nama') }}" required>
                    </div>
                    <div class="row">
                         <div class="col-md-6 mb-3">
                            <label for="osis_id" class="form-label fw-bold">OSIS ID (Pengganti) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="osis_id" id="osis_id" maxlength="4" pattern="\d{4}" title="Harus 4 digit angka" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" value="{{ old('osis_id') }}" required>
                            <div class="form-text small">Wajib 4 digit angka.</div>
                            <div id="osis-feedback" class="invalid-feedback">
                                OSIS ID sudah terdaftar.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ktp" class="form-label fw-bold">Nomor KTP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="ktp" id="ktp" maxlength="16" pattern="\d{16}" title="Harus 16 digit angka" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" value="{{ old('ktp') }}" required>
                            <div class="form-text small">Wajib 16 digit angka.</div>
                            <div id="ktp-feedback" class="invalid-feedback">
                                Nomor KTP sudah terdaftar.
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal_lahir" class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir"
                            min="{{ date('Y-m-d', strtotime('-56 years')) }}" max="{{ date('Y-m-d', strtotime('-18 years')) }}"
                            value="{{ old('tanggal_lahir') }}"
                            required>
                        <div id="tgl-feedback" class="invalid-feedback">
                            Usia tidak memenuhi syarat (18-56 tahun).
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Kelamin <span class="text-danger">*</span></label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="laki" name="jenis_kelamin" value="L" {{ old('jenis_kelamin') == 'L' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="perempuan" name="jenis_kelamin" value="P" {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="perempuan">Perempuan</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="agama" class="form-label fw-bold">Agama <span class="text-danger">*</span></label>
                            <select class="form-select" name="agama" id="agama" required>
                                <option value="" selected disabled>Pilih...</option>
                                <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
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
                        <label for="alamat" class="form-label fw-bold">Alamat</label>
                        <textarea class="form-control" name="alamat" id="alamat" rows="2">{{ old('alamat') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="asal" class="form-label fw-bold">Asal</label>
                        <input type="text" class="form-control" name="asal" id="asal" value="{{ old('asal') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Data Pekerjaan -->
        <div class="col-md-6">
            <div class="card mb-4">
                 <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-briefcase me-2"></i>Data Pekerjaan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Vendor/Perusahaan</label>
                        <input type="text" class="form-control bg-light" value="{{ $dataM->nama_perusahaan ?? '-' }}" readonly>
                        <div class="form-text">Vendor otomatis mewarisi dari karyawan sebelumnya.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="jabatan" class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                        <select class="form-select select2" name="jabatan" id="jabatan" required>
                            <option value="" selected disabled>Pilih Jabatan</option>
                            @foreach ($dataJ as $item)
                                <option value="{{$item->kode_jabatan}}" {{ old('jabatan') == $item->kode_jabatan ? 'selected' : '' }}>{{$item->jabatan}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_bekerja" class="form-label fw-bold">Tanggal Bekerja (TMT) <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_bekerja" id="tanggal_bekerja" value="{{ old('tanggal_bekerja', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="harianshift" class="form-label fw-bold">Harian/Shift <span class="text-danger">*</span></label>
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
    <div class="d-flex justify-content-between">
        <a href="/penempatan" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Submit</button>
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
            if(tglVal) {
                const selectedDate = new Date(tglVal);
                const minDateStr = tglInput.attr('min');
                const maxDateStr = tglInput.attr('max');
                
                if (minDateStr && maxDateStr) {
                    const minDate = new Date(minDateStr);
                    const maxDate = new Date(maxDateStr);
                    
                    if(selectedDate < minDate || selectedDate > maxDate) {
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
        $('#form-pengganti').on('submit', function(e) {
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
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
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
