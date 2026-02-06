@extends('layouts.main')

@section('title', 'Penempatan')

@section('content')
    <!-- Header -->
    <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-danger border-5">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-map-marker-alt me-2 text-danger"></i> Penempatan</h1>
                <p class="text-muted small mb-0 mt-1">Kelola penempatan dan distribusi karyawan</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-excel me-1"></i> Import/Template
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="importModalLabel"><i class="fas fa-file-excel me-2"></i>Template & Import
                        Data
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ url('/import-karyawan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>1. Download Template:</span>
                            <a href="{{ route('template.karyawan') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download"></i> Download Template
                            </a>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label for="file" class="form-label">2. Upload File Excel:</label>
                            <input type="file" name="file" id="file" class="form-control" accept=".xlsx, .xls, .csv"
                                required>
                            <div class="form-text">Format yang didukung: .xlsx, .xls, .csv</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload"></i> Import
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="filterPaket" class="form-label fw-bold small text-muted text-uppercase">Filter Paket</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-box text-muted"></i></span>
                        <select id="filterPaket" class="form-select border-start-0 ps-0">
                            <option value="">Semua Paket</option>
                            @php
                                $paket = collect($data)->pluck('paket')->unique()->toArray();
                                usort($paket, function ($a, $b) {
                                    preg_match('/\d+/', $a, $matchesA);
                                    preg_match('/\d+/', $b, $matchesB);
                                    return $matchesA[0] - $matchesB[0];
                                });
                            @endphp
                            @foreach ($paket as $paket)
                                <option value="{{ $paket }}">{{ $paket }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="filterAktifMulai" class="form-label fw-bold small text-muted text-uppercase">Filter Aktif Mulai</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-calendar text-muted"></i></span>
                        <select id="filterAktifMulai" class="form-select border-start-0 ps-0">
                            <option value="">Semua Tanggal</option>
                            @php
                                $tanggal = collect($data)->pluck('aktif_mulai')->unique()->toArray();
                                usort($tanggal, function ($a, $b) {
                                    $dateA = DateTime::createFromFormat('F Y', $a);
                                    $dateB = DateTime::createFromFormat('F Y', $b);
                                    return $dateA <=> $dateB;
                                });
                            @endphp
                            @foreach ($tanggal as $tgl)
                                <option value="{{ $tgl }}">{{ $tgl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-dark text-white py-3">
            <h6 class="m-0 fw-bold"><i class="fas fa-table me-2"></i>Data Penempatan Karyawan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="datatablesSimple" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No.</th>
                            <th>OSIS ID</th>
                            <th>Nama</th>
                            <th>Vendor</th>
                            <th>Unit Kerja</th>
                            <th>Paket</th>
                            <th>Jabatan</th>
                            <th>Aktif Mulai</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-monospace text-primary">{{$item->osis_id}}</td>
                                <td class="fw-bold">{{$item->nama_tk}}</td>
                                <td>{{$item->perusahaan}}</td>
                                <td>{{$item->unit_kerja['unit_kerja']}}</td>
                                <td><span class="badge bg-secondary">{{$item->paket}}</span></td>
                                <td>{{$item->jabatan}}</td>
                                <td>{{$item->aktif_mulai }}</td>
                                <td class="text-center">
                                    @if($item->status_aktif == 'Aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Berhenti</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->status_aktif === 'Aktif')
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="#" class="btn btn-danger btn-sm btn-berhenti" data-id="{{ $item->karyawan_id }}"
                                                data-bs-toggle="tooltip" title="Berhentikan">
                                                <i class="fa fa-times-circle"></i>
                                            </a>
                                            <a href="#" class="btn btn-warning btn-sm btn-ganti" data-id="{{ $item->karyawan_id }}"
                                                data-bs-toggle="tooltip" title="Ganti">
                                                <i class="fas fa-exchange-alt"></i>
                                            </a>
                                            <a href="#" class="btn btn-info btn-sm btn-history text-white" data-id="{{ $item->karyawan_id }}"
                                                data-bs-toggle="tooltip" title="History">
                                                <i class="fas fa-history"></i>
                                            </a>
                                        </div>
                                    @elseif($item->status_aktif === 'Berhenti')
                                        <a href="/tambah-pengganti/{{ $item->karyawan_id }}" class="btn btn-success btn-sm w-100">
                                            <i class="fa fa-user-plus me-1"></i> Pengganti
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Fungsi Reusable untuk Proses Berhenti
        function prosesBerhenti(id, isGanti) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: isGanti ? "Ingin mengganti karyawan ini? (Karyawan lama akan diberhentikan)" : "Ingin memberhentikan karyawan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: isGanti ? 'Ya, Ganti' : 'Ya, Berhentikan'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Konfirmasi Berhenti',
                        html:
                            '<textarea id="swal-input-catatan" class="swal2-textarea" placeholder="Contoh: Mengundurkan diri, Habis Kontrak..."></textarea>',
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Kirim',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            const note = document.getElementById('swal-input-catatan').value;
                            if (!note) {
                                Swal.showValidationMessage('Catatan harus diisi');
                            }
                            return { note: note }
                        }
                    }).then((inputResult) => {
                        if (inputResult.isConfirmed) {
                            const { note } = inputResult.value;

                            // Set tanggal otomatis ke tanggal sekarang
                            const today = new Date().toISOString().split('T')[0];

                            $.ajax({
                                url: "/set-berhenti",
                                type: "POST",
                                data: {
                                    id: id,
                                    catatan: note,
                                    tanggal_berhenti: today,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Karyawan telah diberhentikan.',
                                        icon: 'success'
                                    }).then(() => {
                                        if (isGanti) {
                                            window.location.href = '/tambah-pengganti/' + id;
                                        } else {
                                            location.reload();
                                        }
                                    });
                                },
                                error: function (xhr) {
                                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data.', 'error');
                                }
                            });
                        }
                    });
                }
            });
        }

        // Event Listener Button Berhentikan
        $(document).on('click', '.btn-berhenti', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            prosesBerhenti(id, false);
        });

        // Event Listener Button Ganti
        $(document).on('click', '.btn-ganti', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            prosesBerhenti(id, true);
        });

        $(document).ready(function () {
            if (!$.fn.DataTable.isDataTable('#datatablesSimple')) {
                var table = $('#datatablesSimple').DataTable({
                    processing: true,
                    serverSide: false,
                    language: {
                        "decimal": "",
                        "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                        "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                        "infoPostFix": "",
                        "thousands": ",",
                        "lengthMenu": "Tampilkan _MENU_ entri",
                        "loadingRecords": "Sedang memuat...",
                        "processing": "Sedang memproses...",
                        "search": "Cari:",
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
                    }
                });

                $('#filterPaket').on('change', function () {
                    var val = this.value;
                    table.column(5).search(val ? '^' + val + '$' : '', true, false).draw();
                });

                $('#filterAktifMulai').on('change', function () {
                    var val = this.value;
                    table.column(7).search(val ? '^' + val + '$' : '', true, false).draw();
                });
            }
        });
    </script>

    <!-- Modal History -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel"><i class="fas fa-history me-2"></i> Riwayat Karyawan Sebelumnya</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tableHistory">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Karyawan</th>
                                    <th>OSIS ID</th>
                                    <th>Tanggal Diberhentikan</th>
                                    <th>Diberhentikan Oleh</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan dimuat disini via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).on('click', '.btn-history', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const modal = new bootstrap.Modal(document.getElementById('historyModal'));
            const tbody = $('#tableHistory tbody');
            
            tbody.html('<tr><td colspan="6" class="text-center">Memuat data...</td></tr>');
            modal.show();

            $.ajax({
                url: '/get-mpp-history/' + id,
                type: 'GET',
                success: function(response) {
                    tbody.empty();
                    if (response.length === 0) {
                        tbody.html('<tr><td colspan="6" class="text-center">Tidak ada riwayat.</td></tr>');
                    } else {
                        response.forEach((item, index) => {
                            tbody.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.nama}</td>
                                    <td>${item.osis_id || '-'}</td>
                                    <td>${item.tanggal_berhenti || '-'}</td>
                                    <td>${item.diberhentikan_oleh || '-'}</td>
                                    <td>${item.catatan || '-'}</td>
                                </tr>
                            `);
                        });
                    }
                },
                error: function(xhr) {
                    tbody.html('<tr><td colspan="6" class="text-center text-danger">Gagal memuat data.</td></tr>');
                }
            });
        });
    </script>
@endsection