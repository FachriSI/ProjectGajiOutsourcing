@extends('layouts.main')

@section('content')
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">User Profile</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Profile & Activity Log</li>
        </ol>

        <div class="row">
            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-user me-1"></i>
                        User Details
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small mb-1 fw-bold">Name</label>
                            <div class="form-control-plaintext">{{ $user->name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1 fw-bold">Email</label>
                            <div class="form-control-plaintext">{{ $user->email }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1 fw-bold">Joined At</label>
                            <div class="form-control-plaintext">{{ $user->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-history me-1"></i>
                        Activity Log
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                    <td>
                                        @if($log->action == 'Login')
                                            <span class="badge bg-success">Login</span>
                                        @elseif($log->action == 'Logout')
                                            <span class="badge bg-secondary">Logout</span>
                                        @elseif($log->action == 'Update Profile')
                                            <span class="badge bg-primary">Update Profile</span>
                                        @else
                                            <span class="badge bg-info">{{ $log->action }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->description }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No activity found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
