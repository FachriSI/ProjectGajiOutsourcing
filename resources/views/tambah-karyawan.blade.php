@extends('layouts.main')

@section('title', 'Karyawan')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-plus me-2 text-primary"></i> Tambah Karyawan</h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data karyawan baru</p>
    </div>

    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-karyawan">
        @csrf
        <div class="row">
            <!-- Kolom Kiri: Data Personal -->
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm border-0 border-top border-primary border-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary fw-bold mb-4">
                            <i class="fas fa-user me-2"></i>Data Personal
                        </h5>

                        <div class="mb-3">
                            <label for="nama" class="form-label fw-bold text-dark">Nama Tenaga Kerja <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" id="nama" value="{{ old('nama') }}" required
                                placeholder="Masukkan nama lengkap">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="osis_id" class="form-label fw-bold text-dark">OSIS ID <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="osis_id" id="osis_id" required
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="{{ old('osis_id') }}" placeholder="4 digit">
                                <div id="osis-feedback" class="invalid-feedback">
                                    OSIS ID harus 4 digit angka dan belum terdaftar.
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ktp" class="form-label fw-bold text-dark">Nomor KTP <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="ktp" id="ktp" required
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="{{ old('ktp') }}" placeholder="16 digit">
                                <div id="ktp-feedback" class="invalid-feedback">
                                    Nomor KTP harus 16 digit angka dan belum terdaftar.
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
                            <textarea class="form-control" name="alamat" id="alamat" rows="2"
                                placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="asal" class="form-label fw-bold text-dark">Asal</label>
                            <input type="text" class="form-control" name="asal" id="asal" value="{{ old('asal') }}"
                                placeholder="Kota asal">
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
                            <label for="perusahaan" class="form-label fw-bold text-dark">Vendor/Perusahaan <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" name="perusahaan" id="perusahaan" required>
                                <option selected value="">Pilih Perusahaan...</option>
                                @foreach ($dataP as $item)
                                    <option value="{{$item->perusahaan_id}}" {{ old('perusahaan') == $item->perusahaan_id ? 'selected' : '' }}>{{$item->perusahaan}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="paket_id" class="form-label fw-bold text-dark">Pilih Paket <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" name="paket_id" id="paket_id" required>
                                <option value="">Pilih Paket...</option>
                                @foreach ($paketList as $paket)
                                    <option value="{{ $paket->paket_id }}" {{ (old('paket_id') == $paket->paket_id || request('paket_id') == $paket->paket_id) ? 'selected' : '' }}>
                                        {{ $paket->paket }} (Sisa Kuota: {{ $paket->sisa_kuota }})
                                    </option>
                                @endforeach
                            </select>
                            @error('paket_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kode_lokasi" class="form-label fw-bold text-dark">Lokasi Kerja <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" name="kode_lokasi" id="kode_lokasi" required>
                                <option value="">Pilih Lokasi...</option>
                                @foreach ($lokasiList as $lokasi)
                                    <option value="{{ $lokasi->kode_lokasi }}" {{ old('kode_lokasi') == $lokasi->kode_lokasi ? 'selected' : '' }}>
                                        {{ $lokasi->lokasi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kode_lokasi')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-save me-2"></i>Simpan Karyawan
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
            $('.select2').select2({
                placeholder: "Pilih...",
                allowClear: false,
                width: '100%'
            });

            // Data from controller
            const existingOsis = @json($existingOsis ?? []);
            const existingKtp = @json($existingKtp ?? []);

            const osisInput = $('#osis_id');
            const ktpInput = $('#ktp');
            const tglInput = $('#tanggal_lahir');
            const submitBtn = $('button[type="submit"]');

            const osisFeedback = $('#osis-feedback');
            const ktpFeedback = $('#ktp-feedback');
            const tglFeedback = $('#tgl-feedback');

            function validateForm() {
                let isValid = true;

                // Validate OSIS ID
                const osisVal = osisInput.val();
                if (osisVal.length > 0) {
                    // Check if numeric (HTML5 type=number handles non-numeric chars mostly, but let's be safe)
                    if (osisVal.length !== 4) {
                        osisInput.addClass('is-invalid').removeClass('is-valid');
                        osisFeedback.text('OSIS ID harus 4 digit angka.').show();
                        isValid = false;
                    } else if (existingOsis.includes(parseInt(osisVal)) || existingOsis.includes(osisVal.toString())) {
                        osisInput.addClass('is-invalid').removeClass('is-valid');
                        osisFeedback.text('OSIS ID sudah terdaftar.').show();
                        isValid = false;
                    } else {
                        osisInput.removeClass('is-invalid').addClass('is-valid');
                        osisFeedback.hide();
                    }
                } else {
                    // Required but not focused/typed yet? 
                    // If we want "warning sedari wait", we usually wait for input.
                    // But if empty, it's invalid for submit.
                    osisInput.removeClass('is-valid is-invalid'); // Clean state
                    osisFeedback.hide();
                    isValid = false;
                }

                // Validate KTP
                const ktpVal = ktpInput.val();
                if (ktpVal.length > 0) {
                    if (ktpVal.length !== 16) {
                        ktpInput.addClass('is-invalid').removeClass('is-valid');
                        ktpFeedback.text('Nomor KTP harus 16 digit angka.').show();
                        isValid = false;
                    } else if (existingKtp.includes(ktpVal) || existingKtp.includes(parseInt(ktpVal)) || existingKtp.includes(ktpVal.toString())) {
                        ktpInput.addClass('is-invalid').removeClass('is-valid');
                        ktpFeedback.text('Nomor KTP sudah terdaftar.').show();
                        isValid = false;
                    } else {
                        ktpInput.removeClass('is-invalid').addClass('is-valid');
                        ktpFeedback.hide();
                    }
                } else {
                    ktpInput.removeClass('is-valid is-invalid');
                    ktpFeedback.hide();
                    isValid = false;
                }

                // Validate Date
                const tglVal = tglInput.val();
                if (tglVal) {
                    const selectedDate = new Date(tglVal);
                    // min/max attributes are strings YYYY-MM-DD
                    const minDateStr = tglInput.attr('min');
                    const maxDateStr = tglInput.attr('max');

                    if (minDateStr && maxDateStr) {
                        const minDate = new Date(minDateStr);
                        const maxDate = new Date(maxDateStr);

                        // Compare timestamps
                        if (selectedDate < minDate || selectedDate > maxDate) {
                            tglInput.addClass('is-invalid').removeClass('is-valid');
                            tglFeedback.show();
                            isValid = false;
                        } else {
                            tglInput.removeClass('is-invalid').addClass('is-valid');
                            tglFeedback.hide();
                        }
                    }
                } else {
                    isValid = false;
                }

                // Enable/Disable Submit
                // Check if any invalid class exists or any required field is empty
                const anyInvalid = $('.is-invalid').length > 0;

                if (anyInvalid || osisVal === '' || ktpVal === '' || tglVal === '') {
                    submitBtn.prop('disabled', true);
                } else {
                    submitBtn.prop('disabled', false);
                }
            }

            // Input Listeners
            osisInput.on('input', function () {
                if (this.value.length > 4) this.value = this.value.slice(0, 4);
                validateForm();
            });

            ktpInput.on('input', function () {
                if (this.value.length > 16) this.value = this.value.slice(0, 16);
                validateForm();
            });

            tglInput.on('change', validateForm);

            // Run on load
            validateForm();
        });
    </script>
@endsection