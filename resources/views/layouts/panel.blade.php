<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings['business_name'] ?? config('app.name', 'Laravel') }} - Panel</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased d-flex flex-column min-vh-100 admin-body">

    <div id="app" class="container-fluid px-0 grow">
        <div class="row g-0 min-vh-100">

            <!-- Desktop Sidebar -->
            <aside class="col-lg-3 col-xl-2 admin-sidebar border-end d-none d-lg-block">
                <div class="p-3 p-xl-4 h-100 d-flex flex-column">

                    <div class="d-flex align-items-center gap-3 px-2 mb-4">
                        <div
                            class="brand-icon d-inline-flex align-items-center justify-content-center rounded-4 overflow-hidden">
                            @if (!empty($settings['business_logo_url']))
                                <img src="{{ $settings['business_logo_url'] }}" alt="Logo" class="w-100 h-100"
                                    style="object-fit: cover;" />
                            @else
                                <i class="fa-solid fa-droplet"></i>
                            @endif
                        </div>
                        <div>
                            <h1 class="h4 fw-bold mb-0"><span
                                    class="text-info-emphasis">{{ $settings['app_name_first'] ?? 'JNJ' }}</span>{{ $settings['app_name_last'] ?? 'Auto' }}
                            </h1>
                            <div class="small text-secondary">Admin Panel</div>
                        </div>
                    </div>

                    @include('panel.partials.sidebar-nav')

                    <div class="mt-auto pt-4">
                        <div class="promo-card rounded-4 p-4 text-white shadow-sm">
                            <h3 class="h6 fw-bold mb-2">Need today's summary?</h3>
                            <p class="small mb-3 text-white-50">
                                Generate a printable end-of-day report for bookings, walk-ins, active washers, and
                                service revenue.
                            </p>
                            <a href="{{ route('panel.api.export.report') }}"
                                class="btn btn-light fw-semibold rounded-4 px-3">
                                <i class="fa-solid fa-file-export me-2"></i>Export Report
                            </a>
                        </div>
                    </div>

                </div>
            </aside>

            <!-- Mobile Sidebar -->
            <div class="offcanvas offcanvas-start admin-offcanvas border-0" tabindex="-1" id="mobileSidebar"
                aria-labelledby="mobileSidebarLabel">
                <div class="offcanvas-header px-3 pt-3 pb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="brand-icon d-inline-flex align-items-center justify-content-center rounded-4 overflow-hidden">
                            @if (!empty($settings['business_logo_url']))
                                <img src="{{ $settings['business_logo_url'] }}" alt="Logo" class="w-100 h-100"
                                    style="object-fit: cover;" />
                            @else
                                <i class="fa-solid fa-droplet"></i>
                            @endif
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0" id="mobileSidebarLabel"><span
                                    class="text-info-emphasis">{{ $settings['app_name_first'] ?? 'JNJ' }}</span>{{ $settings['app_name_last'] ?? 'Auto' }}
                            </h5>
                            <div class="small text-secondary">Admin Panel</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>

                <div class="offcanvas-body px-3 pt-2 d-flex flex-column">
                    @include('panel.partials.sidebar-nav')

                    <div class="mt-auto pt-4">
                        <div class="promo-card rounded-4 p-4 text-white shadow-sm">
                            <h3 class="h6 fw-bold mb-2">Need today's summary?</h3>
                            <p class="small mb-3 text-white-50">
                                Generate a printable end-of-day report for bookings, walk-ins, active washers, and
                                service revenue.
                            </p>
                            <a href="{{ route('panel.api.export.report') }}"
                                class="btn btn-light fw-semibold rounded-4 px-3 w-100">
                                <i class="fa-solid fa-file-export me-2"></i>Export Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main -->
            <main class="col-12 col-lg-9 col-xl-10">
                <div class="p-3 p-md-4 p-xl-4">
                    <!-- Mobile topbar -->
                    <div class="d-flex d-lg-none align-items-center justify-content-between gap-3 mb-3 mobile-header">
                        <button class="btn btn-light border rounded-4 px-3 py-2" type="button"
                            data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                            <i class="fa-solid fa-bars"></i>
                        </button>

                        <div class="d-flex align-items-center gap-2">
                            <div
                                class="brand-icon brand-icon-sm d-inline-flex align-items-center justify-content-center rounded-4 overflow-hidden">
                                @if (!empty($settings['business_logo_url']))
                                    <img src="{{ $settings['business_logo_url'] }}" alt="Logo" class="w-100 h-100"
                                        style="object-fit: cover;" />
                                @else
                                    <i class="fa-solid fa-droplet"></i>
                                @endif
                            </div>
                            <div class="fw-bold"><span
                                    class="text-info-emphasis">{{ $settings['app_name_first'] ?? 'JNJ' }}</span>{{ $settings['app_name_last'] ?? 'Auto' }}
                            </div>
                        </div>

                        <notification-bell></notification-bell>
                    </div>

                    <!-- Page header -->
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-4 page-header-bar">
                        <div class="flex-grow-1">
                            <h2 class="display-6 fw-bold mb-1">@yield('page-title')</h2>
                            <p class="text-secondary mb-0">@yield('page-description')</p>
                        </div>

                        <div class="d-flex align-items-center gap-2 gap-md-3 flex-shrink-0">
                            @yield('page-actions')
                            <div class="d-none d-lg-block">
                                <notification-bell></notification-bell>
                            </div>
                        </div>
                    </div>

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>

</html>
