@extends('layouts.main')

@section('title', 'Hasil import')
@section('content')
<div class="container mt-4">
    <h4>{{ $title ?? 'Hasil Import' }}</h4>

    @if (!empty($successMessage))
        <div class="alert alert-success">{{ $successMessage }}</div>
    @endif

    @if (!empty($errorMessage))
        <div class="alert alert-danger">{{ $errorMessage }}</div>
    @endif

    <ul class="list-group">
        @foreach ($logs as $log)
            @php
                $class = 'list-group-item';
                if (str_contains($log, 'SUKSES')) {
                    $class .= ' list-group-item-success';
                } elseif (str_contains($log, 'GAGAL')) {
                    $class .= ' list-group-item-danger';
                } else {
                    $class .= ' list-group-item-warning';
                }
            @endphp
            <li class="{{ $class }}">
                {{ $log }}
            </li>
        @endforeach
    </ul>

    <a href="{{ $backUrl ?? '/karyawan' }}" class="btn btn-primary mt-3">{{ $backLabel ?? 'Kembali ke Data Karyawan' }}</a>
</div>
@endsection
