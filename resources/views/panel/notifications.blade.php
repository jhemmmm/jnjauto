@extends('layouts.panel')

@section('page-title')
    Notifications
@endsection

@section('page-description')
    Stay updated with appointment bookings, status changes, and inventory alerts.
@endsection

@section('page-actions')
    <span class="text-secondary fw-semibold small d-none d-md-inline-flex align-items-center gap-2">
        <i class="fa-regular fa-calendar"></i>{{ now()->format('M d, Y') }}
    </span>
@endsection

@section('content')
    <panel-notifications></panel-notifications>
@endsection
