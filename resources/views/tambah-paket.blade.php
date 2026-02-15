@extends('layouts.main')

@section('title', 'Tambah Paket')

@section('content')

    <!-- Modern Page Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-box me-2 text-primary"></i> Tambah Paket</h1>
        <p class="text-muted small mb-0 mt-1">Formulir penambahan data paket baru</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Data Paket Baru
                    </h5>

                    <form id="form-paket" class="form-horizontal" method="post" enctype="multipart/form-data"
                        action="/tambah-paket">
                        @csrf
                        <div class="mb-3">
                            <label for="paket_suffix" class="form-label fw-bold text-dark">Nama Paket <span
                                    class="text-danger">*</span></label>
                            <div class="input-group has-validation">
                                <span class="input-group-text bg-light" id="paket-prefix">Paket</span>
                                <input type="number" class="form-control" name="paket_suffix" id="paket_suffix"
                                    placeholder="Contoh: 1" aria-describedby="paket-prefix" min="1" required>
                                <div id="paket-feedback" class="invalid-feedback">
                                    Nama paket sudah ada.
                                </div>
                            </div>
                            <div class="form-text small">Masukkan angka suffix paket (misal: 1 untuk "Paket 1")</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kuota_paket" class="form-label fw-bold text-dark">Kuota Paket <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="kuota_paket" id="kuota_paket" min="1"
                                    placeholder="0" required>
                                <div id="kuota-feedback" class="invalid-feedback">
                                    Kuota paket harus lebih dari 0.
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unit_kerja" class="form-label fw-bold text-dark">Unit Kerja <span
                                        class="text-danger">*</span></label>
                                <select class="form-select select2" name="unit_kerja" id="unit_kerja" required>
                                    <option value="" selected disabled>Pilih Unit Kerja</option>
                                    @foreach ($unit as $item)
                                        <option value="{{$item->unit_id}}">{{$item->unit_kerja}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="/paket" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4" id="btn-submit">
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
            $('.select2').select2({
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
                    if (kuotaInput.val() !== "") {
                        kuotaInput.addClass('is-invalid').removeClass('is-valid');
                        kuotaFeedback.show();
                    }
                    isValid = false;
                } else {
                    kuotaInput.removeClass('is-invalid').addClass('is-valid');
                    kuotaFeedback.hide();
                }

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