@extends('layouts.main')

@section('title', 'History Kontrak')

@section('content')
    <div class="container-fluid py-3">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                <i class="fas fa-history me-2"></i>
                History Perubahan Kontrak: {{ $paket->paket }}
            </h4>
            <div>
                <a href="{{ route('kalkulator.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Paket Info -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Paket:</strong> {{ $paket->paket }}</p>
                        <p class="mb-1"><strong>Unit Kerja:</strong> {{ $paket->unitKerja->unit_kerja ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Kuota:</strong> {{ $paket->kuota_paket }} orang</p>
                        <p class="mb-1"><strong>Total History:</strong> {{ $histories->total() }} perubahan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="row">
            <div class="col-md-12">
                @if($histories->count() > 0)
                    <div class="timeline">
                        @foreach($histories as $history)
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header {{ 
                                $history->change_type == 'ump_change' ? 'bg-warning' : 
                                ($history->change_type == 'kuota_change' ? 'bg-info' : 
                                ($history->change_type == 'employee_change' ? 'bg-primary' : 'bg-secondary'))
                            }} text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas {{ 
                                            $history->change_type == 'ump_change' ? 'fa-dollar-sign' : 
                                            ($history->change_type == 'kuota_change' ? 'fa-users-cog' : 
                                            ($history->change_type == 'employee_change' ? 'fa-user-plus' : 'fa-sync'))
                                        }} me-2"></i>
                                        {{ 
                                            $history->change_type == 'ump_change' ? 'Perubahan UMP' : 
                                            ($history->change_type == 'kuota_change' ? 'Perubahan Kuota Paket' : 
                                            ($history->change_type == 'employee_change' ? 'Perubahan Jumlah Karyawan' : 'Recalculation Manual'))
                                        }}
                                    </h6>
                                    <small>{{ $history->changed_at->format('d M Y H:i') }}</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Description -->
                                    <div class="col-md-12 mb-3">
                                        <p class="mb-0"><strong>Deskripsi:</strong> {{ $history->change_description ?? '-' }}</p>
                                        @if($history->changer)
                                        <p class="mb-0 text-muted"><small>Oleh: {{ $history->changer->name ?? 'System' }}</small></p>
                                        @endif
                                    </div>

                                    <!-- Old vs New Values -->
                                    <div class="col-md-5">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="text-muted mb-2"><i class="fas fa-arrow-left"></i> Nilai Lama</h6>
                                                @if($history->old_total)
                                                <p class="mb-1">
                                                    <strong>Total:</strong> 
                                                    Rp {{ number_format($history->old_total, 0, ',', '.') }}
                                                </p>
                                                @endif
                                                @if($history->old_value)
                                                    @foreach($history->old_value as $key => $value)
                                                        @if($key != 'total_nilai_kontrak')
                                                        <p class="mb-1">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                                            {{ is_numeric($value) ? number_format($value, 0, ',', '.') : $value }}
                                                        </p>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delta -->
                                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                                        <div class="text-center">
                                            <i class="fas fa-arrow-right fa-2x {{ $history->delta >= 0 ? 'text-success' : 'text-danger' }}"></i>
                                            @if($history->delta)
                                            <p class="mt-2 mb-0">
                                                <span class="badge {{ $history->delta >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $history->delta >= 0 ? '+' : '' }}Rp {{ number_format($history->delta, 0, ',', '.') }}
                                                </span>
                                            </p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- New Values -->
                                    <div class="col-md-5">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="text-success mb-2"><i class="fas fa-arrow-right"></i> Nilai Baru</h6>
                                                @if($history->new_total)
                                                <p class="mb-1">
                                                    <strong>Total:</strong> 
                                                    Rp {{ number_format($history->new_total, 0, ',', '.') }}
                                                </p>
                                                @endif
                                                @if($history->new_value)
                                                    @foreach($history->new_value as $key => $value)
                                                        @if($key != 'total_nilai_kontrak')
                                                        <p class="mb-1">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                                            {{ is_numeric($value) ? number_format($value, 0, ',', '.') : $value }}
                                                        </p>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $histories->links() }}
                    </div>
                @else
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-history fa-5x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada history perubahan</h5>
                            <p class="text-muted">History akan muncul ketika ada perubahan UMP, kuota, atau jumlah karyawan</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline .card {
            margin-left: 50px;
            position: relative;
        }
        .timeline .card::before {
            content: '';
            position: absolute;
            left: -38px;
            top: 20px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #0d6efd;
        }
    </style>
@endsection
