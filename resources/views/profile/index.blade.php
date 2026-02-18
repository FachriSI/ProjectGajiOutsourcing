@extends('layouts.main')

@section('content')

    <!-- 1. Hero Section -->
    <div class="profile-header text-white mb-4 -mx-4" style="height: 200px; position: relative; background: linear-gradient(90deg, #1e3a8a 0%, #3b82f6 100%); margin-left: -1.5rem; margin-right: -1.5rem;">
        <div class="container-fluid px-4 h-100">
            <div class="d-flex flex-column justify-content-center h-100">
                <h1 class="h3 mb-0 fw-bold">Profil Saya</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-white-50 text-decoration-none">Dasbor</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Profil</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- 2. Main Content (Overlapping Hero) -->
    <div style="margin-top: -60px;">
        <div class="row">
            <!-- Left Column: User Card -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow border-0 mb-4 text-center">
                    <div class="card-body pb-4">
                        <div class="position-relative d-inline-block mb-3">
                            <div class="bg-white rounded-circle p-1 shadow-sm mt-n5" style="width: 130px; height: 130px;">
                                <div class="bg-light rounded-circle w-100 h-100 d-flex align-items-center justify-content-center overflow-hidden">
                                     <i class="fas fa-user fa-4x text-secondary"></i>
                                </div>
                            </div>
                            <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-white rounded-circle">
                                <span class="visually-hidden">Online</span>
                            </span>
                        </div>
                        
                        <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-3">{{ $user->email }}</p>
                        
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
                                <i class="fas fa-shield-alt me-1"></i> {{ $user->role ?? 'Pengguna' }}
                            </span>
                             <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-2">
                                <i class="fas fa-calendar-alt me-1"></i> Bergabung {{ $user->created_at->format('M Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats (Example) -->
                 <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-uppercase small text-muted font-monospace">Ringkasan Akun</h6>
                         <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Login</span>
                            <span class="fw-bold text-dark">
                                {{ \App\Models\ActivityLog::where('user_id', $user->id)->where('action', 'Login')->count() }}
                            </span>
                        </div>
                         <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Terakhir Aktif</span>
                            <span class="fw-bold text-dark text-end" style="font-size: 0.9rem;">
                                {{ $user->updated_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Tabs -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pb-0 pt-4 px-4">
                        <ul class="nav nav-tabs profile-tabs border-bottom-0" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-selected="true">
                                    <i class="fas fa-history me-2"></i>Log Aktivitas
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-selected="false">
                                    <i class="fas fa-cog me-2"></i>Pengaturan Akun
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4 pt-0">
                         <div class="tab-content" id="profileTabsContent">
                            
                            <!-- Tab 1: Activity Log -->
                            <div class="tab-pane fade show active mt-3" id="activity" role="tabpanel">
                                <h6 class="fw-bold mb-3 text-primary">Aktivitas Terbaru</h6>
                                <div class="timeline-container">
                                    @forelse($logs as $log)
                                        <div class="d-flex pb-4 border-start border-2 ms-2 ps-4 position-relative border-light">
                                            <div class="position-absolute top-0 start-0 translate-middle rounded-circle bg-white border border-2 d-flex align-items-center justify-content-center" style="width: 16px; height: 16px; margin-top: 4px; border-color: #dee2e6 !important;"></div>
                                            
                                            <div class="w-100">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <div>
                                                         @if($log->action == 'Login')
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Masuk</span>
                                                        @elseif($log->action == 'Logout')
                                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2">Keluar</span>
                                                        @elseif($log->action == 'Update Profile')
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2">Ubah Profil</span>
                                                        @elseif($log->action == 'Update')
                                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2">Ubah Data</span>
                                                        @elseif($log->action == 'Create')
                                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2">Tambah Data</span>
                                                        @elseif($log->action == 'Delete' || $log->action == 'Soft Delete' || $log->action == 'Force Delete')
                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2">Hapus Data</span>
                                                        @else
                                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2">{{ $log->action }}</span>
                                                        @endif
                                                        <span class="fw-bold text-dark ms-1">{{ $log->description }}</span>
                                                    </div>
                                                    <small class="text-muted text-nowrap">{{ $log->created_at->diffForHumans() }}</small>
                                                </div>
                                                <div class="small text-muted bg-light p-2 rounded mt-1">
                                                    <i class="fas fa-globe me-1"></i> IP: {{ $log->ip_address }} | 
                                                    <i class="far fa-clock me-1"></i> {{ $log->created_at->format('d M Y H:i:s') }}
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-5">
                                            <p class="mb-0">Tidak ada riwayat aktivitas.</p>
                                        </div>
                                    @endforelse
                                    
                                    <div class="mt-3">
                                        {{ $logs->links() }}
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: Settings (Mockup) -->
                            <div class="tab-pane fade mt-3" id="settings" role="tabpanel">
                                <h6 class="fw-bold mb-3 text-primary">Ubah Profil</h6>
                                <form action="{{ route('profile.update') }}" method="POST" class="mb-5">
                                    @csrf
                                    @method('PUT')
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Nama Lengkap</label>
                                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Alamat Email</label>
                                            <input type="email" class="form-control" value="{{ $user->email }}" readonly disabled>
                                            <small class="text-muted">Email tidak dapat diubah</small>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    
                                    <h6 class="fw-bold mb-3 text-danger">Keamanan (Opsional)</h6>
                                    <div class="row mb-3">
                                         <div class="col-md-6">
                                            <label class="form-label small fw-bold">Kata Sandi Baru</label>
                                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Konfirmasi Kata Sandi</label>
                                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<style>
    /* Custom Timeline Styles */
    .border-light { border-color: #e3e6f0 !important; }
    
    /* Clean Tabs */
    .profile-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        padding-bottom: 1rem;
        padding-top: 0.5rem;
    }
    .profile-tabs .nav-link:hover {
        color: #4e73df;
        background: transparent;
        border-color: transparent;
    }
    .profile-tabs .nav-link.active {
        color: #4e73df;
        background: transparent;
        border-bottom: 3px solid #4e73df;
        border-top: none;
        border-left: none;
        border-right: none;
    }
</style>
@endsection
