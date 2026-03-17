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
                                    Enter your new password below.
                                </div>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger small">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf

                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fa-solid fa-envelope text-secondary"></i>
                                        </span>
                                        <input id="email" type="email" class="form-control border-0 bg-light @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="you@example.com">
                                    </div>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">New Password</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">
                                            <i class="fa-solid fa-lock text-secondary"></i>
                                        </span>
                                        <input id="password" type="password" class="form-control border-0 bg-light @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Enter new password">
                                        <button class="btn bg-light border-0" type="button" id="togglePassword" aria-label="Show password">
                                            <i class="fa-solid fa-eye text-secondary" id="toggleIcon"></i>
                                        </button>
                                    </div>
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
                                        <input id="password-confirm" type="password" class="form-control border-0 bg-light" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm new password">
                                        <button class="btn bg-light border-0" type="button" id="togglePasswordConfirm" aria-label="Show password">
                                            <i class="fa-solid fa-eye text-secondary" id="toggleIconConfirm"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">
                                    Reset Password
                                </button>

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
