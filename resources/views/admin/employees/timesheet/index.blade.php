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

        <h2>{{ $employee->name }}'s Timesheet - {{ now()->year }}</h2>

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

        <!-- Legend -->
        <div class="legend">
            <div class="legend-item">
                <div class="legend-dot" style="background-color: #007bff;"></div>
                <span>Holiday</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background-color: #28a745;"></div>
                <span>Vacation</span>
            </div>
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
            const holidays = @json($holidays);
            const vacationDates = @json($vacation_dates);
            const employeeId = @json($id);
            let currentMonth = moment().month();
            let currentYear = moment().year();
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
                currentMonth -= 1;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear -= 1;
                }
                generateCalendar(currentMonth, currentYear);
            });

            document.getElementById('next-month').addEventListener('click', () => {
                currentMonth += 1;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear += 1;
                }
                generateCalendar(currentMonth, currentYear);
            });

            function calculateTotalHours() {
                let totalHours = 0;

                const currentMonthStart = moment().year(currentYear).month(currentMonth).startOf('month').format('YYYY-MM-DD');
                const currentMonthEnd = moment().year(currentYear).month(currentMonth).endOf('month').format('YYYY-MM-DD');

                employeesTime.forEach(log => {
                    if (log.time_in && log.time_out && log.date >= currentMonthStart && log.date <= currentMonthEnd) {
                        const timeIn = moment(log.time_in, 'HH:mm:ss');
                        const timeOut = moment(log.time_out, 'HH:mm:ss');
                        totalHours += timeOut.diff(timeIn, 'hours', true);
                    }
                });

                const hours = Math.floor(totalHours);
                const minutes = Math.floor((totalHours - hours) * 60);
                const seconds = Math.floor(((totalHours - hours) * 60 - minutes) * 60);

                const monthName = moment().month(currentMonth).format('MMMM');
                document.getElementById('total-hours-text').innerText = `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                document.querySelector('#total-hours p strong').innerText = `Total Hours Worked (${monthName} ${currentYear}): `;
            }

            function generateCalendar(month, year) {
                let calendarHtml = '';
                let monthName = moment().month(month).format('MMMM');
                let daysInMonth = moment().year(year).month(month).daysInMonth();
                let firstDayOfMonth = moment().year(year).month(month).startOf('month').day();

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

                    // Check if this date is a holiday or vacation day
                    let isHoliday = holidays.includes(dayDate);
                    let isVacation = vacationDates.includes(dayDate);

                    calendarHtml += `<div class="day-box" data-date="${dayDate}">`;
                    
                    // Add indicator dots
                    if (isHoliday) {
                        calendarHtml += `<div class="holiday-indicator" title="Holiday"></div>`;
                    } else if (isVacation) {
                        calendarHtml += `<div class="vacation-indicator" title="Vacation Day"></div>`;
                    }
                    
                    calendarHtml += `<h2>${day}</h2>`;

                    if (timeLog) {
                        if (isEditMode) {
                            calendarHtml += `
                            <p><strong>IN:</strong> <input type="time" value="${timeIn || ''}" data-date="${dayDate}" class="time-in-input"></p>
                            <p><strong>Out:</strong> <input type="time" value="${timeOut || ''}" data-date="${dayDate}" class="time-out-input"></p>
                            <input type="hidden" value="${timeLog.id}" class="log-id-input">
                            <input type="hidden" value="${timeLog.employee_id}" class="employee-id-input">
                            <button class="save-edit-btn btn btn-sm btn-primary" data-date="${dayDate}">Save</button>
                        `;
                        } else {
                            let displayTimeIn = timeIn ? timeIn : 'Not Set';
                            let displayTimeOut = timeOut ? timeOut : 'Not Set';

                            calendarHtml += `
                            <p><strong>IN:</strong> <span class="${!timeIn ? 'not-set' : ''}">${displayTimeIn}</span></p>
                            <p><strong>Out:</strong> <span class="${!timeOut ? 'not-set' : ''}">${displayTimeOut}</span></p>
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
                calculateTotalHours();

                // Attach event listeners for save buttons
                document.querySelectorAll('.save-edit-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        let date = this.getAttribute('data-date');
                        let timeIn = document.querySelector(`.time-in-input[data-date="${date}"]`).value;
                        let timeOut = document.querySelector(`.time-out-input[data-date="${date}"]`).value;
                        let logId = this.closest('.day-box').querySelector('.log-id-input').value;
                        let employeeId = document.querySelector('.employee-id-input').value;
                        updateTimeLog(logId, timeIn, timeOut, employeeId);
                        calculateTotalHours();
                    });
                });

                document.querySelectorAll('.save-create-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        let date = this.getAttribute('data-date');
                        let timeIn = this.parentNode.querySelector('.new-time-in').value;
                        let timeOut = this.parentNode.querySelector('.new-time-out').value;
                        createTimeLog(date, timeIn, timeOut);
                        calculateTotalHours();
                    });
                });
            }

            function updateTimeLog(logId, timeIn, timeOut, employeeId) {
                fetch("{{ route('employee.timelogs.update', ['id' => ':id']) }}".replace(':id', logId), {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    body: JSON.stringify({
                        time_in: timeIn,
                        time_out: timeOut
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