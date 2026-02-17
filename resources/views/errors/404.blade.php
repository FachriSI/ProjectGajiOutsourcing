@extends('errors.layout')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
    <div class="error-code shake">404</div>
    <div class="error-message">Oops! Halaman Tidak Ditemukan</div>
    <p class="error-desc lead text-gray-800 mb-5">
        Sepertinya Anda tersesat. Halaman yang Anda cari mungkin telah dihapus,
        namanya diubah, atau sementara tidak tersedia.
    </p>
    <a href="{{ url('/') }}" class="btn btn-primary btn-home">
        <i class="fas fa-home me-2"></i>Kembali ke Dashboard
    </a>
@endsection
