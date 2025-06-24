{{-- resources/views/vacations/edit.blade.php --}}
@extends('layouts.master')

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Vacations @endslot
        @slot('title') Edit Vacation Request @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Current Status Display -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="alert-heading">Current Vacation Details</h6>
                                <p class="mb-1"><strong>Employee:</strong> {{ $vacation->employee->name ?? 'Unknown Employee' }}</p>
                                <p class="mb-1"><strong>Vacation Type:</strong> {{ $vacation->vacationType->name ?? 'Unknown Type' }}</p>
                                <p class="mb-0"><strong>Duration:</strong> {{ $vacation->duration_in_days }} days</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Current Status:</strong> 
                                    <span class="badge badge-soft-{{ $vacation->status == 'approved' ? 'success' : ($vacation->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($vacation->status) }}
                                    </span>
                                </p>
                                <p class="mb-1"><strong>Requested:</strong> {{ $vacation->created_at->format('M d, Y') }}</p>
                                <p class="mb-0"><strong>Last Updated:</strong> {{ $vacation->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('vacations.update', $vacation->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Employee</label>
                                    <select class="form-select" id="employee_id" name="employee_id" required>
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                {{ (old('employee_id') ?? $vacation->employee_id) == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vacation_type_id" class="form-label">Vacation Type</label>
                                    <select class="form-select" id="vacation_type_id" name="vacation_type_id" required>
                                        <option value="">Select Vacation Type</option>
                                        @foreach($vacationTypes as $type)
                                            <option value="{{ $type->id }}" 
                                                {{ (old('vacation_type_id') ?? $vacation->vacation_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }} ({{ $type->is_paid ? 'Paid' : 'Unpaid' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="{{ old('start_date') ?? $vacation->start_date->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="{{ old('end_date') ?? $vacation->end_date->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pending" 
                                            {{ (old('status') ?? $vacation->status) == 'pending' ? 'selected' : '' }}>
                                            <i class="bx bx-time"></i> Pending Review
                                        </option>
                                        <option value="approved" 
                                            {{ (old('status') ?? $vacation->status) == 'approved' ? 'selected' : '' }}>
                                            <i class="bx bx-check"></i> Approved
                                        </option>
                                        <option value="rejected" 
                                            {{ (old('status') ?? $vacation->status) == 'rejected' ? 'selected' : '' }}>
                                            <i class="bx bx-x"></i> Rejected
                                        </option>
                                    </select>
                                    <div class="form-text">
                                        <small class="text-muted">
                                            <i class="bx bx-info-circle me-1"></i>
                                            Changing status will affect employee's vacation balance
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Change Preview -->
                        <div id="status-change-preview" class="alert alert-warning" style="display: none;">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading">Status Change Impact</h6>
                                    <p class="mb-0" id="status-change-message"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Action Buttons -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">Quick Actions:</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-success" onclick="setStatus('approved')">
                                        <i class="bx bx-check me-1"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" onclick="setStatus('rejected')">
                                        <i class="bx bx-x me-1"></i> Reject
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" onclick="setStatus('pending')">
                                        <i class="bx bx-time me-1"></i> Set Pending
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('vacations.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i> Update Vacation Request
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const originalStatus = '{{ $vacation->status }}';
            const vacationDays = {{ $vacation->duration_in_days }};
            
            // Monitor status changes
            statusSelect.addEventListener('change', function() {
                showStatusChangePreview(this.value, originalStatus, vacationDays);
            });

            function showStatusChangePreview(newStatus, oldStatus, days) {
                const previewDiv = document.getElementById('status-change-preview');
                const messageDiv = document.getElementById('status-change-message');
                
                if (newStatus === oldStatus) {
                    previewDiv.style.display = 'none';
                    return;
                }

                let message = '';
                let alertClass = 'alert-warning';

                if (oldStatus === 'pending' && newStatus === 'approved') {
                    message = `Approving this vacation will deduct ${days} days from the employee's vacation balance.`;
                    alertClass = 'alert-success';
                } else if (oldStatus === 'pending' && newStatus === 'rejected') {
                    message = `Rejecting this vacation will not affect the employee's vacation balance.`;
                    alertClass = 'alert-danger';
                } else if (oldStatus === 'approved' && newStatus === 'rejected') {
                    message = `Changing from approved to rejected will restore ${days} days to the employee's vacation balance.`;
                    alertClass = 'alert-warning';
                } else if (oldStatus === 'approved' && newStatus === 'pending') {
                    message = `Changing from approved to pending will restore ${days} days to the employee's vacation balance.`;
                    alertClass = 'alert-info';
                } else if (oldStatus === 'rejected' && newStatus === 'approved') {
                    message = `Changing from rejected to approved will deduct ${days} days from the employee's vacation balance.`;
                    alertClass = 'alert-success';
                } else if (oldStatus === 'rejected' && newStatus === 'pending') {
                    message = `Changing from rejected to pending will not affect the employee's vacation balance.`;
                    alertClass = 'alert-info';
                }

                if (message) {
                    messageDiv.textContent = message;
                    previewDiv.className = `alert ${alertClass}`;
                    previewDiv.style.display = 'block';
                } else {
                    previewDiv.style.display = 'none';
                }
            }
        });

        // Quick action functions
        function setStatus(status) {
            const statusSelect = document.getElementById('status');
            statusSelect.value = status;
            statusSelect.dispatchEvent(new Event('change'));
            
            // Visual feedback
            const buttons = document.querySelectorAll('.btn-group .btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            if (status === 'approved') {
                buttons[0].classList.add('active');
            } else if (status === 'rejected') {
                buttons[1].classList.add('active');
            } else {
                buttons[2].classList.add('active');
            }
        }
    </script>
@endsection