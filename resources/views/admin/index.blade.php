@extends('layouts.master')

@section('title')
    Sidebar Dashboard
@endsection

@section('css')
    <!-- Lightbox css -->
    <link href="/assets/libs/magnific-popup/magnific-popup.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1') Dashboard @endslot
    @slot('title') Dashboard @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-12">
            <div class="row">
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

                <!-- Gender Pie Chart -->


            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Employee Gender Distribution</h4>
                <canvas id="genderChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById('genderChart').getContext('2d');
            var genderChart = new Chart(ctx, {
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
                    responsive: true
                }
            });
        });
    </script>
@endsection