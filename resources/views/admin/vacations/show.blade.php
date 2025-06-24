@extends('layouts.master')

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Vacations @endslot
        @slot('title') Vacation Details @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">Vacation Request #{{ $vacation->id }}</h4>
                        <div>
                            <span class="badge badge-soft-{{ $vacation->status == 'approved' ? 'success' : ($vacation->status == 'rejected' ? 'danger' : 'warning') }} fs-6">
                                {{ ucfirst($vacation->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Employee:</label>
                                <p class="form-control-plaintext">{{ $vacation->employee->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Vacation Type:</label>
                                <p class="form-control-plaintext">
                                    {{ $vacation->vacationType->name }} 
                                    <span class="badge badge-soft-{{ $vacation->vacationType->is_paid ? 'success' : 'secondary' }}">
                                        {{ $vacation->vacationType->is_paid ? 'Paid' : 'Unpaid' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Start Date:</label>
                                <p class="form-control-plaintext">{{ $vacation->start_date->format('F d, Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">End Date:</label>
                                <p class="form-control-plaintext">{{ $vacation->end_date->format('F d, Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Duration:</label>
                                <p class="form-control-plaintext">{{ $vacation->duration_in_days }} days</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Request Date:</label>
                                <p class="form-control-plaintext">{{ $vacation->created_at->format('F d, Y g:i A') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Updated:</label>
                                <p class="form-control-plaintext">{{ $vacation->updated_at->format('F d, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('vacations.index') }}" class="btn btn-secondary me-2">Back to List</a>
                                <a href="{{ route('vacations.edit', $vacation->id) }}" class="btn btn-primary">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection