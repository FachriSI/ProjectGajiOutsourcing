@extends('layouts.main')

@section('content')
<main>
    <div class="container-fluid px-4">
        <!-- Modern Header Container -->
        <div class="bg-white p-4 rounded shadow-sm mb-4 mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-circle me-2 text-primary"></i> User Profile</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 mt-2 bg-transparent p-0">
                            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Profile & Activity Log</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>

        <div class="row">
            <!-- User Details Card -->
            <div class="col-xl-4">
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom fw-bold text-primary py-3">
                        <i class="fas fa-user me-1"></i> User Details
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="fas fa-user fa-3x text-secondary"></i>
                            </div>
                            <h5 class="mt-3 font-weight-bold">{{ $user->name }}</h5>
                            <p class="text-muted small mb-0">{{ $user->email }}</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="small fw-bold text-muted">Joined At</span>
                                <span class="text-dark">{{ $user->created_at->format('d M Y') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="small fw-bold text-muted">Role</span>
                                <span class="badge bg-primary rounded-pill">Admin</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Activity Log Card -->
            <div class="col-xl-8">
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom fw-bold text-primary py-3">
                        <i class="fas fa-history me-1"></i> Activity Log
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Time</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th class="pe-4">IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                    <tr>
                                        <td class="ps-4 text-nowrap small text-muted">
                                            <i class="far fa-clock me-1"></i> {{ $log->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td>
                                            @if($log->action == 'Login')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Login</span>
                                            @elseif($log->action == 'Logout')
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3">Logout</span>
                                            @elseif($log->action == 'Update Profile')
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">Update Profile</span>
                                            @else
                                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">{{ $log->action }}</span>
                                            @endif
                                        </td>
                                        <td class="small">{{ $log->description }}</td>
                                        <td class="pe-4 small font-monospace text-muted">{{ $log->ip_address }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">No activity logs found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 py-3">
                        <div class="d-flex justify-content-end">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
