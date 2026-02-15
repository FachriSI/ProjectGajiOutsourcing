@extends('layouts.main')

@section('title', 'Paket')

@section('content')

    <h3 class="mt-4">Tambah Paket</h3>
    <!-- Input Date Range -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-box me-2"></i>Data Paket Baru</h6>
                </div>
                <div class="card-body">
                    <form id="form-paket" class="form-horizontal" method="post" enctype="multipart/form-data" action="/tambah-paket">
                        @csrf
                        <div class="mb-3">
                            <label for="paket_suffix" class="form-label fw-bold">Nama Paket <span class="text-danger">*</span></label>
                            <div class="input-group has-validation">
                                <span class="input-group-text bg-light" id="paket-prefix">Paket</span>
                                <input type="number" class="form-control" name="paket_suffix" id="paket_suffix" placeholder="Contoh: 1"
                                    aria-describedby="paket-prefix" min="1" required
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                <div id="paket-feedback" class="invalid-feedback">
                                    Nama paket sudah ada.
                                </div>
                            </div>
                            <div class="form-text small">Masukkan angka suffix paket (misal: 1 untuk "Paket 1")</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kuota_paket" class="form-label fw-bold">Kuota Paket <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="kuota_paket" id="kuota_paket" min="1" placeholder="0" required
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                <div id="kuota-feedback" class="invalid-feedback">
                                    Kuota paket harus lebih dari 0.
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unit_kerja" class="form-label fw-bold">Unit Kerja <span class="text-danger">*</span></label>
                                <select class="form-select select2" name="unit_kerja" id="unit_kerja" required>
                                    <option value="" selected disabled>Pilih Unit Kerja</option>
                                    @foreach ($unit as $item)
                                        <option value="{{$item->unit_id}}">{{$item->unit_kerja}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="/paket" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary" id="btn-submit">
                                <i class="fas fa-save me-2"></i>Simpan Paket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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