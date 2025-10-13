<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/pages/time-tracking.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Employee Time Tracking</title>
</head>

<body class="time-tracking-page">
    <button id="fullscreen-btn" class="btn btn-dark position-absolute top-0 end-0 m-3">
        <i id="fullscreen-icon" class="fa fa-expand"></i>
    </button>
    

    <div class="cust-container">
        <h2 class="text-center mb-4">Employee Time Tracking</h2>
        <div class="row">
        
            @foreach ($employees as $employee)
                <div class="col-md-6 col-sm-10 col-xl-3 mb-3">
                    <div class="card text-center p-3">
                        <div class="card-body">
                            <div class="profile-img-div">
                                <img class="profile-img" src="{{ asset('uploads/' . $employee->image_url) }}"
                                    alt="Employee Image" />
                                <h5 class="card-title">{{ $employee->name }}</h5>
                            </div>
                            <button class="btn btn-toggle start" id="toggle-btn-{{ $employee->id }}"
                                data-id="{{ $employee->id }}">
                                <i id="icon-btn-{{ $employee->id }}" class="fa fa-play"></i>
                            </button>
                        </div>
                        <div class="play-btns">
                            <p id="time-in-{{ $employee->id }}">--:--</p>
                            <p id="time-out-{{ $employee->id }}">--:--</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal fade" id="pinModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content pin-modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title w-100 text-center">Enter Your PIN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="pin-container">
                        <input type="password" class="pin-input" id="pin-1" maxlength="1" inputmode="numeric">
                        <input type="password" class="pin-input" id="pin-2" maxlength="1" inputmode="numeric">
                        <input type="password" class="pin-input" id="pin-3" maxlength="1" inputmode="numeric">
                        <input type="password" class="pin-input" id="pin-4" maxlength="1" inputmode="numeric">
                    </div>
                    <div class="pin-dots mt-3">
                        <span class="pin-dot" id="dot-1"></span>
                        <span class="pin-dot" id="dot-2"></span>
                        <span class="pin-dot" id="dot-3"></span>
                        <span class="pin-dot" id="dot-4"></span>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" id="clear-pin">Clear</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let employeeIds = $(".btn-toggle").map(function () {
                return $(this).data("id");
            }).get();

            // Fetch time logs when the page loads
            function loadTimeLogsForToday() {
                $.ajax({
                    url: "{{ route('get.time.logs') }}",
                    type: "GET",
                    data: { employee_ids: employeeIds },
                    success: function (data) {
                        console.log('Time logs loaded:', data);
                        Object.keys(data).forEach(employeeId => {
                            let log = data[employeeId];
                            updateEmployeeDisplay(employeeId, log);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading time logs:', error);
                    }
                });
            }

            function updateEmployeeDisplay(employeeId, log) {
                // Update time displays
                $("#time-in-" + employeeId).text(log.time_in || "--:--");
                $("#time-out-" + employeeId).text(log.time_out || "--:--");

                let button = $("#toggle-btn-" + employeeId);
                let icon = $("#icon-btn-" + employeeId);

                // Update button state based on time log
                if (log.time_in && !log.time_out) {
                    // Employee has clocked in but not out - show stop button
                    button.removeClass("start").addClass("stop");
                    icon.removeClass("fa-play").addClass("fa-stop");
                } else {
                    // Employee hasn't clocked in or has completed the day - show start button
                    button.removeClass("stop").addClass("start");
                    icon.removeClass("fa-stop").addClass("fa-play");
                }
            }

            // Load initial data
            loadTimeLogsForToday();

            let currentEmployeeId = null;
            let action = null;

            $(".btn-toggle").click(function () {
                currentEmployeeId = $(this).data("id");
                action = $(this).hasClass("start") ? "start" : "stop";
                $('#pinModal').modal('show');
                setTimeout(() => $('#pin-1').focus(), 300);
            });

            // PIN input handling
            $('.pin-input').on('input', function(e) {
                let value = $(this).val();
                let index = parseInt($(this).attr('id').split('-')[1]);
                
                // Only allow numbers
                if (!/^\d*$/.test(value)) {
                    $(this).val('');
                    return;
                }
                
                // Update dot indicator
                if (value) {
                    $('#dot-' + index).addClass('filled');
                    // Move to next input
                    if (index < 4) {
                        $('#pin-' + (index + 1)).focus();
                    }
                } else {
                    $('#dot-' + index).removeClass('filled');
                }
                
                // Check if all 4 digits are entered
                checkPinComplete();
            });

            // Handle backspace for better UX
            $('.pin-input').on('keydown', function(e) {
                let index = parseInt($(this).attr('id').split('-')[1]);
                
                if (e.key === 'Backspace') {
                    if ($(this).val() === '' && index > 1) {
                        // Move to previous input if current is empty
                        $('#pin-' + (index - 1)).focus();
                    } else {
                        // Clear current input
                        $(this).val('');
                        $('#dot-' + index).removeClass('filled');
                    }
                }
            });

            function checkPinComplete() {
                let pin = '';
                for (let i = 1; i <= 4; i++) {
                    let value = $('#pin-' + i).val();
                    if (value) {
                        pin += value;
                    }
                }
                
                if (pin.length === 4) {
                    verifyPinWithBackend(pin);
                }
            }

            function verifyPinWithBackend(pin) {
                // Show loading state
                $('.pin-input').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('verify.pin') }}",
                    type: "POST",
                    data: {
                        employee_id: currentEmployeeId,
                        pin: pin,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        $('.pin-input').prop('disabled', false);
                        
                        if (response.success) {
                            $('#pinModal').modal('hide');
                            clearPinInputs();
                            
                            // Perform the start/stop action
                            performWorkAction();
                        } else {
                            console.log('PIN verification failed:', response.message || 'Invalid PIN');
                            showPinError();
                        }
                    },
                    error: function(xhr, status, error) {
                        $('.pin-input').prop('disabled', false);
                        console.error('PIN verification error:', error);
                        console.log('PIN verification response:', xhr.responseText);
                        
                        if (xhr.status === 400) {
                            try {
                                let errorResponse = JSON.parse(xhr.responseText);
                                console.log('PIN error message:', errorResponse.message);
                            } catch (e) {
                                console.log('Could not parse error response');
                            }
                        }
                        
                        showPinError();
                    }
                });
            }

            function performWorkAction() {
                let url = action === "start" ? "{{ route('start.work') }}" : "{{ route('stop.work') }}";
                
                // Show loading state
                let button = $("#toggle-btn-" + currentEmployeeId);
                button.prop('disabled', true);
                
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        employee_id: currentEmployeeId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        console.log('Work action response:', response);
                        
                        let icon = $("#icon-btn-" + currentEmployeeId);

                        if (action === "start") {
                            // Handle start work response
                            if (response.time_in) {
                                $("#time-in-" + currentEmployeeId).text(response.time_in);
                                $("#time-out-" + currentEmployeeId).text("--:--");
                                button.removeClass("start").addClass("stop");
                                icon.removeClass("fa-play").addClass("fa-stop");
                            }
                        } else {
                            // Handle stop work response
                            if (response.time_out) {
                                $("#time-out-" + currentEmployeeId).text(response.time_out);
                                button.removeClass("stop").addClass("start");
                                icon.removeClass("fa-stop").addClass("fa-play");
                            }
                        }
                        
                        // Re-enable button
                        button.prop('disabled', false);
                        
                        // Optionally reload all time logs to ensure consistency
                        setTimeout(() => {
                            loadTimeLogsForToday();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        console.error('Work action error:', error);
                        console.log('Response status:', xhr.status);
                        console.log('Response text:', xhr.responseText);
                        
                        // Re-enable button
                        button.prop('disabled', false);
                        
                        // Show specific error message based on status
                        let errorMessage = 'An error occurred. Please try again.';
                        
                        if (xhr.status === 400) {
                            try {
                                let errorResponse = JSON.parse(xhr.responseText);
                                errorMessage = errorResponse.error || errorMessage;
                            } catch (e) {
                                errorMessage = 'Bad request. Please check your input.';
                            }
                        } else if (xhr.status === 404) {
                            errorMessage = 'Service not found. Please contact administrator.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error. Please try again later.';
                        }
                        
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        
                        // Reload time logs to get current state
                        loadTimeLogsForToday();
                    }
                });
            }

            function showPinError() {
                $('.pin-input').addClass('error');
                $('.pin-dots').addClass('error');
                
                setTimeout(() => {
                    $('.pin-input').removeClass('error');
                    $('.pin-dots').removeClass('error');
                    clearPinInputs();
                }, 1000);
            }

            function clearPinInputs() {
                $('.pin-input').val('');
                $('.pin-dot').removeClass('filled');
                $('#pin-1').focus();
            }

            $('#clear-pin').click(function() {
                clearPinInputs();
            });

            // Clear inputs when modal is closed
            $('#pinModal').on('hidden.bs.modal', function() {
                clearPinInputs();
            });

            $("#fullscreen-btn").click(function () {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                    $("#fullscreen-icon").removeClass("fa-expand").addClass("fa-compress");
                } else {
                    document.exitFullscreen();
                    $("#fullscreen-icon").removeClass("fa-compress").addClass("fa-expand");
                }
            });

            // Auto-refresh time logs every 30 seconds to keep data current
            setInterval(loadTimeLogsForToday, 30000);
        });
    </script>
</body>
</html>