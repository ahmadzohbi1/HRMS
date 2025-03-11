<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter Your PIN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="password" id="pin-input" class="form-control" placeholder="Enter PIN"
                        inputmode="numeric">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
            $.ajax({
                url: "{{ route('get.time.logs') }}",
                type: "GET",
                data: { employee_ids: employeeIds },
                success: function (data) {
                    Object.keys(data).forEach(employeeId => {
                        let log = data[employeeId];
                        $("#time-in-" + employeeId).text(log.time_in || "--:--");
                        $("#time-out-" + employeeId).text(log.time_out || "--:--");

                        let button = $("#toggle-btn-" + employeeId);
                        let icon = $("#icon-btn-" + employeeId);

                        if (log.time_in && !log.time_out) {
                            button.removeClass("start").addClass("stop");
                            icon.removeClass("fa-play").addClass("fa-stop");
                        } else {
                            button.removeClass("stop").addClass("start");
                            icon.removeClass("fa-stop").addClass("fa-play");
                        }
                    });
                }
            });

            let currentEmployeeId = null;
            let action = null;

            $(".btn-toggle").click(function () {
                currentEmployeeId = $(this).data("id");
                action = $(this).hasClass("start") ? "start" : "stop";
                $('#pinModal').modal('show');
            });

            $("#pin-input").on('input', function () {
                let pin = $(this).val();
                if (pin === "") {
                    return;
                }

                $.ajax({
                    url: "{{ route('verify.pin') }}",
                    type: "POST",
                    data: {
                        employee_id: currentEmployeeId,
                        pin: pin,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#pinModal').modal('hide');
                            $('#pin-input').val('');

                            $.ajax({
                                url: action === "start" ? "{{ route('start.work') }}" : "{{ route('stop.work') }}",
                                type: "POST",
                                data: {
                                    employee_id: currentEmployeeId,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    let button = $("#toggle-btn-" + currentEmployeeId);
                                    let icon = $("#icon-btn-" + currentEmployeeId);

                                    if (action === "start") {
                                        $("#time-in-" + currentEmployeeId).text(response.time_in);
                                        button.removeClass("start").addClass("stop");
                                        icon.removeClass("fa-play").addClass("fa-stop");
                                    } else {
                                        $("#time-out-" + currentEmployeeId).text(response.time_out);
                                        button.removeClass("stop").addClass("start");
                                        icon.removeClass("fa-stop").addClass("fa-play");
                                    }
                                }
                            });
                        } else {
                            alert("Incorrect PIN.");
                        }
                    }
                });
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
</style>

</html>
