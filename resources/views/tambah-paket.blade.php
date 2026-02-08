@extends('layouts.main')

@section('title', 'Paket')

@section('content')

    <h3 class="mt-4">Tambah Paket</h3>
    <!-- Input Date Range -->
    <form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/tambah-paket">
        @csrf
        <div class="mb-3">
            <label for="paket_suffix" class="form-label">Nama Paket</label>
            <div class="input-group has-validation">
                <span class="input-group-text" id="paket-prefix">Paket</span>
                <input type="number" class="form-control" name="paket_suffix" id="paket_suffix" placeholder="Contoh: 1"
                    aria-describedby="paket-prefix" min="1" required
                    onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                <div id="paket-feedback" class="invalid-feedback">
                    Nama paket sudah ada.
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="kuota_paket" class="form-label">Kuota Paket</label>
            <input type="number" class="form-control" name="kuota_paket" id="kuota_paket" min="1" required
                onkeypress="return event.charCode >= 48 && event.charCode <= 57">
            <div id="kuota-feedback" class="invalid-feedback">
                Kuota paket harus lebih dari 0.
            </div>
        </div>

        <div class="mb-3">
            <label for="unit_kerja" class="form-label">Unit Kerja</label>
            <select class="custom-select select2" name="unit_kerja" id="unit_kerja" required>
                <option value="" selected disabled>Pilih Unit Kerja</option>
                @foreach ($unit as $item)
                    <option value="{{$item->unit_id}}">{{$item->unit_kerja}}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary" id="btn-submit">Submit</button>
    </form>

    <script>
        $(document).ready(function () {
            $('#unit_kerja').select2({
                placeholder: "Pilih Unit Kerja",
                allowClear: false,
                width: '100%'
            });

            // Loop existing package names from controller
            const existingPakets = @json($existingPakets ?? []);

            const paketInput = $('#paket_suffix');
            const kuotaInput = $('#kuota_paket');
            const submitBtn = $('#btn-submit');
            const paketFeedback = $('#paket-feedback');
            const kuotaFeedback = $('#kuota-feedback');

            function validateForm() {
                let isValid = true;

                // Validate Paket Name
                const suffix = paketInput.val().trim();
                const fullName = "Paket " + suffix;

                if (suffix === "") {
                    paketInput.removeClass('is-valid is-invalid');
                    paketFeedback.hide();
                    isValid = false; // Required
                } else if (existingPakets.includes(fullName)) {
                    paketInput.addClass('is-invalid').removeClass('is-valid');
                    paketFeedback.text("Nama paket '" + fullName + "' sudah ada. Harap gunakan nama lain.").show();
                    isValid = false;
                } else {
                    paketInput.removeClass('is-invalid').addClass('is-valid');
                    paketFeedback.hide();
                }

                // Validate Quota
                const kuota = parseInt(kuotaInput.val());

                if (isNaN(kuota) || kuota <= 0) {
                    if (kuotaInput.val() !== "") { // Only show invalid if user has typed something or tried to submit
                        kuotaInput.addClass('is-invalid').removeClass('is-valid');
                        kuotaFeedback.show();
                    }
                    isValid = false;
                } else {
                    kuotaInput.removeClass('is-invalid').addClass('is-valid');
                    kuotaFeedback.hide();
                }

                // Simple check if inputs are empty for button state (optional, or just rely on HTML5 required)
                // But user asked for warning immediately. 
                // We'll keep the button enabled but prevent submit if invalid, 
                // OR disable it. User said "warning sedari awal", usually implies real-time feedback.
                // Disabling button is good practice.

                if (isValid) {
                    submitBtn.prop('disabled', false);
                } else {
                    submitBtn.prop('disabled', true);
                }
            }

            // Real-time listeners
            paketInput.on('input', validateForm);
            kuotaInput.on('input', validateForm);

            // Run once on load to set initial state
            validateForm();
        });
    </script>
@endsection