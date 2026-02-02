@extends('layouts.main')

@section('title', 'Penempatan')

@section('content')
    <!-- @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif -->

    <h3 class="mt-4">Penempatan</h3>
    <!-- Tombol Ikon Excel -->
    <!-- Tombol Ikon Excel -->
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal"
        title="Template & Import Data">
        <i class="fas fa-file-excel fa-lg"></i>
    </button>
    
    <!-- Modal Import Excel -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="importModalLabel"><i class="fas fa-file-excel me-2"></i>Template & Import Data
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

    <!-- <a href="/gettambah-penempatan" class="btn btn-primary mb-3">Tambah Data</a> -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="filterPaket">Filter Paket</label>
            <select id="filterPaket" class="form-control">
                <option value="">Semua</option>
                @php
                    $paket = collect($data)->pluck('paket')->unique()->toArray();
                    usort($paket, function ($a, $b) {
                        preg_match('/\d+/', $a, $matchesA);
                        preg_match('/\d+/', $b, $matchesB);
                        return $matchesA[0] - $matchesB[0]; // Membandingkan angka dalam string
                    });
                @endphp
                @foreach ($paket as $paket)
                    <option value="{{ $paket }}">{{ $paket }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="filterAktifMulai">Filter Aktif Mulai</label>
            <select id="filterAktifMulai" class="form-control">
                <option value="">Semua</option>
                @php
                    $tanggal = collect($data)->pluck('aktif_mulai')->unique()->toArray();

                    // Mengonversi tanggal ke objek DateTime dan mengurutkannya
                    usort($tanggal, function ($a, $b) {
                        $dateA = DateTime::createFromFormat('F Y', $a);
                        $dateB = DateTime::createFromFormat('F Y', $b);
                        return $dateA <=> $dateB; // Mengurutkan berdasarkan objek DateTime
                    });
                @endphp
                @foreach ($tanggal as $tgl)
                    <option value="{{ $tgl }}">{{ $tgl }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <table id="datatablesSimple">
        <thead>
            <tr>
                <th>No.</th>
                <th>OSIS ID</th>
                <th>Nama</th>
                <th>Vendor/Perusahaan</th>
                <th>Unit Kerja</th>
                <th>Paket</th>
                <th>Jabatan</th>
                <th>Aktif Mulai</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <th>{{ $loop->iteration }}</th>
                    <td>{{$item->osis_id}}</td>
                    <td>{{$item->nama_tk}}</td>
                    <td>{{$item->perusahaan}}</td>
                    <td>{{$item->unit_kerja['unit_kerja']}}</td>
                    <td>{{$item->paket}}</td>
                    <td>{{$item->jabatan}}</td>
                    <td>{{$item->aktif_mulai }}</td>
                    <td>{{$item->status_aktif}}</td>
                    <td>
                        @if($item->status_aktif === 'Aktif')
                            <div class="d-flex gap-1">
                                <a href="#" class="btn btn-danger btn-sm btn-berhenti" data-id="{{ $item->karyawan_id }}">
                                    <i class="fa fa-times-circle"></i> Berhentikan
                                </a>
                                <a href="#" class="btn btn-warning btn-sm btn-ganti" data-id="{{ $item->karyawan_id }}">
                                    <i class="fas fa-exchange-alt"></i> Ganti
                                </a>
                            </div>
                        @elseif($item->status_aktif === 'Berhenti')
                            <a href="/tambah-pengganti/{{ $item->karyawan_id }}" class="btn btn-success btn-sm">
                                <i class="fa fa-user-plus"></i> Tambah Pengganti
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

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
                            '<input type="date" id="swal-input-date" class="swal2-input" placeholder="Tanggal Berhenti">' +
                            '<textarea id="swal-input-catatan" class="swal2-textarea" placeholder="Contoh: Mengundurkan diri, Habis Kontrak..."></textarea>',
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Kirim',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            const date = document.getElementById('swal-input-date').value;
                            const note = document.getElementById('swal-input-catatan').value;
                            if (!date || !note) {
                                Swal.showValidationMessage('Tanggal dan Catatan harus diisi');
                            }
                            return { date: date, note: note }
                        }
                    }).then((inputResult) => {
                        if (inputResult.isConfirmed) {
                            const { date, note } = inputResult.value;

                            $.ajax({
                                url: "/set-berhenti",
                                type: "POST",
                                data: {
                                    id: id,
                                    catatan: note,
                                    tanggal_berhenti: date,
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
@endsection