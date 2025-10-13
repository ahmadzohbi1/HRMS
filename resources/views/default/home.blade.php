<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Employee Time Tracking</title>
</head>

<body>
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

<style>
    body {
        background-color: #c6f1f3;
    }

    .container {
        margin-top: 50px;
    }

    .card-body {
        justify-content: space-between;
        display: flex;
        flex: 1 1 auto;
        padding: 1rem 0rem;
    }

    .card-title {
        font-size: 17px;
        margin-left: 10px;
        margin-top: 13px;
        margin-bottom: .5rem;
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
        background: linear-gradient(135deg, #66caea, #4ba25a);
        color: white;
    }

    .card:hover {
        transform: scale(1.05);
    }

    .btn-toggle {
        width: 100px;
        font-size: 1.1rem;
        transition: background 0.3s ease-in-out;
    }

    .btn-toggle.start {
        padding-left: 12px;
        height: 50px;
        width: 50px;
        border-radius: 30px;
        background: white;
        color: green;
    }

    .btn-toggle.stop {
        padding-left: 12px;
        height: 50px;
        width: 50px;
        border-radius: 30px;
        background: white;
        color: red;
    }

    .btn-toggle:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .profile-img-div {
        display: flex;
    }

    .profile-img {
        border-radius: 40px;
        width: 50px;
        height: 55px;
    }

    .play-btns {
        width: 200px;
        margin: 60px 0px 0px 31px;
        position: absolute;
        justify-content: space-evenly;
        display: flex;
    }

    .cust-container {
        margin: 10px;
    }

    /* PIN Modal Styles */
    .pin-modal-content {
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        border: none;
    }

    .pin-container {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin: 20px 0;
    }

    .pin-input {
        width: 60px;
        height: 60px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        background: #f8f9fa;
        transition: all 0.3s ease;
        outline: none;
    }

    .pin-input:focus {
        border-color: #4ba25a;
        background: white;
        box-shadow: 0 0 0 3px rgba(75, 162, 90, 0.1);
        transform: scale(1.05);
    }

    .pin-input.error {
        border-color: #dc3545;
        background: #ffe6e6;
        animation: shake 0.5s;
    }

    .pin-dots {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .pin-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #e0e0e0;
        transition: all 0.3s ease;
    }

    .pin-dot.filled {
        background: #4ba25a;
        transform: scale(1.2);
    }

    .pin-dots.error .pin-dot {
        background: #dc3545;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .modal-header .modal-title {
        color: #333;
        font-weight: 600;
        font-size: 1.3rem;
    }

    .btn-close {
        filter: none;
        opacity: 0.7;
    }

    .btn-close:hover {
        opacity: 1;
    }

    /* Mobile responsiveness */
    @media (max-width: 576px) {
        .pin-input {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
        
        .pin-container {
            gap: 10px;
        }
    }
</style>

</html>