@extends('layouts.master')

@section('title') Vacation Balances @endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Vacation Management @endslot
        @slot('title') Employee Vacation Balances @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">Vacation Balances</h4>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group" role="group">
                                <a href="{{ route('vacation-balances.create') }}" class="btn btn-success">
                                    <i class="bx bx-plus me-1"></i> Add Balance
                                </a>
                                <a href="{{ route('vacation-balances.bulk.create') }}" class="btn btn-info">
                                    <i class="bx bx-list-plus me-1"></i> Bulk Add
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-all me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-block-helper me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Advanced Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('vacation-balances.index') }}" id="filterForm">
                                <div class="row g-3">
                                    <div class="col-xl-3 col-lg-4 col-md-6">
                                        <label class="form-label">Filter by Year</label>
                                        <select name="year" class="form-select">
                                            <option value="">All Years</option>
                                            @foreach($years as $year)
                                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-3 col-lg-4 col-md-6">
                                        <label class="form-label">Filter by Employee</label>
                                        <select name="employee_id" class="form-select">
                                            <option value="">All Employees</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-3 col-lg-4 col-md-6">
                                        <label class="form-label">Filter by Vacation Type</label>
                                        <select name="vacation_type_id" class="form-select">
                                            <option value="">All Types</option>
                                            @foreach($vacationTypes as $type)
                                                <option value="{{ $type->id }}" {{ request('vacation_type_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-3 col-lg-12 col-md-6">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary flex-fill">
                                                <i class="bx bx-search me-1"></i> Filter
                                            </button>
                                            <a href="{{ route('vacation-balances.index') }}" class="btn btn-outline-secondary">
                                                <i class="bx bx-reset me-1"></i> Clear
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Total Balances</p>
                                            <h4 class="mb-0">{{ $balances->count() }}</h4>
                                        </div>
                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                <span class="avatar-title">
                                                    <i class="bx bx-list-ul font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Total Days</p>
                                            <h4 class="mb-0">{{ $balances->sum('balance') }}</h4>
                                        </div>
                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                                <span class="avatar-title">
                                                    <i class="bx bx-calendar font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Avg Balance</p>
                                            <h4 class="mb-0">{{ $balances->count() > 0 ? round($balances->avg('balance'), 1) : 0 }}</h4>
                                        </div>
                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                                <span class="avatar-title">
                                                    <i class="bx bx-trending-up font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Low Balances</p>
                                            <h4 class="mb-0">{{ $balances->where('balance', '<=', 5)->count() }}</h4>
                                        </div>
                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                                <span class="avatar-title">
                                                    <i class="bx bx-error font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table id="vacationBalancesTable" class="table table-bordered dt-responsive nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Vacation Type</th>
                                    <th>Year</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($balances as $balance)
                                    <tr>
                                        <td>
                                            <span class="fw-medium">#{{ $balance->id }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-3">
                                                    
                                                        
                                                    <img src="{{ asset('uploads/' . $balance->employee->image_url) }}"
                                                         class="rounded-circle me-2" 
                                                         width="32" height="32" alt="Avatar">
                                                    
                                                </div>
                                                <div>
                                                    <h5 class="font-size-14 mb-1">{{ $balance->employee->name }}</h5>
                                                    <p class="text-muted font-size-13 mb-0">{{ $balance->employee->email ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($balance->employee->departments->count() > 0)
                                                <span class="badge badge-soft-info">
                                                    {{ $balance->employee->departments->first()->name ?? 'N/A' }}
                                                </span>
                                            @else
                                                <span class="text-muted">No Department</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">{{ $balance->vacationType->name }}</span>
                                                <span class="badge badge-soft-{{ $balance->vacationType->is_paid ? 'success' : 'secondary' }}">
                                                    {{ $balance->vacationType->is_paid ? 'Paid' : 'Unpaid' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-primary">{{ $balance->year }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    @if($balance->balance > 15)
                                                        <i class="mdi mdi-circle font-size-10 text-success"></i>
                                                    @elseif($balance->balance > 5)
                                                        <i class="mdi mdi-circle font-size-10 text-warning"></i>
                                                    @else
                                                        <i class="mdi mdi-circle font-size-10 text-danger"></i>
                                                    @endif
                                                </div>
                                                <span class="fw-medium">{{ $balance->balance }}</span>
                                                <small class="text-muted ms-1">days</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($balance->balance > 15)
                                                <span class="badge badge-soft-success">Excellent</span>
                                            @elseif($balance->balance > 10)
                                                <span class="badge badge-soft-info">Good</span>
                                            @elseif($balance->balance > 5)
                                                <span class="badge badge-soft-warning">Low</span>
                                            @else
                                                <span class="badge badge-soft-danger">Critical</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('vacation-balances.show', $balance->id) }}" 
                                                   class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="View Details">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a href="{{ route('vacation-balances.edit', $balance->id) }}" 
                                                   class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="Edit Balance">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form action="{{ route('vacation-balances.destroy', $balance->id) }}" 
                                                      method="POST" style="display: inline-block;" id="delete-balance-{{ $balance->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                                            data-bs-toggle="tooltip" title="Delete Balance"
                                                            onclick="confirmDelete('Vacation Balance').then(result => { if(result) document.getElementById('delete-balance-{{ $balance->id }}').submit(); })">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#vacationBalancesTable').DataTable({
                "order": [[ 0, "desc" ]],
                "pageLength": 25,
                "responsive": true,
                "language": {
                    "search": "Search balances:",
                    "lengthMenu": "Show _MENU_ balances per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ balances",
                    "infoFiltered": "(filtered from _MAX_ total balances)"
                },
                "columnDefs": [
                    { "orderable": false, "targets": 7 }
                ]
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection