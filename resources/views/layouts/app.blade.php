<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings['business_name'] ?? config('app.name', 'Laravel') }} - Premium Car Wash Services</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <!-- Scripts -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased d-flex flex-column min-vh-100">

    {{-- ── Navigation ───────────────────────────────────────────── --}}
    <nav class="navbar navbar-expand-lg fixed-top py-3 border-bottom shadow-sm bg-white">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <div class="brand-icon-box bg-primary text-white overflow-hidden">
                    @if (!empty($settings['business_logo_url']))
                        <img src="{{ $settings['business_logo_url'] }}" alt="Logo" class="w-100 h-100"
                            style="object-fit: cover;" />
                    @else
                        <i class="fa-solid fa-droplet"></i>
                    @endif
                </div>
                <span class="fw-bold fs-3">
                    <span class="text-primary">{{ $settings['app_name_first'] ?? 'JNJ' }}</span><span
                        class="text-dark">{{ $settings['app_name_last'] ?? 'Auto' }}</span>
                </span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent">
                <i class="fa-solid fa-bars fs-2"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-4">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-secondary" href="{{ route('home') }}#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-secondary" href="{{ route('home') }}#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-secondary"
                            href="{{ route('home') }}#testimonial">Testimonials</a>
                    </li>
                </ul>

                <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3 mt-3 mt-lg-0">
                    @if (($settings['show_emergency_phone'] ?? '1') === '1')
                        <div class="text-lg-end d-none d-lg-block">
                            <span class="d-block text-muted small fw-medium">Emergency?</span>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['business_phone'] ?? '') }}"
                                class="text-decoration-none fw-bold text-dark hover-brand">
                                <i class="fa-solid fa-phone me-1"></i>
                                {{ $settings['business_phone'] ?? '(555) 123-4567' }}
                            </a>
                        </div>
                    @endif

                    <a href="{{ route('appointment.index') }}"
                        class="btn btn-primary rounded-pill px-4 py-2 fw-bold text-white">
                        Book Now
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main id="app" class="mt-5 pt-5 flex-fill">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white py-5">
        <div class="container">
            <div class="row gy-4">

                {{-- Brand --}}
                <div class="col-md-4">
                    <h5 class="fw-bold d-flex align-items-center gap-2">
                        <div class="brand-icon-box text-white overflow-hidden">
                            @if (!empty($settings['business_logo_url']))
                                <img src="{{ $settings['business_logo_url'] }}" alt="Logo" class="w-100 h-100"
                                    style="object-fit: cover;" />
                            @else
                                <i class="fa-solid fa-droplet"></i>
                            @endif
                        </div>
                        <span class="fw-bold fs-3">
                            <span class="text-primary">{{ $settings['app_name_first'] ?? 'JNJ' }}</span><span
                                class="text-dark">{{ $settings['app_name_last'] ?? 'Auto' }}</span>
                        </span>
                    </h5>
                    <p class="text-secondary">
                        {{ $settings['business_name'] ?? 'JNJ Auto Car Wash' }} -
                        Professional car wash and detailing services. Fast, reliable, and affordable.
                    </p>
                </div>

                {{-- Navigation --}}
                <div class="col-md-2">
                    <h6 class="text-primary fw-bold">Company</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}#about" class="footer-link">About</a></li>
                        <li><a href="{{ route('home') }}#services" class="footer-link">Services</a></li>
                        <li><a href="{{ route('home') }}#testimonial" class="footer-link">Testimonials</a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div class="col-md-2">
                    <h6 class="text-primary fw-bold">Contact</h6>
                    <p class="text-secondary mb-1">
                        <i class="fa-solid fa-phone me-2 text-primary"></i>
                        {{ $settings['business_phone'] ?? '(555) 123-4567' }}
                    </p>
                    <p class="text-secondary">
                        <i class="fa-solid fa-location-dot me-2 text-primary"></i>
                        {{ $settings['business_address'] ?? 'Naga City' }}
                    </p>
                </div>

                {{-- Admin --}}
                <div class="col-md-2">
                    <h6 class="text-primary fw-bold">Administrator</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('login') }}" class="footer-link">Panel</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </footer>

</body>

</html>
