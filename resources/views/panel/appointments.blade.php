@extends('layouts.panel')

@section('page-title')
    Appointments
@endsection

@section('page-description')
    View, filter, and manage all customer bookings in one place.
@endsection

@section('page-actions')
    <span class="text-secondary fw-semibold small d-none d-md-inline-flex align-items-center gap-2">
        <i class="fa-regular fa-calendar"></i>{{ now()->format('M d, Y') }}
    </span>

    <button class="btn btn-info text-white rounded-4 px-3 py-2 fw-semibold btn-gradient" data-action="add-appointment">
        <i class="fa-solid fa-plus me-2"></i>Add Booking
    </button>
@endsection

@section('content')
    <panel-appointments></panel-appointments>
@endsection
