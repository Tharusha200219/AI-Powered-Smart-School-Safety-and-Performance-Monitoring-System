@extends('admin.layouts.app')

@section('content')
    @include('admin.layouts.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @include('admin.layouts.navbar')

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <h6 class="mb-0">{{ isset($event) ? 'Edit Event' : 'Create New Event' }}</h6>
                                </div>
                                <div class="col-6 text-end">
                                    <a class="btn btn-outline-dark mb-0 d-flex align-items-center justify-content-center btn-back-auto"
                                        href="{{ route('admin.management.events.index') }}">
                                        <i class="material-symbols-rounded me-1 icon-size-md">arrow_back</i>Back
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form
                                action="{{ isset($event) ? route('admin.management.events.update', $event->id) : route('admin.management.events.store') }}"
                                method="POST">
                                @csrf
                                @if(isset($event))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <x-input name="name" title="Event Name" :isRequired="true" attr="maxlength='255'"
                                            :value="old('name', $event->name ?? '')" />
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="input-group input-group-static mb-4">
                                                    <label>Event Date</label>
                                                    <input type="date" name="event_date" class="form-control"
                                                        value="{{ old('event_date', isset($event) ? $event->event_date->format('Y-m-d') : '') }}"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-static mb-4">
                                                    <label>Start Time</label>
                                                    <input type="time" name="start_time" class="form-control"
                                                        value="{{ old('start_time', isset($event) && $event->start_time ? $event->start_time->format('H:i') : '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-static mb-4">
                                                    <label>End Time</label>
                                                    <input type="time" name="end_time" class="form-control"
                                                        value="{{ old('end_time', isset($event) && $event->end_time ? $event->end_time->format('H:i') : '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <x-input name="location" title="Location" attr="maxlength='255'"
                                            :value="old('location', $event->location ?? '')" />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <x-input name="description" type="textarea" title="Description"
                                            :value="old('description', $event->description ?? '')" />
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 text-end">
                                        <a href="{{ route('admin.management.events.index') }}"
                                            class="btn btn-outline-secondary">Cancel</a>
                                        <button type="submit"
                                            class="btn btn-primary">{{ isset($event) ? 'Update Event' : 'Create Event' }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection