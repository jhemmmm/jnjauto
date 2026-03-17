@extends('layouts.panel')

@section('page-title')
    Settings
@endsection

@section('page-description')
    Manage your profile, password, and business configuration.
@endsection

@section('page-actions')
    <span class="text-secondary fw-semibold small d-none d-md-inline-flex align-items-center gap-2">
        <i class="fa-regular fa-calendar"></i>{{ now()->format('M d, Y') }}
    </span>
@endsection

@section('content')
    <panel-settings></panel-settings>
@endsection
