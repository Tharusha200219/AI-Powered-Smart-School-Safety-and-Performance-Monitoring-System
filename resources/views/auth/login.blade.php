@extends('admin.layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('sass/app.scss') }}">
@endsection

@section('content')
    <main class="main-content @if (env('DEFAULT_THEME') == 'dark') bg-dark @endif mt-0">
        <div class="page-header align-items-start min-vh-100">
            <div class="container my-auto">
                <div id="particles-js"></div>
                <div class="row">
                    <div class="col-lg-4 col-md-8 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom card-body-dark-mode-Glassmorphism custom-class-for-Glassmorphism">
                            <div class="card-body text-center ">
                                <img class="w-75" src="{{ asset('assets/img/logo_text.png') }}" alt="">
                                <h4 class="mt-4 @if (env('DEFAULT_THEME') == 'dark') text-white @endif" >Login </h4>
                                <small class="text-color-black @if (env('DEFAULT_THEME') == 'dark') text-white @endif text-dark" >Authenticate & get access to all features</small>
                                <hr />
                                <form method="POST" role="form" class="text-start" action="{{ route('login') }}">
                                    @csrf
                                    <div class="input-group input-group-outline my-3">
                                        <label class="form-label @if (env('DEFAULT_THEME') == 'dark') text-white @endif">{{ __('Email Address') }}</label>
                                        <input type="email" class="@if (env('DEFAULT_THEME') == 'dark') text-white @endif form-control @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email') }}" required autocomplete="email"
                                            autofocus>
                                        @include('admin.layouts.form-error', ['input' => 'email'])
                                    </div>
                                    <div class="input-group input-group-outline mb-4">
                                        <label class="form-label @if (env('DEFAULT_THEME') == 'dark') text-white @endif">{{ __('Password') }}</label>
                                        <input type="password" class="@if (env('DEFAULT_THEME') == 'dark') text-white @endif form-control @error('password') is-invalid @enderror"
                                            name="password" required autocomplete="current-password">
                                    </div>
                                    <div class="form-check form-switch d-flex align-items-center my-3">
                                        <input class="form-check-input te" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label mb-0 ms-3 @if (env('DEFAULT_THEME') == 'dark') text-white @endif"
                                            for="rememberMe">{{ __('Remember Me') }}</label>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit"
                                            class="btn bg-gradient-dark w-100 my-4">{{ __('Login') }}</button>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <p class="mt-1 text-sm text-center">
                                            <a href="{{ route('password.request') }}"
                                                class="@if (env('DEFAULT_THEME') == 'dark') text-white @endif"><small>{{ __('auth.reset_password') }}</small></a>
                                        </p>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('admin.layouts.footer')
        </div>
    </main>
@endsection

@section('script')
    <script src="{{ asset('assets/js/custom.js') }}" defer data-deferred="1"></script>
@endsection
