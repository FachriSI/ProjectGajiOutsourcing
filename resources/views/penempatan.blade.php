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
                <th class="text-center">Aksi</th>
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
                    <td class="text-center">
                        @if($item->status_aktif === 'Aktif')
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="#" class="btn btn-danger btn-sm btn-berhenti" data-id="{{ $item->karyawan_id }}"
                                    title="Berhentikan">
                                    <i class="fa fa-times-circle"></i>
                                </a>
                                <a href="#" class="btn btn-warning btn-sm btn-ganti" data-id="{{ $item->karyawan_id }}"
                                    title="Ganti">
                                    <i class="fas fa-exchange-alt"></i>
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

        // Event Listener Button Ganti - Dialog 2 Opsi Penggantian
        $(document).on('click', '.btn-ganti', function (e) {
            e.preventDefault();
            const id = $(this).data('id');

            Swal.fire({
                title: 'Ganti Karyawan',
                html: `
                        <div class="text-start" style="max-height: 500px; overflow-y: auto;">
                            <!-- PILIHAN JENIS PENGGANTIAN -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Penggantian:</label>
                                <select id="jenis_penggantian" class="form-select form-select-sm">
                                    <option value="personal">Ganti Data Personal Saja (Posisi Sama)</option>
                                    <option value="semua">Ganti Semua Data (Posisi Baru)</option>
                                </select>
                            </div>

                            <hr>
                            <h6 class="fw-bold mb-3">📝 Data Personal</h6>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="form-label small mb-1">OSIS ID</label>
                                    <input type="text" id="osis_id" class="form-control form-control-sm">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small mb-1">Nomor KTP</label>
                                    <input type="text" id="ktp" class="form-control form-control-sm" maxlength="16">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" id="nama" class="form-control form-control-sm">
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="form-label small mb-1">Tanggal Lahir</label>
                                    <input type="date" id="tanggal_lahir" class="form-control form-control-sm">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small mb-1">Jenis Kelamin</label>
                                    <select id="jenis_kelamin" class="form-select form-select-sm">
                                        <option value="">Pilih...</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="form-label small mb-1">Agama</label>
                                    <select id="agama" class="form-select form-select-sm">
                                        <option value="">Pilih...</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small mb-1">Status Perkawinan</label>
                                    <select id="status" class="form-select form-select-sm">
                                        <option value="">Pilih...</option>
                                        <option value="S">Single</option>
                                        <option value="M">Menikah</option>
                                        <option value="D">Duda</option>
                                        <option value="J">Janda</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small mb-1">Alamat</label>
                                <input type="text" id="alamat" class="form-control form-control-sm">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small mb-1">Asal</label>
                                <input type="text" id="asal" class="form-control form-control-sm">
                            </div>

                            <!-- DATA PENEMPATAN (Tampil jika pilih "Ganti Semua") -->
                            <div id="data-penempatan" style="display: none;">
                                <hr>
                                <h6 class="fw-bold mb-3">🏢 Data Penempatan</h6>
                                <div class="mb-2">
                                    <label class="form-label small mb-1">Jabatan <span class="text-danger">*</span></label>
                                    <select id="jabatan" class="form-select form-select-sm">
                                        <option value="">Loading...</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small mb-1">Lokasi <span class="text-danger">*</span></label>
                                    <select id="lokasi" class="form-select form-select-sm">
                                        <option value="">Loading...</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small mb-1">Paket <span class="text-danger">*</span></label>
                                    <select id="paket" class="form-select form-select-sm">
                                        <option value="">Loading...</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small mb-1">Harian/Shift</label>
                                    <select id="harianshift" class="form-select form-select-sm">
                                        <option value="1">Harian</option>
                                        <option value="2">Shift</option>
                                    </select>
                                </div>
                            </div>

                            <hr>
                            <div class="mb-2">
                                <label class="form-label small mb-1">Catatan Berhenti (Karyawan Lama) <span class="text-danger">*</span></label>
                                <textarea id="catatan" class="form-control form-control-sm" rows="2" placeholder="Contoh: Habis kontrak, Mengundurkan diri..."></textarea>
                            </div>
                        </div>
                    `,
                width: '700px',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check"></i> Simpan Penggantian',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                didOpen: () => {
                    // Load dropdown data untuk opsi "Ganti Semua"
                    Promise.all([
                        fetch('/api/jabatan').then(r => r.json()),
                        fetch('/api/lokasi').then(r => r.json()),
                        fetch('/api/paket').then(r => r.json())
                    ]).then(([jabatanData, lokasiData, paketData]) => {
                        const jabatanSelect = document.getElementById('jabatan');
                        const lokasiSelect = document.getElementById('lokasi');
                        const paketSelect = document.getElementById('paket');

                        jabatanSelect.innerHTML = '<option value="">Pilih...</option>' +
                            jabatanData.map(j => `<option value="${j.kode_jabatan}">${j.jabatan}</option>`).join('');
                        lokasiSelect.innerHTML = '<option value="">Pilih...</option>' +
                            lokasiData.map(l => `<option value="${l.kode_lokasi}">${l.lokasi}</option>`).join('');
                        paketSelect.innerHTML = '<option value="">Pilih...</option>' +
                            paketData.map(p => `<option value="${p.paket_id}">${p.paket}</option>`).join('');
                    });

                    // Toggle tampilan data penempatan
                    document.getElementById('jenis_penggantian').addEventListener('change', function () {
                        const dataPenempatan = document.getElementById('data-penempatan');
                        dataPenempatan.style.display = this.value === 'semua' ? 'block' : 'none';
                    });
                },
                preConfirm: () => {
                    const jenisPenggantian = document.getElementById('jenis_penggantian').value;
                    const nama = document.getElementById('nama').value;
                    const catatan = document.getElementById('catatan').value;

                    // Validasi wajib
                    if (!nama || !catatan) {
                        Swal.showValidationMessage('Nama dan Catatan wajib diisi');
                        return false;
                    }

                    // Validasi untuk opsi "Ganti Semua"
                    if (jenisPenggantian === 'semua') {
                        const jabatan = document.getElementById('jabatan').value;
                        const lokasi = document.getElementById('lokasi').value;
                        const paket = document.getElementById('paket').value;

                        if (!jabatan || !lokasi || !paket) {
                            Swal.showValidationMessage('Jabatan, Lokasi, dan Paket wajib diisi untuk penggantian semua data');
                            return false;
                        }
                    }

                    // Collect data
                    const data = {
                        jenis_penggantian: jenisPenggantian,
                        osis_id: document.getElementById('osis_id').value,
                        ktp: document.getElementById('ktp').value,
                        nama: nama,
                        tanggal_lahir: document.getElementById('tanggal_lahir').value,
                        jenis_kelamin: document.getElementById('jenis_kelamin').value,
                        agama: document.getElementById('agama').value,
                        status: document.getElementById('status').value,
                        alamat: document.getElementById('alamat').value,
                        asal: document.getElementById('asal').value,
                        catatan: catatan
                    };

                    // Tambahkan data penempatan jika opsi "Ganti Semua"
                    if (jenisPenggantian === 'semua') {
                        data.jabatan = document.getElementById('jabatan').value;
                        data.lokasi = document.getElementById('lokasi').value;
                        data.paket = document.getElementById('paket').value;
                        data.harianshift = document.getElementById('harianshift').value;
                    }

                    return data;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu, data sedang diproses',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim data untuk update karyawan
                    $.ajax({
                        url: "/ganti-karyawan/" + id,
                        type: "POST",
                        data: {
                            ...result.value,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Data karyawan berhasil diganti.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan saat mengganti karyawan.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
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