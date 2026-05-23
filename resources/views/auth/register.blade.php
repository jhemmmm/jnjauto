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
                                    Create your account to get started.
                                </div>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger small">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Full Name</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fa-solid fa-user text-secondary"></i>
                                        </span>
                                        <input id="name" type="text" class="form-control border-0 bg-light @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="John Doe">
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fa-solid fa-envelope text-secondary"></i>
                                        </span>
                                        <input id="email" type="email" class="form-control border-0 bg-light @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@example.com">
                                    </div>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Password</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fa-solid fa-lock text-secondary"></i>
                                        </span>
                                        <input id="password" type="password" class="form-control border-0 bg-light @error('password') is-invalid @enderror" name="password" required minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}" autocomplete="new-password" placeholder="8+ chars with Aa, number, symbol" title="Use at least 8 characters with uppercase, lowercase, number, and special character.">
                                        <button class="btn bg-light border-0" type="button" id="togglePassword" aria-label="Show password">
                                            <i class="fa-solid fa-eye text-secondary" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                    <div class="text-secondary small mt-1">Use at least 8 characters with uppercase, lowercase, number, and special character.</div>
                                    @error('password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password-confirm" class="form-label fw-semibold">Confirm Password</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fa-solid fa-lock text-secondary"></i>
                                        </span>
                                        <input id="password-confirm" type="password" class="form-control border-0 bg-light" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password">
                                        <button class="btn bg-light border-0" type="button" id="togglePasswordConfirm" aria-label="Show password">
                                            <i class="fa-solid fa-eye text-secondary" id="toggleIconConfirm"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">
                                    Register
                                </button>

                                <div class="text-center mt-3">
                                    <span class="text-secondary small">Already have an account?</span>
                                    <a href="{{ route('login') }}" class="small text-decoration-none fw-semibold">Sign in</a>
                                </div>

                                <div class="text-center text-secondary small mt-3">
                                    &copy; {{ date('Y') }} {{ $settings['business_name'] ?? 'JNJ CarWash' }}
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function setupToggle(btnId, inputId, iconId) {
                const btn = document.getElementById(btnId);
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);

                if (!btn || !input || !icon) return;

                btn.addEventListener('click', () => {
                    const isPassword = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPassword ? 'text' : 'password');
                    icon.classList.toggle('fa-eye', !isPassword);
                    icon.classList.toggle('fa-eye-slash', isPassword);
                });
            }

            setupToggle('togglePassword', 'password', 'toggleIcon');
            setupToggle('togglePasswordConfirm', 'password-confirm', 'toggleIconConfirm');
        });
    </script>
@endsection
