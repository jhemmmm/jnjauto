@extends('layouts.panel')

@section('page-title')
    Inventory
@endsection

@section('page-description')
    Manage car wash supplies, track stock levels, and record inventory movements.
@endsection

@section('page-actions')
    <span class="text-secondary fw-semibold small d-none d-md-inline-flex align-items-center gap-2">
        <i class="fa-regular fa-calendar"></i>{{ now()->format('M d, Y') }}
    </span>

    <button class="btn btn-light border rounded-4 px-3 py-2 fw-semibold" data-action="add-category">
        <i class="fa-solid fa-folder-plus me-2"></i>Add Category
    </button>

    <button class="btn btn-info text-white rounded-4 px-3 py-2 fw-semibold btn-gradient" data-action="add-item">
        <i class="fa-solid fa-plus me-2"></i>Add Item
    </button>
@endsection

@section('content')
    <panel-inventory></panel-inventory>
@endsection
