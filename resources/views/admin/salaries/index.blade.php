@extends('layouts.master')

@section('title', 'Employees Salaries')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1', 'Salaries')
    @slot('li_2', route('salary.index'))
    @slot('title', 'Employees Salaries')
    @endcomponent

    @php
        // Default to the current month and year if not provided
        $selectedMonth = request('month', date('m'));
        $selectedYear = request('year', date('Y'));

        // Previous and next month logic
        $prevMonth = $selectedMonth == 1 ? 12 : $selectedMonth - 1;
        $prevYear = $selectedMonth == 1 ? $selectedYear - 1 : $selectedYear;
        $nextMonth = $selectedMonth == 12 ? 1 : $selectedMonth + 1;
        $nextYear = $selectedMonth == 12 ? $selectedYear + 1 : $selectedYear;
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('salary.index') }}" id="filter-form">
                        <div class="row" style="margin-bottom: 15px">
                            <div class="col-md-4">
                                <label for="month">Select Month</label>
                                <select class="form-control" name="month" id="month">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="year">Select Year</label>
                                <select class="form-control" name="year" id="year">
                                    @foreach(range(date('Y') - 5, 2060) as $y)
                                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2" style="margin-right:15px">Filter</button>
                                <a href="{{ route('salary.index') }}" class="btn btn-secondary mr-3">Reset</a>
                            </div>
                        </div>
                    </form>

                    <!-- Salaries Table -->
                    <table id="datatable" class="table table-hover table-bordered nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Salary</th>
                                <th>Currency</th>
                                <th>Month</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($salaries as $salary)
                                <tr>
                                    <td>{{ $salary->employee_id }}</td>
                                    <td>{{ $salary->employee->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($salary->salary, 2) }}</td>
                                    <td>{{ $salary->currency }}</td>
                                    <td>{{ date('F Y', strtotime($salary->month)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No salaries found for the selected month.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <a href="{{ route('salary.index', ['month' => $prevMonth, 'year' => $prevYear]) }}"
                            class="btn btn-outline-primary">
                            ← Previous Month
                        </a>
                        <h5>{{ date('F Y', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear)) }}</h5>
                        <a href="{{ route('salary.index', ['month' => $nextMonth, 'year' => $nextYear]) }}"
                            class="btn btn-outline-primary">
                            Next Month →
                        </a>
                    </div>
                    <div class="d-flex justify-content-end mb-4 mt-4" id="action_btns">
                        <form action="{{ route('employee.timelogs.updateAllHours') }}" method="POST">
                            @csrf
                            <input type="hidden" name="month" value="{{ $selectedMonth }}">
                            <input type="hidden" name="year" value="{{ $selectedYear }}">
                            <button type="submit" class="btn btn-rounded btn-success waves-effect waves-light">
                                Update Total Hours
                            </button>
                        </form>
                    </div>
                    
                    <!-- Laravel Pagination Links -->
                    <div id="paginate_emp" class="d-flex justify-content-center mt-3">
                        {{ $salaries->appends(['month' => $selectedMonth, 'year' => $selectedYear])->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const filterForm = document.getElementById("filter-form");
            const monthSelect = document.getElementById("month");
            const yearSelect = document.getElementById("year");

            // Auto-submit when selection changes
            monthSelect.addEventListener("change", () => filterForm.submit());
            yearSelect.addEventListener("change", () => filterForm.submit());
        });
    </script>
@endsection

<style >
     #paginate_emp{
        text-align: center;
    }
    #paginate_emp svg{
        width: 20px;
    }
    #paginate_emp .text-sm{
        margin-top: 2rem;
    }
   
</style>
