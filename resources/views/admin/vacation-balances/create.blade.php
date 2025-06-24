@extends('layouts.master')

@section('title') Create Vacation Balance @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Vacation Balances @endslot
        @slot('title') Create New Balance @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-8 col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Create Vacation Balance</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-block-helper me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('vacation-balances.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                                    <select class="form-select" id="employee_id" name="employee_id" required>
                                        <option value="">Choose an employee...</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                    {{ old('employee_id') == $employee->id ? 'selected' : '' }}
                                                    data-email="{{ $employee->email }}"
                                                    data-department="{{ $employee->departments->first()->name ?? 'No Department' }}">
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select an employee.</div>
                                    <div id="employee-info" class="form-text"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="vacation_type_id" class="form-label">Vacation Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="vacation_type_id" name="vacation_type_id" required>
                                        <option value="">Choose a vacation type...</option>
                                        @foreach($vacationTypes as $type)
                                            <option value="{{ $type->id }}" 
                                                    {{ old('vacation_type_id') == $type->id ? 'selected' : '' }}
                                                    data-paid="{{ $type->is_paid ? 'Yes' : 'No' }}"
                                                    data-max-days="{{ $type->max_days_allowed ?? 'Unlimited' }}">
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a vacation type.</div>
                                    <div id="vacation-type-info" class="form-text"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                                    <select class="form-select" id="year" name="year" required>
                                        <option value="">Choose a year...</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" 
                                                    {{ (old('year') ?? $balance->year) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a year.</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="balance" class="form-label">Balance (Days) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="balance" name="balance" 
                                               value="{{ old('balance') ?? $balance->balance }}" min="0" max="365" required>
                                        <span class="input-group-text">days</span>
                                    </div>
                                    <div class="invalid-feedback">Please enter a valid balance (0-365 days).</div>
                                    <div class="form-text">Current balance will be updated to this value.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Balance Change Preview -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">Balance Change Preview</h6>
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center">
                                        <p class="text-muted mb-1">Current Balance</p>
                                        <h4 class="text-info mb-0">{{ $balance->balance }} days</h4>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <i class="bx bx-right-arrow-alt font-size-24 text-muted"></i>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <p class="text-muted mb-1">New Balance</p>
                                        <h4 id="new-balance-preview" class="text-success mb-0">{{ $balance->balance }} days</h4>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <span id="balance-change" class="badge badge-soft-info">No change</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('vacation-balances.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-check me-1"></i> Update Balance
                            </button>
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
            const employeeSelect = document.getElementById('employee_id');
            const vacationTypeSelect = document.getElementById('vacation_type_id');
            const balanceInput = document.getElementById('balance');
            const originalBalance = {{ $balance->balance }};

            // Update employee info
            employeeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const employeeInfo = document.getElementById('employee-info');
                
                if (selectedOption.value) {
                    const email = selectedOption.getAttribute('data-email');
                    const department = selectedOption.getAttribute('data-department');
                    employeeInfo.innerHTML = `<strong>Email:</strong> ${email} | <strong>Department:</strong> ${department}`;
                } else {
                    employeeInfo.innerHTML = '';
                }
            });

            // Update vacation type info
            vacationTypeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const vacationTypeInfo = document.getElementById('vacation-type-info');
                
                if (selectedOption.value) {
                    const isPaid = selectedOption.getAttribute('data-paid');
                    const maxDays = selectedOption.getAttribute('data-max-days');
                    vacationTypeInfo.innerHTML = `<strong>Type:</strong> ${isPaid === 'Yes' ? 'Paid' : 'Unpaid'} | <strong>Max Days:</strong> ${maxDays}`;
                } else {
                    vacationTypeInfo.innerHTML = '';
                }
            });

            // Update balance change preview
            balanceInput.addEventListener('input', function() {
                const newBalance = parseInt(this.value) || 0;
                const change = newBalance - originalBalance;
                
                document.getElementById('new-balance-preview').textContent = newBalance + ' days';
                
                const changeElement = document.getElementById('balance-change');
                if (change > 0) {
                    changeElement.textContent = `+${change} days increase`;
                    changeElement.className = 'badge badge-soft-success';
                } else if (change < 0) {
                    changeElement.textContent = `${change} days decrease`;
                    changeElement.className = 'badge badge-soft-danger';
                } else {
                    changeElement.textContent = 'No change';
                    changeElement.className = 'badge badge-soft-info';
                }
            });

            // Initialize info displays
            employeeSelect.dispatchEvent(new Event('change'));
            vacationTypeSelect.dispatchEvent(new Event('change'));

            // Form validation
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    </script>
@endsection