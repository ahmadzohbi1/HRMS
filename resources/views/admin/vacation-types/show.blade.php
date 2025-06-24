@extends('layouts.master')

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Vacation Types @endslot
        @slot('title') Vacation Type Details @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">{{ $vacationType->name }}</h4>
                        <span class="badge badge-soft-{{ $vacationType->is_paid ? 'success' : 'secondary' }} fs-6">
                            {{ $vacationType->is_paid ? 'Paid' : 'Unpaid' }}
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Maximum Days:</label>
                                <p class="form-control-plaintext">
                                    {{ $vacationType->isUnlimited() ? 'Unlimited' : $vacationType->max_days_allowed . ' days' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Total Vacation Requests:</label>
                                <p class="form-control-plaintext">{{ $vacationType->vacations_count }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Employee Balances:</label>
                                <p class="form-control-plaintext">{{ $vacationType->employee_vacation_balances_count }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created:</label>
                                <p class="form-control-plaintext">{{ $vacationType->created_at->format('F d, Y g:i A') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Updated:</label>
                                <p class="form-control-plaintext">{{ $vacationType->updated_at->format('F d, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('vacation-types.index') }}" class="btn btn-secondary me-2">Back to List</a>
                                <a href="{{ route('vacation-types.edit', $vacationType->id) }}" class="btn btn-primary">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection