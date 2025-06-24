@extends('layouts.master')

@section('title') Vacation Balance Details @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Vacation Balances @endslot
        @slot('title') Balance Details @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title mb-0">Vacation Balance Details</h4>
                        <div class="ms-auto">
                            <span class="badge badge-soft-primary">ID: {{ $balance->id }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Balance Status -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h2 class="text-primary mb-1">{{ $balance->balance }}</h2>
                                            <p class="text-muted mb-0">Days Available</p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($balance->balance > 15)
                                                <span class="badge badge-soft-success fs-6 px-3 py-2">Excellent Balance</span>
                                            @elseif($balance->balance > 10)
                                                <span class="badge badge-soft-info fs-6 px-3 py-2">Good Balance</span>
                                            @elseif($balance->balance > 5)
                                                <span class="badge badge-soft-warning fs-6 px-3 py-2">Low Balance</span>
                                            @else
                                                <span class="badge badge-soft-danger fs-6 px-3 py-2">Critical Balance</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Information -->
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <h5 class="font-size-15 mb-3">Employee Information</h5>
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="ps-0 fw-medium">Name:</td>
                                            <td>{{ $balance->employee->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Email:</td>
                                            <td>{{ $balance->employee->email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Department:</td>
                                            <td>
                                                @if($balance->employee->departments->count() > 0)
                                                    {{ $balance->employee->departments->first()->name }}
                                                @else
                                                    <span class="text-muted">No Department</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Position:</td>
                                            <td>{{ $balance->employee->position->name ?? 'N/A' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h5 class="font-size-15 mb-3">Vacation Type Details</h5>
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="ps-0 fw-medium">Type:</td>
                                            <td>{{ $balance->vacationType->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Payment:</td>
                                            <td>
                                                <span class="badge badge-soft-{{ $balance->vacationType->is_paid ? 'success' : 'secondary' }}">
                                                    {{ $balance->vacationType->is_paid ? 'Paid' : 'Unpaid' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Max Days:</td>
                                            <td>
                                                {{ $balance->vacationType->isUnlimited() ? 'Unlimited' : $balance->vacationType->max_days_allowed . ' days' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Year:</td>
                                            <td>
                                                <span class="badge badge-soft-primary">{{ $balance->year }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Balance History -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="font-size-15 mb-3">Balance History</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td class="fw-medium">Created On:</td>
                                            <td>{{ $balance->created_at->format('F d, Y g:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium">Last Updated:</td>
                                            <td>{{ $balance->updated_at->format('F d, Y g:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium">Days Since Creation:</td>
                                            <td>{{ $balance->created_at->diffInDays(now()) }} days</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium">Last Modified:</td>
                                            <td>{{ $balance->updated_at->diffForHumans() }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('vacation-balances.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Back to List
                        </a>
                        <a href="{{ route('vacation-balances.edit', $balance->id) }}" class="btn btn-primary">
                            <i class="bx bx-edit me-1"></i> Edit Balance
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Sidebar -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('vacation-balances.create') }}" class="btn btn-outline-success">
                            <i class="bx bx-plus me-1"></i> Create New Balance
                        </a>
                        <a href="{{ route('vacation-balances.bulk.create') }}" class="btn btn-outline-info">
                            <i class="bx bx-list-plus me-1"></i> Bulk Create Balances
                        </a>
                        <a href="{{ route('vacations.create') }}" class="btn btn-outline-primary">
                            <i class="bx bx-calendar me-1"></i> Create Vacation Request
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Balance Recommendations</h5>
                </div>
                <div class="card-body">
                    @if($balance->balance > 20)
                        <div class="alert alert-success">
                            <i class="mdi mdi-check-circle me-2"></i>
                            <strong>Excellent!</strong> This employee has a healthy vacation balance.
                        </div>
                    @elseif($balance->balance > 10)
                        <div class="alert alert-info">
                            <i class="mdi mdi-information me-2"></i>
                            <strong>Good.</strong> Balance is adequate for vacation planning.
                        </div>
                    @elseif($balance->balance > 5)
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert me-2"></i>
                            <strong>Low Balance.</strong> Consider adding more vacation days.
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <i class="mdi mdi-alert-circle me-2"></i>
                            <strong>Critical!</strong> This balance is very low and needs attention.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection