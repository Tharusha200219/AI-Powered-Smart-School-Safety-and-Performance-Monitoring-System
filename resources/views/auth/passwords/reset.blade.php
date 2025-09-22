@extends('admin.layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
@endsection

@section('content')
    <main class="main-content @if (env('DEFAULT_THEME') == 'dark') bg-dark @endif mt-0">
        <div class="page-header align-items-start min-vh-100">
            <div class="container my-auto">
                <div id="particles-js"></div>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card card-body custom-class-for-Glassmorphism">
                            <img class="w-20 align-self-sm-center" src="{{ asset('assets/img/favicon.ico') }}"
                                alt="">
                            <h3 class="text-center text-dark @if (env('DEFAULT_THEME') == 'dark') text-white @endif">{{ __('passwords.reset-password-title') }}</h3>
                            <small class="text-center">{{ __('passwords.reset-password-second-title') }}</small>
                            <div class="card-body ">
                                <form method="POST" action="{{ route('password.update') }}">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <div
                                        class="input-group input-group-outline my-3">
                                        <label for="email"
                                            class="form-label text-dark @if (env('DEFAULT_THEME') == 'dark') text-white @endif">{{ __('Email Address') }}</label>

                                       
                                            <input id="email" readonly type="email"
                                                class="text-dark @if (env('DEFAULT_THEME') == 'dark') text-white @endif form-control @error('email') is-invalid @enderror" name="email"
                                                value="{{ $email ?? old('email') }}" required autocomplete="email"
                                                autofocus>

                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        
                                    </div>

                                    <div
                                        class="input-group input-group-outline my-3 ">
                                        <label for="password"
                                            class="form-label text-dark @if (env('DEFAULT_THEME') == 'dark') text-white @endif">{{ __(key: 'Password') }}</label>

                                        
                                            <input id="password" type="password"
                                                class="text-dark @if (env('DEFAULT_THEME') == 'dark') text-white @endif form-control @error('password') is-invalid @enderror" name="password"
                                                required autocomplete="new-password">

                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                       
                                    </div>

                                    <div
                                        class="input-group input-group-outline my-3">
                                        <label for="password-confirm"
                                            class="form-label text-dark @if (env('DEFAULT_THEME') == 'dark') text-white @endif">{{ __('Confirm Password') }}</label>

                                        
                                            <input id="password-confirm" type="password" class="text-dark @if (env('DEFAULT_THEME') == 'dark') text-white @endif form-control"
                                                name="password_confirmation" required autocomplete="new-password">
                                        
                                    </div>

                                    <div class="row mb-0">
                                        <div class="d-flex justify-content-center w-100">
                                            <button type="submit" class="btn bg-gradient-dark w-60 my-4 mb-2">
                                                {{ __('Reset Password') }}
                                            </button>
                                        </div>
                                    </div>
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
