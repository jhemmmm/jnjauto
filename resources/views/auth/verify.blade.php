@extends('layouts.app')

@section('content')
    <section class="auth-wrap py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-5 col-xl-4">
                    <div class="card auth-card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-4">
                                <div class="d-flex justify-content-center mb-2">
                                    <div class="brand-icon-box bg-primary text-white auth-logo overflow-hidden">
                                        @if(!empty($settings['business_logo_url']))
                                            <img src="{{ $settings['business_logo_url'] }}" alt="Logo" class="w-100 h-100" style="object-fit: cover;" />
                                        @else
                                            <i class="fa-solid fa-droplet"></i>
                                        @endif
                                    </div>
                                </div>

                                <div class="fw-bold fs-3">
                                    <span class="text-primary">{{ $settings['app_name_first'] ?? 'JNJ' }}</span><span class="text-dark">{{ $settings['app_name_last'] ?? 'Auto' }}</span>
                                </div>

                                <div class="text-secondary small">
                                    Verify your email address.
                                </div>
                            </div>

                            @if (session('resent'))
                                <div class="alert alert-success small" role="alert">
                                    A fresh verification link has been sent to your email address.
                                </div>
                            @endif

                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="fa-solid fa-envelope-circle-check text-primary fa-3x"></i>
                                </div>
                                <p class="text-secondary mb-0">
                                    Before proceeding, please check your email for a verification link.
                                    If you did not receive the email, click the button below.
                                </p>
                            </div>

                            <form method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">
                                    Resend Verification Email
                                </button>
                            </form>

                            <div class="text-center text-secondary small mt-3">
                                &copy; {{ date('Y') }} {{ $settings['business_name'] ?? 'JNJ CarWash' }}
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
