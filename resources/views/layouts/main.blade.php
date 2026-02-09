<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>@yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('assets/img/sp-white2.png') }}" type="image/png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- Custom Styles -->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

    <!-- Global Table Styles -->
    <link href="{{ asset('css/custom-tables.css') }}" rel="stylesheet">

    <style>
        /* Modern DataTables Search Bar */
        .dataTables_filter {
            margin-bottom: 1.5rem;
        }

        .dataTables_filter input {
            border-radius: 20px !important;
            padding: 0.4rem 1rem 0.4rem 2.5rem !important;
            font-weight: 600 !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath fill='%236c757d' d='M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0s208 93.1 208 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: 0.8rem center !important;
            background-size: 1rem !important;
            border: 1px solid #ced4da !important;
            transition: all 0.2s ease-in-out !important;
            width: 250px !important;
        }

        .dataTables_filter input:focus {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
            outline: 0 !important;
        }
    </style>


    <!-- jQuery (harus paling atas sebelum plugin lain) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

</head>

<body class="sb-nav-fixed">

    {{-- Navbar --}}
    @include('layouts.navbar')

    <div id="layoutSidenav">
        {{-- Sidebar --}}
        @include('layouts.sidebar')

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    @yield('content')
                </div>
            </main>

            {{-- Footer --}}
            @include('layouts.footer')
        </div>
    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- jQuery Mask (opsional) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom Scripts -->
    <script src="{{ asset('js/scripts.js') }}"></script>

    <!-- Inisialisasi Select2 dan DataTable -->
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                width: '100%'
            });

            $('.datatable').each(function () {
                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        lengthChange: false,
                        language: {
                            "decimal": "",
                            "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                            "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                            "infoPostFix": "",
                            "thousands": ",",
                            "lengthMenu": "Tampilkan _MENU_ entri",
                            "loadingRecords": "Memuat...",
                            "processing": "Memproses...",
                            "search": "",
                            "searchPlaceholder": "Cari data...",
                            "zeroRecords": "Tidak ditemukan data yang sesuai",
                            "paginate": {
                                "first": "Pertama",
                                "last": "Terakhir",
                                "next": "Selanjutnya",
                                "previous": "Sebelumnya"
                            },
                            "aria": {
                                "sortAscending": ": aktifkan untuk mengurutkan kolom ke atas",
                                "sortDescending": ": aktifkan untuk mengurutkan kolom ke bawah"
                            }
                        },
                        initComplete: function () {
                            const table = this.api();
                            const container = $(table.table().container());
                            const infoDiv = container.find('.dataTables_info');

                            // Create the checkbox HTML with separator
                            const switchId = 'showAllSwitch_' + Math.random().toString(36).substr(2, 9);
                            const checkboxHtml = `
                                <div class="d-inline-block me-2" style="vertical-align: middle;">
                                    <div class="form-check d-inline-block me-2">
                                        <input class="form-check-input btn-show-all-switch" type="checkbox" id="${switchId}" style="cursor: pointer;">
                                        <label class="form-check-label small fw-bold text-muted" for="${switchId}" style="cursor: pointer;">Tampilkan semua</label>
                                    </div>
                                    <span class="text-muted me-2">|</span>
                                </div>
                            `;

                            // Create a wrapper for same-line alignment without affecting siblings (pagination)
                            const flexWrapper = $('<div class="d-flex align-items-center flex-wrap mt-2"></div>');
                            infoDiv.before(flexWrapper);
                            flexWrapper.append(checkboxHtml);
                            flexWrapper.append(infoDiv);

                            infoDiv.addClass('mb-0 ms-1');
                            infoDiv.css('padding-top', '0'); // Reset padding to align with checkbox

                            container.on('change', '.btn-show-all-switch', function () {
                                if (this.checked) {
                                    table.page.len(-1).draw();
                                } else {
                                    table.page.len(10).draw();
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>

    <!-- Notifikasi SweetAlert dari Session -->
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                html: "{!! session('success') !!}",
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                html: "{!! session('error') !!}",
                showConfirmButton: true
            });
        </script>
    @endif

    <!-- Global Delete Handler with SweetAlert -->
    <script>
        $(document).ready(function () {
            $(document).on('click', '.btn-delete', function (e) {
                e.preventDefault();
                var deleteUrl = $(this).attr('href');
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Data akan dipindahkan ke sampah dan dapat dipulihkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = deleteUrl;
                    }
                });
            });

            // Global Bootstrap Tooltip Initialization
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    @stack('scripts')

</body>

</html>