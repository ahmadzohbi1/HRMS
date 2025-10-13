@extends('layouts.master')

@section('content')
    @component('components.breadcrumb')
    @slot('li_1') Holidays @endslot
    @slot('title') Holidays List @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Holiday Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Total Holidays</p>
                                            <h4 class="mb-0">{{ $holidays->count() }}</h4>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-calendar font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">This Year</p>
                                            <h4 class="mb-0">{{ $holidays->filter(fn($h) => $h->date->year == date('Y'))->count() }}</h4>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-success align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-success">
                                                <i class="bx bx-time font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Upcoming</p>
                                            <h4 class="mb-0">{{ $holidays->filter(fn($h) => $h->date >= now())->count() }}</h4>
                                        </div>
                                        <div class="avatar-sm rounded-circle bg-warning align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-warning">
                                                <i class="bx bx-bell font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <a href="/holiday/calender" class="btn btn-info me-2">
                                <i class="bx bx-calendar-alt me-1"></i>
                                View Calendar
                            </a>
                        </div>
                        <div>
                        <a href="{{ route('holidays.create') }}"
                            class="btn btn-rounded btn-success waves-effect waves-light">
                            <i class="bx bx-plus font-size-16 me-2 align-middle"></i>
                            Add Holiday
                        </a>
                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Holiday Name</th>
                                <th>Holiday Date</th>
                                <th>Day of Week</th>
                                <th>Year</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($holidays as $holiday)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <i class="bx bx-calendar text-danger me-1"></i>
                                        <strong>{{ $holiday->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $holiday->date->format('F j, Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $holiday->date->format('l') }}
                                        </span>
                                    </td>
                                    <td>{{ $holiday->date->format('Y') }}</td>
                                    <td>
                                        <a href="{{ route('holidays.show', $holiday->id) }}" 
                                           class="btn btn-primary btn-sm"
                                           title="View Details">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <a href="{{ route('holidays.edit', $holiday->id) }}" 
                                           class="btn btn-warning btn-sm"
                                           title="Edit">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form action="{{ route('holidays.destroy', $holiday->id) }}" method="POST"
                                            class="d-inline-block" id="delete-holiday-{{ $holiday->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm"
                                                    title="Delete"
                                                    onclick="confirmDelete('Holiday').then(result => { if(result) document.getElementById('delete-holiday-{{ $holiday->id }}').submit(); })">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection