@extends('layouts.main')

@section('title', 'Karyawan')

@section('content')

  <!-- Header -->
  <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4 border-start border-primary border-5">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-users me-2 text-primary"></i> Data Karyawan</h1>
        <p class="text-muted small mb-0 mt-1">Manajemen data karyawan, mutasi, promosi, dan jadwal.</p>
      </div>
      <div class="d-flex gap-2">
        @if($hasDeleted)
          <a href="/karyawan/sampah" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-trash-restore me-1"></i> Sampah
          </a>
        @endif

        <!-- Export Excel -->
        <a href="/karyawan/export" class="btn btn-success shadow-sm">
          <i class="fas fa-file-excel me-1"></i> Export Excel
        </a>

        <!-- Button Template & Import -->
        <button type="button" class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal"
          data-bs-target="#templateModal" title="Template & Import Data">
          <i class="fas fa-file-excel me-1"></i> Import / Template
        </button>

        <a href="/gettambah-karyawan" class="btn btn-primary shadow-sm">
          <i class="fas fa-plus me-1"></i> Tambah Karyawan
        </a>
      </div>
    </div>
  </div>

  <!-- Modal Template & Import -->
  <div class="modal fade" id="templateModal" tabindex="-1" aria-labelledby="templateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="templateModalLabel"><i class="fas fa-file-excel me-2"></i>Template & Import Data
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs" id="importTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="mutasi-tab" data-bs-toggle="tab" data-bs-target="#mutasi" type="button"
                role="tab" aria-controls="mutasi" aria-selected="true">Mutasi & Promosi</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="baru-tab" data-bs-toggle="tab" data-bs-target="#baru" type="button" role="tab"
                aria-controls="baru" aria-selected="false">Karyawan Baru</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="pakaian-tab" data-bs-toggle="tab" data-bs-target="#pakaian" type="button"
                role="tab" aria-controls="pakaian" aria-selected="false">Update Pakaian</button>
            </li>
          </ul>
          <div class="tab-content p-3 border border-top-0 rounded-bottom" id="importTabsContent">
            <!-- Tab Mutasi -->
            <div class="tab-pane fade show active" id="mutasi" role="tabpanel" aria-labelledby="mutasi-tab">
              <div class="alert alert-light border border-primary text-primary">
                <i class="fas fa-info-circle"></i> Gunakan fitur ini untuk melakukan mutasi atau promosi karyawan secara
                massal.
              </div>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span>1. Download Template Mutasi:</span>
                <a href="{{ route('template.mutasi') }}" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-download"></i> Download Template
                </a>
              </div>
              <hr>
              <form action="{{ url('/import-mutasi') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                  <label for="fileMutasi" class="form-label">2. Upload File Mutasi (Excel):</label>
                  <input type="file" name="file" id="fileMutasi" class="form-control" accept=".xlsx, .xls, .csv" required>
                </div>
                <div class="text-end">
                  <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Import Mutasi</button>
                </div>
              </form>
            </div>

            <!-- Tab Karyawan Baru -->
            <div class="tab-pane fade" id="baru" role="tabpanel" aria-labelledby="baru-tab">
              <div class="alert alert-light border border-primary text-primary">
                <i class="fas fa-info-circle"></i> Gunakan fitur ini untuk mengimport data karyawan <strong>BARU</strong>
                yang belum ada di sistem.
                <br>Pastikan OSIS ID dan No KTP belum terdaftar.
              </div>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span>1. Download Template Karyawan Baru:</span>
                <a href="{{ route('template.karyawan-baru') }}" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-download"></i> Download Template
                </a>
              </div>
              <hr>
              <form action="{{ url('/import-karyawan-baru') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                  <label for="fileBaru" class="form-label">2. Upload File Karyawan Baru (Excel):</label>
                  <input type="file" name="file" id="fileBaru" class="form-control" accept=".xlsx, .xls, .csv" required>
                </div>
                <div class="text-end">
                  <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Import Karyawan
                    Baru</button>
                </div>
              </form>
            </div>

            <!-- Tab Pakaian -->
            <div class="tab-pane fade" id="pakaian" role="tabpanel" aria-labelledby="pakaian-tab">
              <div class="alert alert-light border border-primary text-primary">
                <i class="fas fa-info-circle"></i> Gunakan fitur ini untuk mengupdate ukuran pakaian karyawan secara
                massal.
                <br><strong>Note:</strong> Pastikan ukuran baju sesuai dengan Master Data (S, M, L, XL, etc.)
              </div>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span>1. Download Template Pakaian:</span>
                <!-- Anda perlu menyediakan file template ini -->
                <a href="{{ asset('templates/templatePakaian_import.xlsx') }}" class="btn btn-outline-primary btn-sm"
                  download>
                  <i class="fas fa-download"></i> Download Template
                </a>
              </div>
              <hr>
              <form action="{{ url('/import-pakaian') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                  <label for="filePakaian" class="form-label">2. Upload File Pakaian (Excel):</label>
                  <input type="file" name="file" id="filePakaian" class="form-control" accept=".xlsx, .xls, .csv"
                    required>
                </div>
                <div class="text-end">
                  <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Import Pakaian</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow border-0 mb-4">

    <div class="card-body">
      <!-- Filter Bar -->
      <div class="row mb-3 g-2 align-items-end">
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted text-uppercase">Status</label>
          <select id="filterStatus" class="form-select form-select-sm">
            <option value="">Semua Status</option>
            <option value="Aktif" selected>Aktif</option>
            <option value="Berhenti">Berhenti</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted text-uppercase">Perusahaan</label>
          <select id="filterPerusahaan" class="form-select form-select-sm">
            <option value="">Semua Perusahaan</option>
            @foreach ($data->pluck('perusahaan.perusahaan')->unique()->sort() as $perusahaan)
              @if($perusahaan)
                <option value="{{ $perusahaan }}">{{ $perusahaan }}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted text-uppercase">Tipe Pekerjaan</label>
          <select id="filterTipe" class="form-select form-select-sm">
            <option value="">Semua Tipe</option>
            <option value="Lapangan">Lapangan</option>
            <option value="Non Lapangan">Non Lapangan</option>
          </select>
        </div>
        <div class="col-md-3">
          <button id="resetFilter" class="btn btn-outline-secondary btn-sm w-100">
            <i class="fas fa-undo me-1"></i> Reset Filter
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover datatable" id="datatableSimple" width="100%" cellspacing="0">
          <thead class="table-light">
            <tr>
              <th class="text-center" width="5%">No.</th>
              <th>OSIS ID</th>
              <th>KTP</th>
              <th>Nama</th>
              <th>Perusahaan</th>
              <th>Status</th>
              <th>Tipe</th>
              <th class="text-center" width="18%">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($data as $item)
              <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item->osis_id }}</td>
                <td>{{ $item->ktp }}</td>
                <td class="fw-bold">{{ $item->nama_tk }}</td>
                <td>{{ $item->perusahaan->perusahaan }}</td>
                <td>
                  @if($item->status_aktif == 'Aktif')
                    <span class="badge bg-success">Aktif</span>
                  @else
                    <span class="badge bg-danger">{{ $item->status_aktif ?? 'N/A' }}</span>
                  @endif
                </td>
                <td>{{ $item->tipe_pekerjaan ?? '-' }}</td>
                <td class="text-center">
                  <div class="d-flex gap-1 justify-content-center">
                    <a href="/detail-karyawan/{{ $item->karyawan_id }}" class="btn btn-sm btn-outline-primary"
                      data-bs-toggle="tooltip" title="Detail">
                      <i class="fas fa-info-circle"></i>
                    </a>
                    <a href="/getupdate-karyawan/{{ $item->karyawan_id }}" class="btn btn-sm btn-outline-secondary"
                      data-bs-toggle="tooltip" title="Edit">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="{{ url('delete-karyawan', $item->karyawan_id) }}"
                      class="btn btn-sm btn-outline-danger btn-delete" data-bs-toggle="tooltip" title="Hapus">
                      <i class="fas fa-trash"></i>
                    </a>

                    <div class="dropdown">
                      <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false" title="Menu Lainnya">
                        <i class="fas fa-cog"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li>
                          <button type="button" class="dropdown-item btn-mutasi" data-bs-toggle="modal"
                            data-bs-target="#mutasiModal" data-id="{{ $item->karyawan_id }}"
                            data-nama="{{ $item->nama_tk }}"
                            data-paket="{{ $paketKaryawan[$item->karyawan_id]->nama_paket ?? 'Belum ada' }}">
                            <i class="fas fa-random me-2 text-secondary"></i> Mutasi Paket
                          </button>
                        </li>
                        <li>
                          <button type="button" class="dropdown-item btn-promosi" data-bs-toggle="modal"
                            data-bs-target="#promosiModal" data-id="{{ $item->karyawan_id }}"
                            data-nama="{{ $item->nama_tk }}"
                            data-jabatan="{{ $jabatan[$item->karyawan_id]->jabatan ?? 'Belum ada' }}">
                            <i class="fas fa-arrow-up me-2 text-primary"></i> Promosi Jabatan
                          </button>
                        </li>
                        <li>
                          <button type="button" class="dropdown-item btn-edit-shift" data-bs-toggle="modal"
                            data-bs-target="#editShiftModal" data-id="{{ $item->karyawan_id }}"
                            data-nama="{{ $item->nama_tk }}"
                            data-shift="{{ $harianShift[$item->karyawan_id]->harianshift ?? 'Belum ada' }}">
                            <i class="fas fa-clock me-2 text-secondary"></i> Jadwal Kerja
                          </button>
                        </li>
                        <li>
                          <button type="button" class="dropdown-item btn-edit-area" data-bs-toggle="modal"
                            data-bs-target="#editAreaModal" data-id="{{ $item->karyawan_id }}"
                            data-nama="{{ $item->nama_tk }}"
                            data-area="{{ $area[$item->karyawan_id]->area ?? 'Belum ada' }}">
                            <i class="fas fa-map-marker-alt me-2 text-secondary"></i> Penempatan
                          </button>
                        </li>
                        <li>
                          <button type="button" class="dropdown-item btn-edit-pakaian" data-bs-toggle="modal"
                            data-bs-target="#editPakaianModal" data-id="{{ $item->karyawan_id }}"
                            data-nama="{{ $item->nama_tk }}"
                            data-nilai="{{ $item->pakaianTerakhir->nilai_jatah ?? 'Belum ada' }}"
                            data-baju="{{ $item->pakaianTerakhir->ukuran_baju ?? 'Belum ada' }}"
                            data-celana="{{ $item->pakaianTerakhir->ukuran_celana ?? 'Belum ada' }}">
                            <i class="fas fa-tshirt me-2 text-secondary"></i> Pakaian
                          </button>
                        </li>
                      </ul>
                    </div>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal mutasi -->
  <div class="modal fade" id="mutasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form action="{{ url('/mutasi-paket') }}" method="POST">
        @csrf
        <input type="hidden" name="karyawan_id" id="mutasi_karyawan_id">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="mutasiModalLabel">Mutasi Paket - <span id="mutasi_nama"></span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-info border-0 d-flex align-items-center mb-3">
                <i class="fas fa-user-circle fa-2x me-3"></i>
                <div>
                    <div class="small text-uppercase text-muted fw-bold">Karyawan</div>
                    <div class="h6 mb-0 fw-bold" id="mutasi_nama_detail"></div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label text-muted small text-uppercase fw-bold">Paket Saat Ini</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-box"></i></span>
                    <input type="text" class="form-control bg-white fw-bold text-primary" id="mutasi_paket_sekarang" readonly>
                </div>
            </div>
            <div class="mb-3">
              <label for="paket_id" class="form-label">Mutasi ke Paket:</label>
              <select name="paket_id" class="form-select" required>
                <option value="">-- Pilih Paket --</option>
                @foreach ($paketList as $paket)
                  <option value="{{ $paket->paket_id }}">{{ $paket->paket }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label for="beg_date" class="form-label">Tanggal TMT</label>
              <input type="date" class="form-control" name="beg_date">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan Mutasi</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- Modal Promosi -->
  <div class="modal fade" id="promosiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form action="{{ url('/promosi-jabatan') }}" method="POST">
        @csrf
        <input type="hidden" name="karyawan_id" id="promosi_karyawan_id">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="mutasiModalLabel">Promosi Jabatan - <span id="promosi_nama"></span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p><strong>Jabatan saat ini:</strong> <span id="promosi_jabatan_sekarang"></span></p>
            <div class="mb-3">
              <label for="kode_jabatan" class="form-label">Promosi ke Jabatan:</label>
              <select name="kode_jabatan" class="form-select" required>
                <option value="">-- Pilih Jabatan --</option>
                @foreach ($jabatanList as $jabatan)
                  <option value="{{ $jabatan->kode_jabatan }}">{{ $jabatan->jabatan }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label for="beg_date" class="form-label">Tanggal TMT</label>
              <input type="date" class="form-control" name="beg_date">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan Promosi</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- Modal Harian/Shift -->
  <div class="modal fade" id="editShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form action="{{ url('/ganti-shift') }}" method="POST">
        @csrf
        <input type="hidden" name="karyawan_id" id="shift_karyawan_id">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Harian/Shift - <span id="shift_nama"></span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p><strong>Shift saat ini:</strong> <span id="shift_saat_ini"></span></p>
            <div class="mb-3">
              <label class="form-label">Pilih Shift Baru:</label>
              <select name="kode_harianshift" class="form-select" required>
                <option value="1" selected>Harian</option>
                <option value="2">Shift</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Tanggal Berlaku</label>
              <input type="date" class="form-control" name="beg_date" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- Modal Area -->
  <div class="modal fade" id="editAreaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form action="{{ url('/ganti-area') }}" method="POST">
        @csrf
        <input type="hidden" name="karyawan_id" id="area_karyawan_id">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Area - <span id="area_nama"></span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p><strong>Area saat ini:</strong> <span id="area_saat_ini"></span></p>
            <div class="mb-3">
              <label class="form-label">Pilih Area Baru:</label>
              <select name="area_id" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="1">Lapangan</option>
                <option value="2">Non Lapangan</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- Modal Pakaian -->
  <div class="modal fade" id="editPakaianModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form action="{{ url('/ganti-pakaian') }}" method="POST">
        @csrf
        <input type="hidden" name="karyawan_id" id="pakaian_karyawan_id">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Pakaian - <span id="pakaian_nama"></span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p><strong>Ukuran Baju & Celana saat ini:</strong> <span id="baju_saat_ini"></span>, <span
                id="celana_saat_ini"></span></p>
            <div class="mb-3">
              <label class="form-label">Ukuran Baju:</label>
              <select name="ukuran_baju" class="form-select" required>
                <option value="">-- Pilih --</option>
                @foreach ($masterUkuran as $ukuran)
                  <option value="{{ $ukuran->nama_ukuran }}">{{ $ukuran->nama_ukuran }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Ukuran Celana</label>
              <input type="number" class="form-control" name="ukuran_celana" min="25" max="45" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Tanggal Berlaku</label>
              <input type="date" class="form-control" name="beg_date" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
    </div>
  </div>

@push('scripts')
  <script>
    $(document).ready(function() {
        // Event delegation for Mutasi Paket
        $(document).on('click', '.btn-mutasi', function () {
            const nama = $(this).data('nama');
            const paket = $(this).data('paket');
            const id = $(this).data('id');

            $('#mutasi_nama').text(nama); // Title
            $('#mutasi_nama_detail').text(nama); // Body
            $('#mutasi_paket_sekarang').val(paket); // Body Input
            $('#mutasi_karyawan_id').val(id);
        });

        // Event delegation for Promosi Jabatan
        $(document).on('click', '.btn-promosi', function () {
            const nama = $(this).data('nama');
            const jabatan = $(this).data('jabatan');
            const id = $(this).data('id');

            $('#promosi_nama').text(nama);
            $('#promosi_jabatan_sekarang').text(jabatan);
            $('#promosi_karyawan_id').val(id);
        });

        // Event delegation for Edit Shift
        $(document).on('click', '.btn-edit-shift', function () {
            const nama = $(this).data('nama');
            const shift = $(this).data('shift');
            const id = $(this).data('id');

            $('#shift_nama').text(nama);
            $('#shift_saat_ini').text(shift);
            $('#shift_karyawan_id').val(id);
        });

        // Event delegation for Edit Area
        $(document).on('click', '.btn-edit-area', function () {
            const nama = $(this).data('nama');
            const area = $(this).data('area');
            const id = $(this).data('id');

            $('#area_nama').text(nama);
            $('#area_saat_ini').text(area);
            $('#area_karyawan_id').val(id);
        });

        // Event delegation for Edit Pakaian
        $(document).on('click', '.btn-edit-pakaian', function () {
            const nama = $(this).data('nama');
            const id = $(this).data('id');
            const nilai = $(this).data('nilai');
            const baju = $(this).data('baju');
            const celana = $(this).data('celana');
            
            $('#pakaian_nama').text(nama);
            $('#pakaian_karyawan_id').val(id);
            $('#nilai_saat_ini').text(nilai);
            $('#baju_saat_ini').text(baju);
            $('#celana_saat_ini').text(celana);
        });

        // DataTables Filter Logic
        var table = $('#datatableSimple').DataTable();

        // Apply default filter for Status = Aktif
        table.column(5).search('Aktif').draw();

        // Filter event handlers
        $('#filterStatus').on('change', function() {
            table.column(5).search(this.value).draw();
        });
        $('#filterPerusahaan').on('change', function() {
            table.column(4).search(this.value).draw();
        });
        $('#filterTipe').on('change', function() {
            table.column(6).search(this.value).draw();
        });
        $('#resetFilter').on('click', function() {
            $('#filterStatus').val('');
            $('#filterPerusahaan').val('');
            $('#filterTipe').val('');
            table.columns().search('').draw();
        });
    });
  </script>
@endpush

@endsection