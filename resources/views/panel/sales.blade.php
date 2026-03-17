@extends('layouts.panel')

@section('page-title')
    Sales Reports
@endsection

@section('page-description')
    Track revenue, completed services, and customer insights over time.
@endsection

@section('page-actions')
    <span class="text-secondary fw-semibold small d-none d-md-inline-flex align-items-center gap-2">
        <i class="fa-regular fa-calendar"></i>{{ now()->format('M d, Y') }}
    </span>

    <button class="btn btn-light border rounded-4 px-3 py-2 fw-semibold" data-action="export-csv">
        <i class="fa-solid fa-file-csv me-2"></i>Export CSV
    </button>
@endsection

@section('content')
    <panel-sales></panel-sales>
@endsection
