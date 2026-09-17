@extends('layouts.loginlayout')

@section('content')
<main>
    <div class="container">
        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                        <div class="d-flex justify-content-center py-4">
                            <a href="index.html" class="logo d-flex align-items-center w-auto">
                                <!-- <img src="{{ asset('assets/img/cdo-seal.png') }}" alt=""> -->
                                <span class="d-none d-lg-block">GAD Database</span>
                            </a>
                        </div><!-- End Logo -->

                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="pt-4 pb-2 text-center">
                                    <img src="{{ asset('assets/img/GAD_logo_hd.png') }}" alt="Logo" style="height: 200px;">
                                </div>

                                @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                                @endif

                                <form class="row g-3 needs-validation" method="POST" action="{{ route('login') }}" novalidate>
                                    @csrf
                                    <div class="col-12">
                                        <label for="yourUsername" class="form-label">{{ __('User ID') }}</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text" id="inputGroupPrepend">@</span>
                                            <input type="text" id="user_id" name="user_id" class="form-control @error('user_id') is-invalid @enderror" value="{{ old('user_id') }}" autocomplete="user_id" autofocus required>
                                            @error('user_id')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="yourPassword" class="form-label">{{ __('Password') }}</label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" autocomplete="current-password" required>
                                        @error('password')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-2">
                                        <button class="btn btn-primary w-100" type="submit">Login</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <div class="credits">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12" style="text-align: center;">
                                        <img src="{{ asset('assets/img/cdo-seal.png') }}" alt="" style="height: 50px; width: 50px;" />
                                        <!-- <img src="{{ asset('assets/img/headergoldencdologo.png') }}" alt="" style="height: 50px; width: auto;" /> -->
                                        <img src="{{ asset('assets/img/rise.png') }}" alt="" style="height: 50px; width: auto;" />
                                        <img src="{{ asset('assets/img/ict-logo.png') }}" alt="" style="height: 50px; width: 50px;" />
                                    </div>
                                    <div class="col-12" style="text-align: center;">
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
</main><!-- End #main -->
@endsection