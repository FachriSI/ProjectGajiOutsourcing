@extends('errors.layout')

@section('title', '500 - Terjadi Kesalahan Server')

@section('content')
    <div class="error-code text-danger">500</div>
    <div class="error-message">Terjadi Kesalahan Server</div>
    <p class="error-desc lead text-gray-800 mb-5">
        Maaf, ada masalah di pihak kami. Server sedang mengalami gangguan.
        Silakan coba lagi beberapa saat lagi.
    </p>
    <a href="{{ url('/') }}" class="btn btn-danger btn-home">
        <i class="fas fa-sync-alt me-2"></i>Refresh Halaman
    </a>
@endsection
