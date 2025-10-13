{{-- resources/views/vacation-balances/edit.blade.php --}}
@extends('layouts.master')

@section('title') Edit Vacation Balance @endsection

@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') 
            <a href="{{ route('vacation-balances.index') }}">Vacation Balances</a>
        @endslot
        @slot('title') Edit Balance #{{ $balance->id }} @endslot
    @endcomponent

    <div class="row justify-content-center">
        <div class="col-xl-10">
            <!-- Current Balance Overview -->
            <div class="card balance-comparison mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="employee-avatar mx-auto">
                                {{ substr($balance->employee->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-3">
                                    <h6 class="text-white-50 mb-1">Employee</h6>
                                    <h5 class="text-white mb-0">{{ $balance->employee->name }}</h5>
                                    <small class="text-white-50">{{ $balance->employee->email ?? 'No email' }}</small>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="text-white-50 mb-1">Vacation Type</h6>
                                    <h5 class="text-white mb-0">{{ $balance->vacationType->name }}</h5>
                                    <small class="badge badge-soft-light">
                                        {{ $balance->vacationType->is_paid ? 'Paid' : 'Unpaid' }}
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="text-white-50 mb-1">Year</h6>
                                    <h5 class="text-white mb-0">{{ $balance->year }}</h5>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="text-white-50 mb-1">Current Balance</h6>
                                    <h5 class="text-white mb-0">{{ $balance->balance }} days</h5>
                                    <small class="text-white-50">Last updated {{ $balance->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="card balance-card">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-0">
                                <i class="bx bx-edit-alt text-primary me-2"></i>
                                Edit Vacation Balance
                            </h4>
                            <p class="text-muted mb-0">Update the vacation balance details below</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="info-badge">ID: {{ $balance->id }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="mdi mdi-alert-circle me-2 font-size-20"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading">Please fix the following errors:</h5>
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('vacation-balances.update', $balance->id) }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="employee_id" class="form-label fw-semibold">
                                        <i class="bx bx-user me-1 text-primary"></i>
                                        Employee <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg" id="employee_id" name="employee_id" required>
                                        <option value="">Choose an employee...</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                    {{ (old('employee_id') ?? $balance->employee_id) == $employee->id ? 'selected' : '' }}
                                                    data-email="{{ $employee->email }}"
                                                    data-department="{{ $employee->departments->first()->name ?? 'No Department' }}">
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        <i class="bx bx-error-circle me-1"></i>
                                        Please select an employee.
                                    </div>
                                    <div id="employee-info" class="form-text mt-2"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="vacation_type_id" class="form-label fw-semibold">
                                        <i class="bx bx-calendar-alt me-1 text-primary"></i>
                                        Vacation Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg" id="vacation_type_id" name="vacation_type_id" required>
                                        <option value="">Choose a vacation type...</option>
                                        @foreach($vacationTypes as $type)
                                            <option value="{{ $type->id }}" 
                                                    {{ (old('vacation_type_id') ?? $balance->vacation_type_id) == $type->id ? 'selected' : '' }}
                                                    data-paid="{{ $type->is_paid ? 'Paid' : 'Unpaid' }}"
                                                    data-max-days="{{ $type->max_days_allowed ?? 'Unlimited' }}">
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        <i class="bx bx-error-circle me-1"></i>
                                        Please select a vacation type.
                                    </div>
                                    <div id="vacation-type-info" class="form-text mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="year" class="form-label fw-semibold">
                                        <i class="bx bx-time me-1 text-primary"></i>
                                        Year <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg" id="year" name="year" required>
                                        <option value="">Choose a year...</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" 
                                                    {{ (old('year') ?? $balance->year) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        <i class="bx bx-error-circle me-1"></i>
                                        Please select a year.
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="balance" class="form-label fw-semibold">
                                        <i class="bx bx-calculator me-1 text-primary"></i>
                                        Balance (Days) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light">
                                            <i class="bx bx-calendar-check text-primary"></i>
                                        </span>
                                        <input type="number" class="form-control" id="balance" name="balance" 
                                               value="{{ old('balance') ?? $balance->balance }}" min="0" max="365" required>
                                        <span class="input-group-text bg-light text-muted">days</span>
                                    </div>
                                    <div class="invalid-feedback">
                                        <i class="bx bx-error-circle me-1"></i>
                                        Please enter a valid balance (0-365 days).
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="bx bx-info-circle me-1 text-info"></i>
                                        Enter the new balance amount to replace the current balance.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Balance Change Preview -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="bx bx-trending-up text-info me-2"></i>
                                    Balance Change Preview
                                </h5>
                                <div class="row align-items-center text-center">
                                    <div class="col-md-4">
                                        <div class="p-3">
                                            <h6 class="text-muted mb-2">Current Balance</h6>
                                            <div class="change-indicator text-info">{{ $balance->balance }}</div>
                                            <small class="text-muted">days</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3">
                                            <i class="bx bx-right-arrow-alt font-size-36 text-muted"></i>
                                            <div class="mt-2">
                                                <span id="balance-change-badge" class="badge badge-soft-info px-3 py-2">
                                                    No change
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3">
                                            <h6 class="text-muted mb-2">New Balance</h6>
                                            <div id="new-balance-display" class="change-indicator text-success">{{ $balance->balance }}</div>
                                            <small class="text-muted">days</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <div id="balance-recommendation" class="alert alert-info d-none">
                                        <i class="bx bx-bulb me-2"></i>
                                        <span id="recommendation-text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('vacation-balances.show', $balance->id) }}" class="btn btn-outline-info">
                                    <i class="bx bx-show me-1"></i> View Details
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('vacation-balances.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="bx bx-x me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bx bx-check me-1"></i> Update Balance
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bx bx-history text-warning me-2"></i>
                                Change History
                            </h6>
                            <div class="timeline-item">
                                <p class="mb-1">
                                    <strong>Created:</strong> {{ $balance->created_at->format('M d, Y g:i A') }}
                                </p>
                                <p class="mb-1">
                                    <strong>Last Updated:</strong> {{ $balance->updated_at->format('M d, Y g:i A') }}
                                </p>
                                <p class="mb-0 text-muted">
                                    <small>{{ $balance->updated_at->diffForHumans() }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bx bx-shield-check text-success me-2"></i>
                                Balance Guidelines
                            </h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-1">
                                    <i class="bx bx-check-circle text-success me-2"></i>
                                    <small>15+ days: Excellent balance</small>
                                </li>
                                <li class="mb-1">
                                    <i class="bx bx-info-circle text-info me-2"></i>
                                    <small>10-15 days: Good balance</small>
                                </li>
                                <li class="mb-1">
                                    <i class="bx bx-error-circle text-warning me-2"></i>
                                    <small>5-10 days: Low balance</small>
                                </li>
                                <li class="mb-0">
                                    <i class="bx bx-x-circle text-danger me-2"></i>
                                    <small>0-5 days: Critical balance</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const employeeSelect = document.getElementById('employee_id');
            const vacationTypeSelect = document.getElementById('vacation_type_id');
            const balanceInput = document.getElementById('balance');
            const originalBalance = {{ $balance->balance }};

            // Initialize displays
            updateEmployeeInfo();
            updateVacationTypeInfo();
            updateBalancePreview();

            // Event listeners
            employeeSelect.addEventListener('change', updateEmployeeInfo);
            vacationTypeSelect.addEventListener('change', updateVacationTypeInfo);
            balanceInput.addEventListener('input', updateBalancePreview);

            function updateEmployeeInfo() {
                const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
                const employeeInfo = document.getElementById('employee-info');
                
                if (selectedOption.value && selectedOption.dataset.email) {
                    const email = selectedOption.dataset.email;
                    const department = selectedOption.dataset.department;
                    employeeInfo.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="bx bx-envelope me-2 text-primary"></i>
                            <strong>Email:</strong> <span class="ms-1">${email}</span>
                            <span class="mx-2">|</span>
                            <i class="bx bx-buildings me-2 text-primary"></i>
                            <strong>Department:</strong> <span class="ms-1">${department}</span>
                        </div>
                    `;
                } else {
                    employeeInfo.innerHTML = '';
                }
            }

            function updateVacationTypeInfo() {
                const selectedOption = vacationTypeSelect.options[vacationTypeSelect.selectedIndex];
                const vacationTypeInfo = document.getElementById('vacation-type-info');
                
                if (selectedOption.value && selectedOption.dataset.paid) {
                    const isPaid = selectedOption.dataset.paid;
                    const maxDays = selectedOption.dataset.maxDays;
                    const paidBadge = isPaid === 'Paid' ? 'success' : 'secondary';
                    vacationTypeInfo.innerHTML = `
                        <div class="d-flex align-items-center">
                            <span class="badge badge-soft-${paidBadge} me-2">${isPaid}</span>
                            <i class="bx bx-time me-2 text-primary"></i>
                            <strong>Max Days:</strong> <span class="ms-1">${maxDays}</span>
                        </div>
                    `;
                } else {
                    vacationTypeInfo.innerHTML = '';
                }
            }

            function updateBalancePreview() {
                const newBalance = parseInt(balanceInput.value) || 0;
                const change = newBalance - originalBalance;
                
                // Update display
                document.getElementById('new-balance-display').textContent = newBalance;
                
                const changeBadge = document.getElementById('balance-change-badge');
                const recommendationDiv = document.getElementById('balance-recommendation');
                const recommendationText = document.getElementById('recommendation-text');
                
                // Update change indicator
                if (change > 0) {
                    changeBadge.textContent = `+${change} days increase`;
                    changeBadge.className = 'badge badge-soft-success px-3 py-2';
                } else if (change < 0) {
                    changeBadge.textContent = `${change} days decrease`;
                    changeBadge.className = 'badge badge-soft-danger px-3 py-2';
                } else {
                    changeBadge.textContent = 'No change';
                    changeBadge.className = 'badge badge-soft-info px-3 py-2';
                }

                // Update recommendation
                let recommendation = '';
                let alertClass = 'alert-info';
                
                if (newBalance > 20) {
                    recommendation = 'Excellent! This balance provides great flexibility for vacation planning.';
                    alertClass = 'alert-success';
                } else if (newBalance > 15) {
                    recommendation = 'Good balance. Employee has adequate vacation days available.';
                    alertClass = 'alert-info';
                } else if (newBalance > 10) {
                    recommendation = 'Moderate balance. Consider monitoring usage patterns.';
                    alertClass = 'alert-warning';
                } else if (newBalance > 5) {
                    recommendation = 'Low balance. May need attention or top-up soon.';
                    alertClass = 'alert-warning';
                } else if (newBalance >= 0) {
                    recommendation = 'Critical balance! Immediate attention required.';
                    alertClass = 'alert-danger';
                }

                if (recommendation) {
                    recommendationText.textContent = recommendation;
                    recommendationDiv.className = `alert ${alertClass}`;
                    recommendationDiv.classList.remove('d-none');
                } else {
                    recommendationDiv.classList.add('d-none');
                }
            }

            // Form validation
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });

            // Add loading state to submit button
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="bx bx-loader bx-spin me-1"></i> Updating...';
                submitBtn.disabled = true;
            });
        });
    </script>
@endsection