@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')

@endpush

@section('content')


    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="stat-card blue">
                <div class="stat-icon blue"><i class="fas fa-user-md"></i></div>
                <div>
                    <div class="stat-label">Total Banner</div>
                    <div class="stat-value">{{ $totalBanner }}</div>
                </div>

            </div>
            <div class="stat-card blue">
                <div class="stat-icon blue"><i class="fas fa-user-md"></i></div>
                <div>
                    <div class="stat-label">Total Employee</div>
                    <div class="stat-value">{{ $totalEmployee }}</div>
                </div>

            </div>

        </div>

    </div>

@endsection
