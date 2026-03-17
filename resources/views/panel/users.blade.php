@extends('layouts.panel')

@section('page-title')
    Users
@endsection

@section('page-description')
    Manage user accounts and assign roles.
@endsection

@section('page-actions')
    <span class="text-secondary fw-semibold small d-none d-md-inline-flex align-items-center gap-2">
        <i class="fa-regular fa-calendar"></i>{{ now()->format('M d, Y') }}
    </span>

    <button class="btn btn-info text-white rounded-4 px-3 py-2 fw-semibold btn-gradient" data-action="add-user">
        <i class="fa-solid fa-user-plus me-2"></i>Add User
    </button>
@endsection

@section('content')
    <panel-users></panel-users>
@endsection
