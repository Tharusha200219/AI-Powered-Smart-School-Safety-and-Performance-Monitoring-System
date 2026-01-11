@extends('admin.layouts.app')

@section('content')
    @include('admin.layouts.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @include('admin.layouts.navbar')

        <div class="container-fluid pt-2">
            <div class="row">
                <div class="col-12">
                    @include('admin.layouts.flash')

                    <div class="card my-4">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <a href="{{ route('admin.seating-arrangement.index') }}"
                                        class="btn btn-sm btn-secondary me-3 mb-0">
                                        <i class="material-symbols-rounded" style="font-size: 18px;">arrow_back</i>
                                    </a>
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="material-symbols-rounded me-2">psychology</i>
                                        Generate Seating Arrangement
                                    </h6>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('admin.seating-arrangement.generate') }}" method="POST"
                                id="seatingForm">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <x-input name="grade_level" type="select" title="Grade Level" :isRequired="true"
                                            placeholder="Select Grade Level" :options="[
                                                1 => 'Grade 1',
                                                2 => 'Grade 2',
                                                3 => 'Grade 3',
                                                4 => 'Grade 4',
                                                5 => 'Grade 5',
                                                6 => 'Grade 6',
                                                7 => 'Grade 7',
                                                8 => 'Grade 8',
                                                9 => 'Grade 9',
                                                10 => 'Grade 10',
                                                11 => 'Grade 11',
                                                12 => 'Grade 12',
                                                13 => 'Grade 13',
                                            ]" />
                                    </div>

                                    <div class="col-md-6">
                                        <x-input name="section" type="select" title="Section" :isRequired="true"
                                            placeholder="Select Section" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <x-input name="academic_year" title="Academic Year" :isRequired="true"
                                            value="{{ date('Y') }}-{{ date('Y') + 1 }}">
                                        </x-input>
                                    </div>

                                    <div class="col-md-6">
                                        <x-input name="term" type="select" title="Term" :isRequired="true"
                                            placeholder="Select Term" :options="[
                                                1 => 'Term 1',
                                                2 => 'Term 2',
                                                3 => 'Term 3',
                                            ]" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <x-input name="seats_per_row" type="number" title="Seats Per Row" :isRequired="true"
                                            attr="min='1' max='10'" value="5">
                                        </x-input>
                                        <small class="text-muted">Number of seats in each row (1-10)</small>
                                    </div>

                                    <div class="col-md-6">
                                        <x-input name="total_rows" type="number" title="Total Rows" :isRequired="true"
                                            attr="min='1' max='20'" value="6">
                                        </x-input>
                                        <small class="text-muted">Number of rows in classroom (1-20)</small>
                                    </div>
                                </div>

                                {{-- <div class="alert alert-info mt-4">
                                    <i class="material-symbols-rounded me-2">info</i>
                                    <strong>AI-Powered Seating:</strong> The system will analyze student performance data
                                    and generate an optimal seating arrangement that:
                                    <ul class="mb-0 mt-2">
                                        <li>Balances strong and weak students for peer learning</li>
                                        <li>Considers behavioral patterns and attendance</li>
                                        <li>Optimizes teacher visibility and classroom management</li>
                                    </ul>
                                </div> --}}

                                <div class="text-end mt-4">
                                    <a href="{{ route('admin.seating-arrangement.index') }}"
                                        class="btn btn-light">Cancel</a>
                                    <button type="submit" class="btn btn-primary" id="generateBtn">
                                        <i class="material-symbols-rounded me-1" style="font-size: 18px;">psychology</i>
                                        Generate Arrangement
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for components to render
            setTimeout(function() {
                const gradeLevelSelect = document.querySelector('select[name="grade_level"]');
                const sectionSelect = document.querySelector('select[name="section"]');

                if (gradeLevelSelect && sectionSelect) {
                    gradeLevelSelect.addEventListener('change', function() {
                        const grade = this.value;

                        if (!grade) {
                            sectionSelect.innerHTML = '<option value="">Select Section</option>';
                            return;
                        }

                        // Fetch sections for selected grade
                        fetch(`/admin/seating-arrangement/get-sections?grade=${grade}`)
                            .then(response => response.json())
                            .then(data => {
                                sectionSelect.innerHTML =
                                    '<option value="">Select Section</option>';
                                data.forEach(section => {
                                    sectionSelect.innerHTML +=
                                        `<option value="${section}">${section}</option>`;
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                sectionSelect.innerHTML =
                                    '<option value="">Error loading sections</option>';
                            });
                    });
                } else {
                    console.log('Select elements not found:', {
                        gradeLevelSelect,
                        sectionSelect
                    });
                }
            }, 200);
        });

        document.getElementById('seatingForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('generateBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Generating...';
        });
    </script>
@endpush
