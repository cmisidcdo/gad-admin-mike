@extends('layouts.loginlayout')

@section('content')
<main>
    <div class="container">
        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-7 d-flex flex-column align-items-center justify-content-center">

                        {{-- Logo / Brand --}}
                        <div class="d-flex justify-content-center py-4">
                            <a href="#" class="logo d-flex align-items-center gap-2 text-decoration-none">
                                <img src="{{ asset('assets/img/GAD_logo_hd.png') }}" alt="GAD Logo" style="height: 60px;">
                                <span class="fw-bold fs-5 text-dark d-none d-md-block">GAD Database</span>
                            </a>
                        </div>

                        {{-- Signup Success Banner --}}
                        @if(session('signup_success'))
                        <div class="alert alert-success alert-dismissible fade show w-100 shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('signup_success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <div class="card w-100 mb-3 shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
                            {{-- Tab Navigation --}}
                            <div class="card-header p-0 border-0 bg-white">
                                <ul class="nav nav-tabs nav-fill border-0" id="authTabs" role="tablist" style="border-bottom: 2px solid #e9ecef;">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ !session('open_signup') ? 'active' : '' }} fw-semibold py-3 border-0 rounded-0"
                                                id="login-tab" data-bs-toggle="tab" data-bs-target="#login-panel"
                                                type="button" role="tab" aria-controls="login-panel"
                                                aria-selected="{{ !session('open_signup') ? 'true' : 'false' }}"
                                                style="color: #012970; font-size: 0.95rem;">
                                            <i class="bi bi-person-fill me-1"></i> Login
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ session('open_signup') ? 'active' : '' }} fw-semibold py-3 border-0 rounded-0"
                                                id="signup-tab" data-bs-toggle="tab" data-bs-target="#signup-panel"
                                                type="button" role="tab" aria-controls="signup-panel"
                                                aria-selected="{{ session('open_signup') ? 'true' : 'false' }}"
                                                style="color: #012970; font-size: 0.95rem;">
                                            <i class="bi bi-person-plus-fill me-1"></i> Sign Up
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body px-4 pt-4 pb-3 tab-content" id="authTabContent">

                                {{-- ─── LOGIN PANEL ─── --}}
                                <div class="tab-pane fade {{ !session('open_signup') ? 'show active' : '' }}" id="login-panel" role="tabpanel" aria-labelledby="login-tab">
                                    <div class="text-center mb-4">
                                        <h5 class="fw-bold mb-1" style="color: #012970;">Welcome back!</h5>
                                        <p class="text-muted small mb-0">Sign in to your GAD account</p>
                                    </div>

                                    @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                                        <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    @endif

                                    <form class="row g-3" method="POST" action="{{ route('login') }}" id="loginForm">
                                        @csrf
                                        <div class="col-12">
                                            <label for="login_user_id" class="form-label fw-semibold small text-secondary">User ID</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                                <input type="text"
                                                       id="login_user_id"
                                                       name="user_id"
                                                       class="form-control border-start-0 ps-0 @error('user_id') is-invalid @enderror"
                                                       value="{{ old('user_id') }}"
                                                       autocomplete="username"
                                                       placeholder="Enter your User ID"
                                                       autofocus required>
                                                @error('user_id')
                                                <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="login_password" class="form-label fw-semibold small text-secondary">Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                                <input type="password"
                                                       id="login_password"
                                                       name="password"
                                                       class="form-control border-start-0 ps-0 border-end-0 @error('password') is-invalid @enderror"
                                                       placeholder="Enter your password"
                                                       autocomplete="current-password"
                                                       required>
                                                <button class="btn btn-outline-secondary border border-start-0" type="button" id="toggleLoginPassword" tabindex="-1"
                                                        onclick="toggleVisibility('login_password', 'loginPassIcon')">
                                                    <i class="bi bi-eye" id="loginPassIcon"></i>
                                                </button>
                                                @error('password')
                                                <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 mt-3">
                                            <button class="btn btn-primary w-100 py-2 fw-semibold" type="submit" id="loginBtn">
                                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                                            </button>
                                        </div>

                                        <div class="col-12 text-center">
                                            <small class="text-muted">Don't have an account?
                                                <a href="#" class="text-primary text-decoration-none fw-semibold"
                                                   onclick="document.getElementById('signup-tab').click(); return false;">
                                                    Sign up here
                                                </a>
                                            </small>
                                        </div>
                                    </form>
                                </div>

                                {{-- ─── SIGNUP PANEL ─── --}}
                                <div class="tab-pane fade {{ session('open_signup') ? 'show active' : '' }}" id="signup-panel" role="tabpanel" aria-labelledby="signup-tab">
                                    <div class="text-center mb-4">
                                        <h5 class="fw-bold mb-1" style="color: #012970;">Create an Account</h5>
                                        <p class="text-muted small mb-0">New accounts are pending admin activation after signup.</p>
                                    </div>

                                    @if($errors->any() && session('open_signup'))
                                    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        Please correct the errors below.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    @endif

                                    <form method="POST" action="{{ route('signup.store') }}" id="signupForm" novalidate>
                                        @csrf

                                        {{-- User ID --}}
                                        <div class="mb-3">
                                            <label for="reg_user_id" class="form-label fw-semibold small text-secondary">User ID <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-at text-muted"></i></span>
                                                <input type="text"
                                                       id="reg_user_id"
                                                       name="user_id"
                                                       class="form-control border-start-0 ps-0 @error('user_id') is-invalid @enderror"
                                                       value="{{ old('user_id') }}"
                                                       placeholder="Choose a unique User ID"
                                                       required>
                                                @error('user_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Full Name --}}
                                        <div class="mb-3">
                                            <label for="reg_name" class="form-label fw-semibold small text-secondary">Full Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                                <input type="text"
                                                       id="reg_name"
                                                       name="name"
                                                       class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror"
                                                       value="{{ old('name') }}"
                                                       placeholder="Your full name"
                                                       required>
                                                @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Email --}}
                                        <div class="mb-3">
                                            <label for="reg_email" class="form-label fw-semibold small text-secondary">Email Address <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                                <input type="email"
                                                       id="reg_email"
                                                       name="email"
                                                       class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                                                       value="{{ old('email') }}"
                                                       placeholder="your@email.com"
                                                       required>
                                                @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Office / Department --}}
                                        <div class="mb-3">
                                            <label for="reg_office" class="form-label fw-semibold small text-secondary">Office / Department <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-building text-muted"></i></span>
                                                <select id="reg_office"
                                                        name="office"
                                                        class="form-select border-start-0 @error('office') is-invalid @enderror"
                                                        required>
                                                    <option value="">-- Select your office --</option>
                                                    @foreach(\App\Models\TblRefDepartment::orderBy('department_name')->get() as $dept)
                                                    <option value="{{ $dept->department_id }}"
                                                            {{ old('office') == $dept->department_id ? 'selected' : '' }}>
                                                        {{ $dept->department_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('office')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Password --}}
                                        <div class="mb-3">
                                            <label for="reg_password" class="form-label fw-semibold small text-secondary">Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                                <input type="password"
                                                       id="reg_password"
                                                       name="password"
                                                       class="form-control border-start-0 ps-0 border-end-0 @error('password') is-invalid @enderror"
                                                       placeholder="Min. 8 characters"
                                                       autocomplete="new-password"
                                                       required>
                                                <button class="btn btn-outline-secondary border border-start-0" type="button" tabindex="-1"
                                                        onclick="toggleVisibility('reg_password', 'regPassIcon1')">
                                                    <i class="bi bi-eye" id="regPassIcon1"></i>
                                                </button>
                                                @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Confirm Password --}}
                                        <div class="mb-3">
                                            <label for="reg_password_confirmation" class="form-label fw-semibold small text-secondary">Confirm Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                                                <input type="password"
                                                       id="reg_password_confirmation"
                                                       name="password_confirmation"
                                                       class="form-control border-start-0 ps-0 border-end-0"
                                                       placeholder="Re-enter your password"
                                                       autocomplete="new-password"
                                                       required>
                                                <button class="btn btn-outline-secondary border border-start-0" type="button" tabindex="-1"
                                                        onclick="toggleVisibility('reg_password_confirmation', 'regPassIcon2')">
                                                    <i class="bi bi-eye" id="regPassIcon2"></i>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Math CAPTCHA --}}
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold small text-secondary">
                                                Security Check <span class="text-danger">*</span>
                                            </label>
                                            <div class="p-3 rounded-3 border d-flex align-items-center gap-3" style="background: #f8f9fa;">
                                                <div class="flex-shrink-0">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="p-2 rounded-2 fw-bold text-center" id="captchaBox"
                                                             style="background: linear-gradient(135deg, #012970, #0d6efd); color: #fff; font-size: 1.1rem; min-width: 110px; letter-spacing: 2px; user-select: none; font-family: 'Courier New', monospace;">
                                                            {{ $captchaQuestion }}
                                                        </div>
                                                        <button type="button" id="refreshCaptchaBtn"
                                                                class="btn btn-sm btn-outline-secondary py-1 px-2"
                                                                title="Get a new question"
                                                                onclick="refreshCaptcha()">
                                                            <i class="bi bi-arrow-clockwise"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="text-muted small fw-medium">=</span>
                                                        <input type="number"
                                                               id="captcha_answer"
                                                               name="captcha_answer"
                                                               class="form-control form-control-sm @error('captcha_answer') is-invalid @enderror"
                                                               placeholder="Your answer"
                                                               autocomplete="off"
                                                               required>
                                                    </div>
                                                    @error('captcha_answer')
                                                    <div class="text-danger small mt-1"><i class="bi bi-x-circle me-1"></i>{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <small class="text-muted">Solve the math problem to verify you're human.</small>
                                        </div>

                                        <div class="d-grid mb-3">
                                            <button class="btn btn-success py-2 fw-semibold" type="submit" id="signupBtn">
                                                <i class="bi bi-person-check-fill me-1"></i> Create Account
                                            </button>
                                        </div>

                                        <div class="text-center">
                                            <small class="text-muted">
                                                Already have an account?
                                                <a href="#" class="text-primary text-decoration-none fw-semibold"
                                                   onclick="document.getElementById('login-tab').click(); return false;">
                                                    Login here
                                                </a>
                                            </small>
                                        </div>
                                    </form>
                                </div>

                            </div>{{-- end tab-content --}}
                        </div>

                        {{-- Footer logos --}}
                        <div class="credits">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <img src="{{ asset('assets/img/cdo-seal.png') }}" alt="CDO" style="height: 48px; width: 48px;" />
                                        <img src="{{ asset('assets/img/rise.png') }}" alt="RISE" style="height: 48px; width: auto;" />
                                        <img src="{{ asset('assets/img/ict-logo.png') }}" alt="ICT" style="height: 48px; width: 48px;" />
                                    </div>
                                    <div class="col-12 text-center mt-1">
                                        <span style="font-size: 10px;">Powered by: <span>City Management Information Systems and Innovation Department</span>.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<style>
.nav-tabs .nav-link.active {
    border-bottom: 3px solid #012970 !important;
    color: #012970 !important;
    background: transparent;
}
.nav-tabs .nav-link:hover {
    color: #012970;
    border-bottom: 3px solid #a0b4d0;
}
.nav-tabs .nav-link {
    transition: all 0.2s ease;
    border: none;
    border-bottom: 3px solid transparent;
}
.input-group-text {
    background-color: #f8f9fa;
}
#captchaBox {
    text-rendering: geometricPrecision;
    box-shadow: 0 2px 8px rgba(1,41,112,0.15);
}
</style>

<script>
function toggleVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

function refreshCaptcha() {
    const btn = document.getElementById('refreshCaptchaBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

    fetch('{{ route('captcha.refresh') }}')
        .then(r => r.json())
        .then(data => {
            document.getElementById('captchaBox').textContent = data.question;
            document.getElementById('captcha_answer').value = '';
        })
        .catch(() => {})
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
        });
}

// Auto-open signup tab if server says so (e.g. after validation failure on signup)
document.addEventListener('DOMContentLoaded', function () {
    @if(session('open_signup') || $errors->has('user_id') || $errors->has('name') || $errors->has('email') || $errors->has('office') || $errors->has('password') || $errors->has('captcha_answer'))
    const signupTab = document.getElementById('signup-tab');
    if (signupTab) {
        const tab = new bootstrap.Tab(signupTab);
        tab.show();
    }
    @endif
});
</script>
@endsection