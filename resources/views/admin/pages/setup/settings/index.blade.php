@extends('admin.layouts.app')

@section('content')
    @include('admin.layouts.sidebar')

    <main class="main-content position-relative max-height-vh-100">

        @include('admin.layouts.navbar')

        <div class="container-fluid pt-2">
            <form method="POST" role="form" class="text-start" action="{{ route('admin.setup.settings.update') }}">
                @csrf
                <div class="row">
                    <div class="ms-3">
                        @php
                            $breadcrumbs = getBreadcrumbs();
                            $breadcrumb = $breadcrumbs[count($breadcrumbs) - 2];
                        @endphp
                        <h3 class="mb-0 h4 font-weight-bolder">{{ ucfirst($breadcrumb) }}</h3>
                    </div>
                    <div class="col-4">
                        <div class="card my-4">
                            <div class="card-body p-3">
                                <h6 class="mt-4 mb-3">Basic Settings</h6>
                                <div class="row">
                                    <div class="col-md-12">
                                        <x-input name="title" type="text" title="{{ __('settings.company_name') }}"
                                            value="{{ $setting->title }}" isRequired=true
                                            attr="maxlength=255 autocomplete=organization" />
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card my-4">
                            <div class="card-body p-3">
                                <h6 class="mt-4 mb-3">Company Details</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-input name="company_name" type="email"
                                            title="{{ __('settings.company_email') }}" value="{{ $setting->company_email }}"
                                            isRequired=true attr="maxlength=255" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-input name="company_phone" type="number"
                                            title="{{ __('settings.company_phone') }}" value="{{ $setting->company_phone }}"
                                            attr="maxlength=255 autocomplete=organization" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-input name="company_address" type="textarea"
                                            title="{{ __('settings.company_address') }}"
                                            value="{{ $setting->company_address }}" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-input name="mail_signature" type="textarea"
                                            title="{{ __('settings.mail_signature') }}"
                                            value="{{ $setting->mail_signature }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-end">
                        <button class="btn btn-danger">Clear</button>
                        <button class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection
