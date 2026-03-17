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
                                    <div class="brand-icon-box bg-primary text-white auth-logo">
                                        <i class="fa-solid fa-droplet"></i>
                                    </div>
                                </div>

                                <div class="fw-bold fs-3">
                                    <span class="text-primary">JNJ</span><span class="text-dark">Auto</span>
                                </div>

                                <div class="text-secondary small">
                                    Enter your email to reset your password.
                                </div>
                            </div>

                            @if (session('status'))
                                <div class="alert alert-success small" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger small">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fa-solid fa-envelope text-secondary"></i>
                                        </span>
                                        <input id="email" type="email" class="form-control border-0 bg-light @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="you@example.com">
                                    </div>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">
                                    Send Password Reset Link
                                </button>

                                <div class="text-center mt-3">
                                    <a href="{{ route('login') }}" class="small text-decoration-none fw-semibold">
                                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Login
                                    </a>
                                </div>

                                <div class="text-center text-secondary small mt-3">
                                    © {{ date('Y') }} JNJ CarWash
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
