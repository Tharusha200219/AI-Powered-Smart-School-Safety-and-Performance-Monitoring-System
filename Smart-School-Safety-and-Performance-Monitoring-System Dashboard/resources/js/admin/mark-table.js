// Mark Table enhancements
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function () {
        try {
            const table = $("#mark-table").DataTable();
            $(".dataTables_filter input").attr(
                "placeholder",
                "Search by student, subject, year, term..."
            );

            // Filter functionality
            function applyFilters() {
                const studentFilter = $("#student_filter").val();
                const subjectFilter = $("#subject_filter").val();

                // Update URL with filter parameters
                const url = new URL(window.location);
                if (studentFilter) {
                    url.searchParams.set("student_filter", studentFilter);
                } else {
                    url.searchParams.delete("student_filter");
                }
                if (subjectFilter) {
                    url.searchParams.set("subject_filter", subjectFilter);
                } else {
                    url.searchParams.delete("subject_filter");
                }

                // Reload the page with filters
                window.location.href = url.toString();
            }

            function clearFilters() {
                const url = new URL(window.location);
                url.searchParams.delete("student_filter");
                url.searchParams.delete("subject_filter");
                window.location.href = url.toString();
            }

            // Bind filter events
            $("#apply_filters").on("click", applyFilters);
            $("#student_filter, #subject_filter").on("change", applyFilters);
            $("#clear_filters").on("click", clearFilters);

            // Simple responsive adjustments
            function handleResponsive() {
                const isMobile = window.innerWidth <= 768;
                if (isMobile) {
                    table.page.len(10).draw();
                    $(".dataTables_length").hide();
                } else {
                    $(".dataTables_length").show();
                }
            }

            handleResponsive();

            let resizeTimer;
            window.addEventListener("resize", function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function () {
                    handleResponsive();
                    table.responsive.recalc();
                }, 250);
            });
        } catch (e) {
            // DataTable may not be initialized yet
            // console.log('Mark table init error', e);
        }
    }, 100);
});
