@extends('layouts.master')

@section('title')
    Employee Timesheet
@endsection

@section('content')
    <div class="calendar-container">
        <!-- Year Header -->
        <div class="year-header">
            <h1 id="year-display">{{ now()->year }}</h1>
        </div>

        <h2>Employee Timesheet - Year</h2>

        <!-- Link to the external CSS file -->
        <link href="{{ asset('assets/css/timesheet.css') }}" rel="stylesheet" />

        <!-- Year and Month Filter -->
        <div class="filter-container mb-3">
            <div class="filter-box">
                <select id="year-select">
                    @for ($i = 2020; $i <= 2050; $i++)
                        <option value="{{ $i }}" {{ $i == now()->year ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="filter-box">
                <select id="month-select">
                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $key => $month)
                        <option value="{{ $key }}" {{ $key == now()->month - 1 ? 'selected' : '' }}>{{ $month }}</option>
                    @endforeach
                </select>
            </div>
            <button id="filter-btn" class="btn btn-primary">Filter</button>
        </div>

        <!-- Pagination Controls -->
        <div class="pagination-controls">
            <button id="prev-month" class="btn btn-primary">Previous Month</button>
            <button id="next-month" class="btn btn-primary">Next Month</button>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-end mb-3">
            <button id="edit-mode" class="btn btn-sm btn-warning">Edit Time Logs</button>
            <button id="create-mode" class="btn btn-sm btn-success">Create Time Logs</button>
        </div>

        <!-- Month Calendar Display -->
        <div id="calendar"></div>

        <!-- Total Hours Worked -->
        <div id="total-hours" class="total-hours">
            <p><strong>Total Hours Worked: </strong><span id="total-hours-text">00:00:00</span></p>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const employeesTime = @json($employees_time);
    const employeeId = @json($id);
    let currentMonth = moment().month(); // Current month (0-indexed)
    let currentYear = moment().year(); // Current year
    let isEditMode = false;
    let isCreateMode = false;

    document.getElementById('edit-mode').addEventListener('click', () => {
        isEditMode = !isEditMode;
        isCreateMode = false;
        generateCalendar(currentMonth, currentYear);
    });

    document.getElementById('create-mode').addEventListener('click', () => {
        isCreateMode = !isCreateMode;
        isEditMode = false;
        generateCalendar(currentMonth, currentYear);
    });

    document.getElementById('filter-btn').addEventListener('click', () => {
        const selectedYear = parseInt(document.getElementById('year-select').value);
        const selectedMonth = parseInt(document.getElementById('month-select').value);
        currentYear = selectedYear;
        currentMonth = selectedMonth;
        generateCalendar(currentMonth, currentYear);
    });

    document.getElementById('prev-month').addEventListener('click', () => {
        // Go to the previous month
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11; // Set to December
            currentYear--; // Decrease year
        }
        generateCalendar(currentMonth, currentYear);
    });

    document.getElementById('next-month').addEventListener('click', () => {
        // Go to the next month
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0; // Set to January
            currentYear++; // Increase year
        }
        generateCalendar(currentMonth, currentYear);
    });

    function generateCalendar(month, year) {
        let calendarHtml = '';
        let monthName = moment().month(month).format('MMMM');
        let daysInMonth = moment().year(year).month(month).daysInMonth();
        let firstDayOfMonth = moment().year(year).month(month).startOf('month').day();
        let totalWorkedSeconds = 0;

        calendarHtml += `<div class="month-container">
                            <h3>${monthName} ${year}</h3>
                            <div class="month-grid">`;

        const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        daysOfWeek.forEach(day => {
            calendarHtml += `<div class="day-header">${day}</div>`;
        });

        for (let i = 0; i < firstDayOfMonth; i++) {
            calendarHtml += `<div class="day-box empty"></div>`;
        }

        for (let day = 1; day <= daysInMonth; day++) {
            let dayDate = moment().year(year).month(month).date(day).format('YYYY-MM-DD');
            let timeLog = employeesTime.find(log => log.date === dayDate);
            let timeIn = timeLog ? timeLog.time_in : '';
            let timeOut = timeLog ? timeLog.time_out : '';

            if (timeIn && timeOut) {
                let timeInMoment = moment(timeIn, 'HH:mm');
                let timeOutMoment = moment(timeOut, 'HH:mm');
                let workedDuration = moment.duration(timeOutMoment.diff(timeInMoment));
                totalWorkedSeconds += workedDuration.asSeconds();
            }

            calendarHtml += `<div class="day-box" data-date="${dayDate}">`;
            calendarHtml += `<h2>${day}</h2>`;

            if (timeLog) {
                if (isEditMode) {
                    calendarHtml += `
                    <p><strong>IN:</strong> <input type="time" value="${timeIn}" data-date="${dayDate}" class="time-in-input"></p>
                    <p><strong>Out:</strong> <input type="time" value="${timeOut}" data-date="${dayDate}" class="time-out-input"></p>
                    <input type="hidden" value="${timeLog.id}" class="log-id-input">
                    <input type="hidden" value="${timeLog.employee_id}" class="employee-id-input">
                    <button class="save-edit-btn btn btn-sm btn-primary" data-date="${dayDate}">Save</button>
                `;
                } else {
                    calendarHtml += `
                    <p><strong>IN:</strong> ${timeIn}</p>
                    <p><strong>Out:</strong> ${timeOut}</p>
                `;
                }
            } else {
                if (isCreateMode) {
                    calendarHtml += `
                    <p><strong>IN:</strong> <input type="time" class="new-time-in"></p>
                    <p><strong>Out:</strong> <input type="time" class="new-time-out"></p>
                    <button class="save-create-btn btn btn-sm btn-success" data-date="${dayDate}">Create</button>
                `;
                } else {
                    calendarHtml += '';
                }
            }

            calendarHtml += `</div>`;
        }

        calendarHtml += `</div></div>`;
        document.getElementById('calendar').innerHTML = calendarHtml;

        let totalWorkedHours = moment.duration(totalWorkedSeconds, 'seconds');
        let hours = Math.floor(totalWorkedHours.asHours());
        let minutes = totalWorkedHours.minutes();
        let seconds = totalWorkedHours.seconds();
        
        document.getElementById('total-hours-text').innerText = `${hours}:${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

        // Attach event listeners for save buttons
        document.querySelectorAll('.save-edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                let date = this.getAttribute('data-date');
                let timeInInput = document.querySelector(`.time-in-input[data-date="${date}"]`);
                let timeOutInput = document.querySelector(`.time-out-input[data-date="${date}"]`);

                let timeIn = timeInInput && timeInInput.value ? timeInInput.value : "00:00";
                let timeOut = timeOutInput && timeOutInput.value ? timeOutInput.value : "16:00";

                let logIdElement = this.closest('.day-box').querySelector('.log-id-input');
                let employeeIdElement = this.closest('.day-box').querySelector('.employee-id-input');

                let logId = logIdElement.value;
                let employeeId = employeeIdElement.value;

                updateTimeLog(logId, date, timeIn, timeOut, employeeId);
            });
        });

        document.querySelectorAll('.save-create-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                let date = this.getAttribute('data-date');
                let timeIn = this.parentNode.querySelector('.new-time-in').value;
                let timeOut = this.parentNode.querySelector('.new-time-out').value;
                createTimeLog(date, timeIn, timeOut);
            });
        });
    }

    function updateTimeLog(logId, date, timeIn, timeOut, employeeId) {
        if (!timeOut) {
            timeOut = '16:00';
        }

        fetch("{{ route('employee.timelogs.update', ['id' => ':id']) }}".replace(':id', logId), {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: JSON.stringify({
                date: date,
                time_in: timeIn,
                time_out: timeOut,
                employee_id: employeeId
            })
        }).then(response => response.json()).then(data => {
            if (data.message === 'Time Log updated successfully') {
                Swal.fire({
                    icon: 'success',
                    title: 'Time Log updated successfully!',
                    timer: 1500,
                    willClose: () => {
                        generateCalendar(currentMonth, currentYear);
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                });
            }
        });
    }

    function createTimeLog(date, timeIn, timeOut) {
        if (!timeOut) {
            timeOut = '16:00';
        }

        fetch("{{ route('employee.timelogs.store', ['id' => $id]) }}", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: JSON.stringify({
                date: date,
                time_in: timeIn,
                time_out: timeOut,
                employee_id: '{{ $employee->id }}'
            })
        }).then(response => response.json()).then(data => {
            if (data.message === 'Time Log created successfully') {
                Swal.fire({
                    icon: 'success',
                    title: 'Time Log created successfully!',
                    timer: 1500,
                    willClose: () => {
                        generateCalendar(currentMonth, currentYear);
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                });
            }
        });
    }

    generateCalendar(currentMonth, currentYear);
});

    </script>
@endsection