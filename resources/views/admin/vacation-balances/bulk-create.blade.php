@extends('layouts.master')

@section('title') Bulk Create Vacation Balances @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Vacation Balances @endslot
        @slot('title') Bulk Create Balances @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Bulk Create Vacation Balances</h4>
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

                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-information me-2"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading">How Bulk Create Works</h5>
                                <p class="mb-0">Select multiple employees to create vacation balances with the same vacation type, year, and balance amount. The system will automatically skip employees who already have balances for the selected combination.</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('vacation-balances.bulk.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        
                        <!-- Bulk Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Bulk Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label for="vacation_type_id" class="form-label">Vacation Type <span class="text-danger">*</span></label>
                                            <select class="form-select" id="vacation_type_id" name="vacation_type_id" required>
                                                <option value="">Choose vacation type...</option>
                                                @foreach($vacationTypes as $type)
                                                    <option value="{{ $type->id }}" 
                                                            {{ old('vacation_type_id') == $type->id ? 'selected' : '' }}
                                                            data-paid="{{ $type->is_paid ? 'Paid' : 'Unpaid' }}"
                                                            data-max-days="{{ $type->max_days_allowed ?? 'Unlimited' }}">
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Please select a vacation type.</div>
                                            <div id="vacation-type-info" class="form-text"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                                            <select class="form-select" id="year" name="year" required>
                                                <option value="">Choose year...</option>
                                                @foreach($years as $year)
                                                    <option value="{{ $year }}" 
                                                            {{ (old('year') ?? now()->year) == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Please select a year.</div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label for="balance" class="form-label">Balance (Days) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="balance" name="balance" 
                                                       value="{{ old('balance') }}" min="0" max="365" required>
                                                <span class="input-group-text">days</span>
                                            </div>
                                            <div class="invalid-feedback">Please enter a valid balance.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Employee Selection -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Select Employees</h5>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" onclick="selectAll()">
                                            <i class="bx bx-check-square me-1"></i> Select All
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="deselectAll()">
                                            <i class="bx bx-square me-1"></i> Deselect All
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="employee-search" 
                                           placeholder="Search employees by name..." onkeyup="filterEmployees()">
                                </div>
                                <div class="employee-grid">
                                    <div class="row" id="employee-list">
                                        @foreach($employees as $employee)
                                            <div class="col-xl-3 col-lg-4 col-md-6 employee-item" data-name="{{ strtolower($employee->name) }}">
                                                <div class="card mb-3">
                                                    <div class="card-body p-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input employee-checkbox" type="checkbox" 
                                                                   name="employee_ids[]" value="{{ $employee->id }}" 
                                                                   id="employee_{{ $employee->id }}"
                                                                   {{ in_array($employee->id, old('employee_ids', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label w-100" for="employee_{{ $employee->id }}">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar-xs me-2">
                                                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                                            {{ substr($employee->name, 0, 1) }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="mb-1 font-size-14">{{ $employee->name }}</h6>
                                                                        <p class="text-muted font-size-12 mb-0">
                                                                            {{ $employee->departments->first()->name ?? 'No Dept' }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <span id="selected-count">0</span> of {{ $employees->count() }} employees selected
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div id="preview-section" class="card mb-4" style="display: none;">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Preview</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Vacation Type:</strong></p>
                                        <p id="preview-vacation-type" class="text-muted">-</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Year:</strong></p>
                                        <p id="preview-year" class="text-muted">-</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Balance:</strong></p>
                                        <p id="preview-balance" class="text-muted">-</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Selected Employees:</strong></p>
                                        <p id="preview-employees" class="text-muted">0</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('vacation-balances.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                                <i class="bx bx-check me-1"></i> Create Balances
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
            const vacationTypeSelect = document.getElementById('vacation_type_id');
            const yearSelect = document.getElementById('year');
            const balanceInput = document.getElementById('balance');
            const submitBtn = document.getElementById('submit-btn');
            const previewSection = document.getElementById('preview-section');

            // Update vacation type info
            vacationTypeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const vacationTypeInfo = document.getElementById('vacation-type-info');
                
                if (selectedOption.value) {
                    const isPaid = selectedOption.getAttribute('data-paid');
                    const maxDays = selectedOption.getAttribute('data-max-days');
                    vacationTypeInfo.innerHTML = `<strong>Type:</strong> ${isPaid} | <strong>Max Days:</strong> ${maxDays}`;
                } else {
                    vacationTypeInfo.innerHTML = '';
                }
                updatePreview();
            });

            yearSelect.addEventListener('change', updatePreview);
            balanceInput.addEventListener('input', updatePreview);

            function updatePreview() {
                const vacationType = vacationTypeSelect.options[vacationTypeSelect.selectedIndex]?.text || '-';
                const year = yearSelect.value || '-';
                const balance = balanceInput.value || '-';
                const selectedCount = document.querySelectorAll('.employee-checkbox:checked').length;

                document.getElementById('preview-vacation-type').textContent = vacationType;
                document.getElementById('preview-year').textContent = year;
                document.getElementById('preview-balance').textContent = balance + (balance !== '-' ? ' days' : '');
                document.getElementById('preview-employees').textContent = selectedCount;

                // Show preview if all required fields have values
                if (vacationTypeSelect.value && year !== '-' && balance !== '-' && selectedCount > 0) {
                    previewSection.style.display = 'block';
                    submitBtn.disabled = false;
                } else {
                    previewSection.style.display = 'none';
                    submitBtn.disabled = true;
                }
            }

            // Employee selection functions
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('employee-checkbox')) {
                    updateSelectedCount();
                    updatePreview();
                }
            });

            function updateSelectedCount() {
                const selectedCount = document.querySelectorAll('.employee-checkbox:checked').length;
                document.getElementById('selected-count').textContent = selectedCount;
            }

            // Initialize selected count
            updateSelectedCount();
        });

        function selectAll() {
            const visibleCheckboxes = document.querySelectorAll('.employee-item:not([style*="display: none"]) .employee-checkbox');
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateSelectedCount();
            updatePreview();
        }

        function deselectAll() {
            document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedCount();
            updatePreview();
        }

        function filterEmployees() {
            const searchTerm = document.getElementById('employee-search').value.toLowerCase();
            const employeeItems = document.querySelectorAll('.employee-item');
            
            employeeItems.forEach(item => {
                const employeeName = item.getAttribute('data-name');
                if (employeeName.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('.employee-checkbox:checked').length;
            document.getElementById('selected-count').textContent = selectedCount;
        }

        function updatePreview() {
            // This function is defined in the main DOMContentLoaded event listener
            // Re-trigger the preview update
            const event = new Event('change');
            document.getElementById('vacation_type_id').dispatchEvent(event);
        }
    </script>
@endsection<span class="text-danger">*</span></label>
                                    <select class="form-select" id="year" name="year" required>
                                        <option value="">Choose a year...</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" 
                                                    {{ (old('year') ?? now()->year) == $year ? 'selected' : '' }}>
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
                                               value="{{ old('balance') }}" min="0" max="365" required>
                                        <span class="input-group-text">days</span>
                                    </div>
                                    <div class="invalid-feedback">Please enter a valid balance (0-365 days).</div>
                                    <div class="form-text">Enter the number of vacation days available for this employee.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Card -->
                        <div id="preview-card" class="card bg-light mb-4" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title">Preview</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Employee:</strong> <span id="preview-employee">-</span></p>
                                        <p class="mb-1"><strong>Vacation Type:</strong> <span id="preview-vacation-type">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Year:</strong> <span id="preview-year">-</span></p>
                                        <p class="mb-1"><strong>Balance:</strong> <span id="preview-balance">-</span> days</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('vacation-balances.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-check me-1"></i> Create Balance
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
            const yearSelect = document.getElementById('year');
            const balanceInput = document.getElementById('balance');
            const previewCard = document.getElementById('preview-card');

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
                updatePreview();
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
                updatePreview();
            });

            // Update preview on input changes
            yearSelect.addEventListener('change', updatePreview);
            balanceInput.addEventListener('input', updatePreview);

            function updatePreview() {
                const employee = employeeSelect.options[employeeSelect.selectedIndex]?.text || '-';
                const vacationType = vacationTypeSelect.options[vacationTypeSelect.selectedIndex]?.text || '-';
                const year = yearSelect.value || '-';
                const balance = balanceInput.value || '-';

                document.getElementById('preview-employee').textContent = employee;
                document.getElementById('preview-vacation-type').textContent = vacationType;
                document.getElementById('preview-year').textContent = year;
                document.getElementById('preview-balance').textContent = balance;

                // Show preview card if all fields have values
                if (employeeSelect.value && vacationTypeSelect.value && year !== '-' && balance !== '-') {
                    previewCard.style.display = 'block';
                } else {
                    previewCard.style.display = 'none';
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
        });
    </script>
@endsection