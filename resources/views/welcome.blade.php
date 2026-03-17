@extends('layouts.app')

@section('content')
    {{-- Main Section --}}
    <section class="hero-section position-relative pt-0 pt-md-5">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-12 col-lg-6 position-relative" style="z-index: 2;">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill hero-badge mb-4">
                        <span class="badge bg-info-subtle text-info fw-bold">New</span>
                        <span class="small text-secondary fw-semibold">Trusted by 2,000+ Local Drivers</span>
                    </div>

                    <h1 class="display-4 fw-bold hero-title mb-3">
                        Premium Wash for<br>
                        <span class="text-primary">Fresh-Looking Cars.</span>
                    </h1>

                    <p class="lead text-secondary mb-4" style="max-width: 520px;">
                        Skip long queues. We provide fast, professional car wash & detailing with transparent pricing
                        and a satisfaction guarantee on every service.
                    </p>

                    <a href="{{ route('appointment.index') }}" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold">
                        Book Now
                    </a>

                    <!-- stats -->
                    <div class="row mt-4 g-3">
                        <div class="col-6 col-md-4">
                            <div class="d-flex align-items-center gap-3 stat-box">
                                <div>
                                    <div class="fs-3 fw-bold">4.9</div>
                                    <div class="small text-secondary">
                                        <span class="text-warning">
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                        </span>
                                        <div class="fw-semibold">Customer Rating</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-4">
                            <div class="stat-box">
                                <div class="fs-3 fw-bold">10+</div>
                                <div class="small text-secondary fw-semibold">Years Experience</div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="stat-box">
                                <div class="fs-3 fw-bold">Same-day</div>
                                <div class="small text-secondary fw-semibold">Booking Available</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 d-none d-md-block">
                    <div class="hero-image-card">
                        <img src="{{ asset('images/hero-carwash.jpg') }}" alt="Car Wash" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Brands Section --}}
    <section class="bg-white py-4">
        <div class="row justify-content-center text-center">
            <div class="col-12">
                <div class="text-uppercase">
                    <h6 class="letter-spacing-2 text-secondary fw-semibold pb-4">We Wash All Major Brands</h6>
                </div>
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-5 ">
                    <img src="{{ asset('images/toyota.png') }}" class="img-fluid" style="height: 35px;" alt="Toyota" data-aos="fade-up" data-aos-delay="100">
                    <img src="{{ asset('images/bmw.png') }}" class="img-fluid" style="height: 35px;" alt="BMW" data-aos="fade-up" data-aos-delay="200">
                    <img src="{{ asset('images/audi.png') }}" class="img-fluid" style="height: 35px;" alt="Audi" data-aos="fade-up" data-aos-delay="300">
                    <img src="{{ asset('images/mercedes.png') }}" class="img-fluid" style="height: 35px;" alt="Mercedes" data-aos="fade-up" data-aos-delay="400">
                    <img src="{{ asset('images/ford.png') }}" class="img-fluid" style="height: 40px;" alt="Ford" data-aos="fade-up" data-aos-delay="500">
                </div>
            </div>
        </div>
    </section>

    {{--  Service Menu Section --}}
    <section id="services" class="bg-light py-5">
        <div class="container">

            <!-- Section Header -->
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-6">
                    <h6 class="text-uppercase text-primary fw-bold">Premium Care</h6>
                    <h2 class="display-5 fw-bold">Our Service Menu</h2>
                    <p class="text-secondary">
                        Professional car wash and detailing services tailored for every vehicle.
                    </p>
                </div>
            </div>

            {{-- Service Cards --}}
            @forelse($services as $index => $service)
                @if($loop->first)
                    <div class="row g-4">
                @endif

                <div class="col-12 col-md-6 {{ service_col_class($services->count()) }}">
                    <div class="card hover-card h-100 text-center p-4 @if($index === 1) border-primary position-relative @endif">

                        @if($index === 1)
                            <span class="badge bg-primary position-absolute top-0 start-50 translate-middle px-3 py-2 rounded-pill">
                                Most Popular
                            </span>
                        @endif

                        <div class="service-icon mb-3 @if($index === 1) mt-3 @endif">
                            <i class="{{ service_icon($index) }} fs-1 text-primary"></i>
                        </div>

                        <h5 class="fw-bold">{{ $service->name }}</h5>
                        <div class="mb-3">
                            <span class="text-secondary small">starts at</span>
                            <h3 class="fw-bold text-primary mb-0 d-inline">{{ format_price($service->price) }}</h3>
                        </div>

                        @if($service->description)
                            <p class="text-secondary small mb-4">{{ $service->description }}</p>
                        @endif

                        <a href="{{ route('appointment.index') }}"
                            class="btn {{ $index === 1 ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill fw-bold mt-auto">
                            Book Now
                        </a>
                    </div>
                </div>

                @if($loop->last)
                    </div>
                @endif
            @empty
                <div class="text-center py-5">
                    <i class="fa-solid fa-car-side fa-3x text-secondary mb-3"></i>
                    <p class="text-secondary fw-semibold">No services available yet. Check back soon!</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- About Section --}}
    <section id="about" class="py-5 bg-white">
        <div class="container">
            <div class="row align-items-center g-5">
                <!-- LEFT IMAGE -->
                <div class="col-12 col-lg-6">
                    <div class="about-image-wrapper">
                        <img src="{{ asset('images/about-carwash.jpg') }}" alt="JNJ CarWash Team" class="img-fluid rounded-4 shadow">
                    </div>
                </div>

                <!-- RIGHT CONTENT -->
                <div class="col-12 col-lg-6">
                    <h6 class="text-uppercase text-primary fw-bold mb-2">About {{ setting('business_name', 'JNJ Auto Car Wash') }}</h6>

                    <h2 class="display-4 fw-bold mb-3">
                        Professional Car Care <br>
                        You Can Trust.
                    </h2>

                    <p class="text-secondary mb-4">
                        At {{ setting('business_name', 'JNJ Auto Car Wash') }}, we believe your car deserves more than just a rinse.
                        Our trained professionals provide high-quality washing and detailing
                        services using premium products and modern equipment.
                    </p>

                    <!-- Features -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fa-solid fa-circle-check text-primary fs-5"></i>
                                <span class="fw-semibold">Premium Products</span>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fa-solid fa-clock text-primary fs-5"></i>
                                <span class="fw-semibold">Fast Service</span>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fa-solid fa-shield-halved text-primary fs-5"></i>
                                <span class="fw-semibold">Safe Process</span>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fa-solid fa-star text-primary fs-5"></i>
                                <span class="fw-semibold">Top Rated</span>
                            </div>
                        </div>
                    </div>

                    <!-- Small Stats -->
                    <div class="row g-4 mb-4">
                        <div class="col-4">
                            <h4 class="fw-bold text-primary">10+</h4>
                            <small class="text-secondary">Years Experience</small>
                        </div>
                        <div class="col-4">
                            <h4 class="fw-bold text-primary">2,000+</h4>
                            <small class="text-secondary">Happy Customers</small>
                        </div>
                        <div class="col-4">
                            <h4 class="fw-bold text-primary">4.9★</h4>
                            <small class="text-secondary">Customer Rating</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Testimonials Section --}}
    <section id="testimonial" class="py-5 bg-light">
        <div class="container">

            <!-- Section Header -->
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-6">
                    <h6 class="text-uppercase text-primary fw-bold">Customer Reviews</h6>
                    <h2 class="fw-bold">What Our Clients Say</h2>
                    <p class="text-secondary">
                        Trusted by thousands of drivers who keep coming back.
                    </p>
                </div>
            </div>

            <div class="row g-4">

                <!-- Testimonial 1 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card testimonial-card p-4 h-100">
                        <div class="mb-3 text-warning">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>

                        <p class="text-secondary">
                            “Best car wash in town! My SUV looks brand new every time.
                            Staff is professional and fast.”
                        </p>

                        <div class="d-flex align-items-center gap-3 mt-3">
                            <img src="{{ asset('images/user1.jpg') }}" class="testimonial-avatar" alt="Customer">

                            <div>
                                <h6 class="mb-0 fw-bold">John Joseph S.</h6>
                                <small class="text-secondary">Regular Customer</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card testimonial-card p-4 h-100">
                        <div class="mb-3 text-warning">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>

                        <p class="text-secondary">
                            “Very affordable and high quality service.
                            I especially love their premium detailing package.”
                        </p>

                        <div class="d-flex align-items-center gap-3 mt-3">
                            <img src="{{ asset('images/user2.jpg') }}" class="testimonial-avatar" alt="Customer">

                            <div>
                                <h6 class="mb-0 fw-bold">Mary Joy B.</h6>
                                <small class="text-secondary">Tesla Owner</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card testimonial-card p-4 h-100">
                        <div class="mb-3 text-warning">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>

                        <p class="text-secondary">
                            “Quick service and amazing results.
                            Booking online was super easy!”
                        </p>

                        <div class="d-flex align-items-center gap-3 mt-3">
                            <img src="{{ asset('images/user3.jpg') }}" class="testimonial-avatar" alt="Customer">

                            <div>
                                <h6 class="mb-0 fw-bold">Jheamuel P.</h6>
                                <small class="text-secondary">E-Bike Owner</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
