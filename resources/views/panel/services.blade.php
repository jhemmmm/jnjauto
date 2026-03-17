@extends('layouts.panel')

@section('page-title')
    Services & Prices
@endsection

@section('page-description')
    Manage your car wash services and vehicle size categories.
@endsection

@section('page-actions')
    <span class="text-secondary fw-semibold small d-none d-md-inline-flex align-items-center gap-2">
        <i class="fa-regular fa-calendar"></i>{{ now()->format('M d, Y') }}
    </span>

    <button class="btn btn-light border rounded-4 px-3 py-2 fw-semibold" data-action="add-size">
        <i class="fa-solid fa-ruler-combined me-2"></i>Add Size
    </button>

    <button class="btn btn-info text-white rounded-4 px-3 py-2 fw-semibold btn-gradient" data-action="add-service">
        <i class="fa-solid fa-plus me-2"></i>Add Service
    </button>
@endsection

@section('content')
    <panel-services></panel-services>
@endsection
