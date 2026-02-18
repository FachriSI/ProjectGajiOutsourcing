@extends('layouts.main')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 mb-4">Hasil Pencarian</h1>
    
    <div class="mb-4">
        <form action="{{ route('global.search') }}" method="GET" class="d-flex">
            <input class="form-control me-2" type="search" name="q" placeholder="Cari Karyawan, Paket, atau Perusahaan..." aria-label="Search" value="{{ $query }}">
            <button class="btn btn-primary" type="submit">Cari</button>
        </form>
    </div>

    @if($karyawan->isEmpty() && $paket->isEmpty() && $perusahaan->isEmpty())
        <div class="alert alert-info">
            Tidak ditemukan hasil untuk pencarian "<strong>{{ $query }}</strong>".
        </div>
    @else
        <p class="text-muted">Menampilkan hasil untuk: <strong>{{ $query }}</strong></p>
    @endif

    <div class="row">
        <!-- Karyawan Results -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 card-gradient-blue border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="fw-bold text-primary"><i class="fas fa-user me-2"></i>Karyawan</h5>
                </div>
                <div class="card-body">
                    @if($karyawan->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($karyawan as $k)
                                <a href="{{ url('/detail-karyawan/' . $k->id) }}" class="list-group-item list-group-item-action bg-transparent d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $k->nama_tk }}</div>
                                        <small class="text-muted">{{ $k->nik }} - {{ $k->jabatan->jabatan ?? '-' }}</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small ms-2">Tidak ditemukan data karyawan.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Paket Results -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 card-gradient-blue border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="fw-bold text-primary"><i class="fas fa-box me-2"></i>Paket</h5>
                </div>
                <div class="card-body">
                    @if($paket->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($paket as $p)
                                <a href="{{ url('/paket/' . $p->paket_id) }}" class="list-group-item list-group-item-action bg-transparent d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $p->paket }}</div>
                                        <small class="text-muted">ID: {{ $p->paket_id }}</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small ms-2">Tidak ditemukan data paket.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Perusahaan Results -->
        <div class="col-md-6 mb-4">
             <div class="card h-100 card-gradient-blue border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="fw-bold text-primary"><i class="fas fa-building me-2"></i>Perusahaan</h5>
                </div>
                <div class="card-body">
                    @if($perusahaan->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($perusahaan as $p)
                                <a href="{{ url('/perusahaan') }}" class="list-group-item list-group-item-action bg-transparent d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $p->perusahaan }}</div>
                                        <small class="text-muted">{{ $p->kode_perusahaan }}</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small ms-2">Tidak ditemukan data perusahaan.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Jabatan Results -->
        <div class="col-md-6 mb-4">
             <div class="card h-100 card-gradient-blue border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="fw-bold text-primary"><i class="fas fa-id-badge me-2"></i>Jabatan</h5>
                </div>
                <div class="card-body">
                    @if($jabatan->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($jabatan as $j)
                                <a href="{{ url('/jabatan') }}" class="list-group-item list-group-item-action bg-transparent d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $j->jabatan }}</div>
                                        <small class="text-muted">{{ $j->kode_jabatan }}</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small ms-2">Tidak ditemukan data jabatan.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Departemen Results -->
        <div class="col-md-6 mb-4">
             <div class="card h-100 card-gradient-blue border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="fw-bold text-primary"><i class="fas fa-building me-2"></i>Departemen</h5>
                </div>
                <div class="card-body">
                    @if($departemen->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($departemen as $d)
                                <a href="{{ url('/departemen') }}" class="list-group-item list-group-item-action bg-transparent d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $d->departemen }}</div>
                                        <small class="text-muted">{{ $d->kode_departemen }}</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small ms-2">Tidak ditemukan data departemen.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Lokasi Results -->
        <div class="col-md-6 mb-4">
             <div class="card h-100 card-gradient-blue border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="fw-bold text-primary"><i class="fas fa-map-marker-alt me-2"></i>Lokasi</h5>
                </div>
                <div class="card-body">
                    @if($lokasi->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($lokasi as $l)
                                <a href="{{ url('/lokasi') }}" class="list-group-item list-group-item-action bg-transparent d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $l->lokasi }}</div>
                                        <small class="text-muted">{{ $l->kode_lokasi }}</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small ms-2">Tidak ditemukan data lokasi.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Unit Kerja Results -->
        <div class="col-md-6 mb-4">
             <div class="card h-100 card-gradient-blue border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="fw-bold text-primary"><i class="fas fa-briefcase me-2"></i>Unit Kerja</h5>
                </div>
                <div class="card-body">
                    @if($unitKerja->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($unitKerja as $u)
                                <a href="{{ url('/unit-kerja') }}" class="list-group-item list-group-item-action bg-transparent d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $u->unit_kerja }}</div>
                                        <small class="text-muted">{{ $u->kode_unit_kerja }}</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small ms-2">Tidak ditemukan data unit kerja.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Fungsi Results -->
        <div class="col-md-6 mb-4">
             <div class="card h-100 card-gradient-blue border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="fw-bold text-primary"><i class="fas fa-cogs me-2"></i>Fungsi</h5>
                </div>
                <div class="card-body">
                    @if($fungsi->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($fungsi as $f)
                                <a href="{{ url('/fungsi') }}" class="list-group-item list-group-item-action bg-transparent d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $f->fungsi }}</div>
                                        <small class="text-muted">{{ $f->kode_fungsi }}</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small ms-2">Tidak ditemukan data fungsi.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
