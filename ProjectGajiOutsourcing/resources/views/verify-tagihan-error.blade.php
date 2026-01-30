@extends('layouts.public')

@section('title', 'Token Tidak Valid')

@section('content')
    <div class="verification-card">
        <!-- Header -->
        <div class="verification-header bg-danger text-white">
            <h3 class="mb-0">
                <i class="fas fa-times-circle fa-2x mb-3"></i>
                <br>
                Verifikasi Gagal
            </h3>
        </div>

        <!-- Body -->
        <div class="verification-body">
            <!-- Error State -->
            <div class="alert alert-danger border-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>{{ $message }}</strong>
            </div>

            <div class="text-center py-4">
                <i class="fas fa-times-circle text-danger" style="font-size: 64px; opacity: 0.3;"></i>
                <p class="text-muted mt-4">
                    Token verifikasi yang Anda gunakan tidak valid atau sudah kedaluwarsa.<br>
                    Silakan hubungi pihak terkait untuk mendapatkan dokumen yang valid.
                </p>
            </div>
        </div>
    </div>
@endsection
