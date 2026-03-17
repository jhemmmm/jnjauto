@extends('layouts.panel')

@section('page-title')
    Dashboard
@endsection

@section('page-description')
    Monitor live bookings, service slots, staff activity, and revenue for your car wash business.
@endsection

@section('page-actions')
    <span class="text-secondary fw-semibold small d-none d-md-inline-flex align-items-center gap-2">
        <i class="fa-regular fa-calendar"></i>{{ now()->format('M d, Y') }}
    </span>

    <button class="btn btn-info text-white rounded-4 px-3 py-2 fw-semibold btn-gradient" data-action="add-booking-dashboard">
        <i class="fa-solid fa-plus me-2"></i>Add Booking
    </button>
@endsection

@section('content')
    <panel-dashboard></panel-dashboard>
@endsection
