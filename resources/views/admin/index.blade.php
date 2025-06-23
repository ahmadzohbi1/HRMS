@extends('layouts.master')

@section('title')
    Sidebar Dashboard
@endsection

@section('css')
    <!-- Lightbox css -->
    <link href="{{ URL::asset('assets/ibs/magnific-popup/magnific-popup.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Dashboard @endslot
        @slot('title') Dashboard @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-12">
            <div class="row">
                <!-- Total Employees Card -->
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Total Employees</p>
                                    <h4 class="mb-0">{{ $employees }}</h4>
                                </div>
                                <div class="align-self-center flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-primary">
                                            <i class="bx bx-user font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Month Salary Card -->
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Current Month Salary</p>
                                    <h4 class="mb-0">${{ number_format($monthlySalaryData['salaries'][count($monthlySalaryData['salaries'])-1] ?? 0, 2) }}</h4>
                                </div>
                                <div class="align-self-center flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-success mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-success">
                                            <i class="bx bx-money font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Average Monthly Salary Card -->
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Average Monthly Salary</p>
                                    <h4 class="mb-0">${{ count($monthlySalaryData['salaries']) > 0 ? number_format(array_sum($monthlySalaryData['salaries']) / count($monthlySalaryData['salaries']), 2) : '0.00' }}</h4>
                                </div>
                                <div class="align-self-center flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-warning mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-warning">
                                            <i class="bx bx-trending-up font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Salary Chart -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Monthly Salary Payments (Last 12 Months)</h4>
                    <div style="position: relative; height: 400px;">
                        <canvas id="salaryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gender Distribution Chart -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Employee Gender Distribution</h4>
                    <div style="position: relative; height: 400px;">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Salary Statistics</h4>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5 class="font-size-20 text-primary">${{ count($monthlySalaryData['salaries']) > 0 ? number_format(max($monthlySalaryData['salaries']), 2) : '0.00' }}</h5>
                                <p class="text-muted">Highest Monthly Payment</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5 class="font-size-20 text-success">${{ count($monthlySalaryData['salaries']) > 0 ? number_format(min($monthlySalaryData['salaries']), 2) : '0.00' }}</h5>
                                <p class="text-muted">Lowest Monthly Payment</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5 class="font-size-20 text-warning">${{ number_format(array_sum($monthlySalaryData['salaries']), 2) }}</h5>
                                <p class="text-muted">Total Paid (12 Months)</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                @php
                                    $salaries = $monthlySalaryData['salaries'];
                                    $currentMonth = count($salaries) > 0 ? $salaries[count($salaries) - 1] : 0;
                                    $previousMonth = count($salaries) > 1 ? $salaries[count($salaries) - 2] : 0;
                                    $trend = $previousMonth > 0 ? (($currentMonth - $previousMonth) / $previousMonth) * 100 : 0;
                                @endphp
                                <h5 class="font-size-20 {{ $trend >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $trend >= 0 ? '+' : '' }}{{ number_format($trend, 1) }}%
                                </h5>
                                <p class="text-muted">Month-over-Month Change</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Gender Chart
            var genderCtx = document.getElementById('genderChart').getContext('2d');
            var genderChart = new Chart(genderCtx, {
                type: 'pie',
                data: {
                    labels: ['Male', 'Female'],
                    datasets: [{
                        data: [{{ $maleCount }}, {{ $femaleCount }}],
                        backgroundColor: ['#007bff', '#ff6384'],
                        hoverBackgroundColor: ['#0056b3', '#d63384']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });

            // Monthly Salary Chart
            var salaryCtx = document.getElementById('salaryChart').getContext('2d');
            var salaryChart = new Chart(salaryCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlySalaryData['months']) !!},
                    datasets: [{
                        label: 'Total Monthly Salary ($)',
                        data: {!! json_encode($monthlySalaryData['salaries']) !!},
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.1,
                        pointBackgroundColor: '#28a745',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Total Salary: $' + new Intl.NumberFormat().format(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + new Intl.NumberFormat().format(value);
                                }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection