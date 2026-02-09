@extends('layouts.main')

@section('title', 'Karyawan')

@section('content')

    <h3 class="mt-4">Tambah Karyawan</h3>
    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-karyawan">
        @csrf
        <div class="mb-3">
            <label for="osis_id" class="form-label">OSIS ID <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="osis_id" id="osis_id" required
                onkeypress="return event.charCode >= 48 && event.charCode <= 57">
            <div id="osis-feedback" class="invalid-feedback">
                OSIS ID harus 4 digit angka dan belum terdaftar.
            </div>
        </div>
        <div class="mb-3">
            <label for="ktp" class="form-label">Nomor KTP <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="ktp" id="ktp" required
                onkeypress="return event.charCode >= 48 && event.charCode <= 57">
            <div id="ktp-feedback" class="invalid-feedback">
                Nomor KTP harus 16 digit angka dan belum terdaftar.
            </div>
        </div>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Tenaga Kerja <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nama" id="nama" required>
        </div>
        <div class="mb-3">
            <label for="paket_id" class="form-label">Pilih Paket <span class="text-danger">*</span></label>
            <select class="custom-select select2" name="paket_id" id="paket_id" required>
                <option value="">Pilih Paket...</option>
                @foreach ($paketList as $paket)
                    <option value="{{ $paket->paket_id }}"
                        {{ (old('paket_id') == $paket->paket_id || request('paket_id') == $paket->paket_id) ? 'selected' : '' }}>
                        {{ $paket->paket }} (Sisa Kuota: {{ $paket->sisa_kuota }})
                    </option>
                @endforeach
            </select>
            @error('paket_id')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="perusahaan" class="form-label">Vendor/Perusahaan <span class="text-danger">*</span></label>
            <select class="custom-select select2" name="perusahaan" id="perusahaan" required>
                <option selected value="">Pilih Perusahaan</option>
                @foreach ($dataP as $item)
                    <option value="{{$item->perusahaan_id}}">{{$item->perusahaan}}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
            <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir"
                min="{{ date('Y-m-d', strtotime('-56 years')) }}" max="{{ date('Y-m-d', strtotime('-18 years')) }}"
                required>
            <div id="tgl-feedback" class="invalid-feedback">
                Usia tidak memenuhi syarat (18-56 tahun).
            </div>
        </div>
        <div class="mb-3">
            <label for="jenis-kelamin" class="form-label">Jenis Kelamin</label> <br>
            <input type="radio" id="laki" name="jenis_kelamin" value="L">
            <label for="laki">Laki-laki</label>
            <input type="radio" id="perempuan" name="jenis_kelamin" value="P">
            <label for="perempuan">Perempuan</label><br>
        </div>
        <div class="mb-3">
            <label for="agama" class="form-label">Agama</label>
            <select class="form-select" aria-label="Default select example" name="agama" id="agama">
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
            <label for="status" class="form-label">Status</label>
            <select class="form-select" aria-label="Default select example" name="status" id="status">
                <option selected>Pilih...</option>
                <option value="S">Single</option>
                <option value="M">Menikah</option>
                <option value="D">Duda</option>
                <option value="J">Janda</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" class="form-control" name="alamat" id="alamat">
        </div>

        <div class="mb-3">
            <label for="asal" class="form-label">Asal</label>
            <input type="text" class="form-control" name="asal" id="asal">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
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
                if(tglVal) {
                     const selectedDate = new Date(tglVal);
                     // min/max attributes are strings YYYY-MM-DD
                     const minDateStr = tglInput.attr('min');
                     const maxDateStr = tglInput.attr('max');
                     
                     if (minDateStr && maxDateStr) {
                         const minDate = new Date(minDateStr);
                         const maxDate = new Date(maxDateStr);
                         
                         // Compare timestamps
                         if(selectedDate < minDate || selectedDate > maxDate) {
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
            osisInput.on('input', function() {
                if(this.value.length > 4) this.value = this.value.slice(0, 4);
                validateForm();
            });

            ktpInput.on('input', function() {
                if(this.value.length > 16) this.value = this.value.slice(0, 16);
                validateForm();
            });

            tglInput.on('change', validateForm);
            
            // Run on load
            validateForm();
        });
    </script>
@endsection