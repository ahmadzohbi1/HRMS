<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holiday Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('/assets/css/calender.css') }}" rel="stylesheet" type="text/css">
    <!-- <link href="{{ asset('/assets/css/custom.css') }}" rel="stylesheet" type="text/css" /> -->
</head>
<body>
    <div class="container-fluid">
        <div class="calendar-container">
            <div class="calendar-header">
                <h1><i class="fas fa-calendar-alt me-3"></i>Holiday Calendar</h1>
                <div class="month-navigation">
                    <button class="nav-btn" onclick="changeMonth(-1)">
                        <i class="fas fa-chevron-left"></i> Previous
                    </button>
                    <div class="current-month" id="currentMonth"></div>
                    <button class="nav-btn" onclick="changeMonth(1)">
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color legend-today"></div>
                        <span>Today</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color legend-holiday"></div>
                        <span>Holiday</span>
                    </div>
                </div>
            </div>

            <div class="calendar-grid" id="calendarGrid">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <span class="ms-2">Loading calendar...</span>
                </div>
            </div>
        </div>

        <div class="holiday-list">
            <h3 class="text-center mb-4">
                <i class="fas fa-star text-warning me-2"></i>
                Holidays This Month
            </h3>
            <div id="holidayList">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span class="ms-2">Loading holidays...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Features -->
    <div class="text-center mt-4 mb-4">
        <button class="btn btn-primary me-2" onclick="goToToday()">
            <i class="fas fa-calendar-day me-1"></i>
            Go to Today
        </button>
        <button class="btn btn-success me-2" onclick="showHolidayMonthSelector()">
            <i class="fas fa-star me-1"></i>
            Jump to Holiday Month
        </button>
        <button class="btn btn-outline-secondary" onclick="window.print()">
            <i class="fas fa-print me-1"></i>
            Print Calendar
        </button>
    </div>

    <!-- Holiday Month Selector Modal -->
    <div class="modal fade" id="holidayMonthModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-star me-2"></i>
                        Jump to Holiday Month
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="holidayMonthList">
                    <p class="text-center">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Holiday Data -->
    <script>
        // Pass holiday data from Laravel to JavaScript
        window.holidaysData = @json($holidays);
    </script>
    
    <!-- Holiday Calendar JS -->
    <script src="{{ asset('/assets/js/holiday-calender.js') }}"></script>
</body>
</html>