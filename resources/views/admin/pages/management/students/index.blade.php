@extends('admin.layouts.app')

@section('css')
@endsection

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
                                    <h6 class="mb-0">{{ pageTitle() }}</h6>
                                </div>
                                <div class="col-6 text-end">
                                    @if (checkPermission('admin.management.students.form'))
                                        <a class="btn bg-gradient-dark mb-0"
                                            href="{{ route('admin.management.students.form') }}">
                                            <i class="material-symbols-rounded text-sm me-1">add</i>Create Student
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="custom-table-responsive">
                                {{ $dataTable->table() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@isset($dataTable)
    {{ $dataTable->scripts(attributes: ['type' => 'module', 'class' => 'table table-bordered']) }}
@endisset

<<<<<<< HEAD
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced DataTable functionality
        setTimeout(function() {
            const table = $('#student-table').DataTable();

            // Add responsive breakpoint handling
            function handleResponsive() {
                const isMobile = window.innerWidth <= 768;
                const isTablet = window.innerWidth <= 992 && window.innerWidth > 768;

                if (isMobile) {
                    // Mobile-specific adjustments
                    table.page.len(10).draw();
                    $('.dataTables_length').hide();
                    $('.dataTables_info').addClass('small text-center mt-2');
                } else if (isTablet) {
                    // Tablet-specific adjustments
                    table.page.len(15).draw();
                } else {
                    // Desktop view
                    $('.dataTables_length').show();
                    $('.dataTables_info').removeClass('small text-center mt-2');
                }
            }

            // Initial responsive setup
            handleResponsive();

            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    handleResponsive();
                    table.responsive.recalc();
                }, 250);
            });

            // Enhanced search functionality
            $('.dataTables_filter input').attr('placeholder',
                'Search students by name, code, email...');

            // Add loading state improvements
            table.on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $('.dataTables_processing').html(`
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span>Loading students...</span>
                    </div>
                `);
                }
            });

            // Add row hover effects
            $('#student-table tbody').on('mouseenter', 'tr', function() {
                $(this).addClass('table-hover-effect');
            }).on('mouseleave', 'tr', function() {
                $(this).removeClass('table-hover-effect');
            });

            // Enhance empty state
            if (table.data().count() === 0) {
                $('.dataTables_empty').html(`
                <div class="text-center py-5">
                    <i class="material-symbols-rounded text-muted" style="font-size: 3rem;">school</i>
                    <h6 class="text-muted mt-3">No Students Found</h6>
                    <p class="text-muted small">Start by creating your first student record.</p>
                    <a href="{{ route('admin.management.students.form') }}" class="btn btn-primary btn-sm mt-2">
                        <i class="material-symbols-rounded me-1">add</i>Add Student
                    </a>
                </div>
            `);
            }

        }, 100);
    });

    // Additional responsive utilities
    function adjustTableForMobile() {
        const table = $('#student-table');
        if (window.innerWidth <= 576) {
            table.addClass('table-sm');
            // Hide less important columns on very small screens
            table.find('th:nth-child(6), td:nth-child(6)').hide(); // Email column
            table.find('th:nth-child(8), td:nth-child(8)').hide(); // Status column
        } else {
            table.removeClass('table-sm');
            table.find('th, td').show();
        }
    }

    // Run on load and resize
    window.addEventListener('load', adjustTableForMobile);
    window.addEventListener('resize', debounce(adjustTableForMobile, 250));

    // Debounce utility function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
</script>
=======
@vite(['resources/css/admin/tables.css', 'resources/js/admin/student-table.js'])
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606

{{-- Include common scripts if they exist --}}
@if (file_exists(public_path('build/js/common/show.js')))
    @vite(['resources/js/common/show.js'])
@endif

@if (file_exists(public_path('build/js/common/confirm.js')))
    @vite(['resources/js/common/confirm.js'])
@endif

@if (file_exists(public_path('build/js/common/delete.js')))
    @vite(['resources/js/common/delete.js'])
@endif

@if (file_exists(public_path('build/js/common/edit.js')))
    @vite(['resources/js/common/edit.js'])
@endif
