@extends('errors.layout')

@section('title', '403 - Akses Ditolak')

@section('content')
    <div class="error-code text-warning">403</div>
    <div class="error-message">Akses Ditolak</div>
    <p class="error-desc lead text-gray-800 mb-5">
        Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
        Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
    </p>
    <a href="{{ url('/') }}" class="btn btn-warning text-white btn-home">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
@endsection
