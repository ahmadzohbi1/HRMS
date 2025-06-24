@extends('layouts.master')

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Vacation Types @endslot
        @slot('title') Edit Vacation Type @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8">
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

                    <!-- Current Info -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Current Settings</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Name:</strong> {{ $vacationType->name }}</p>
                                <p class="mb-0"><strong>Type:</strong> {{ $vacationType->is_paid ? 'Paid' : 'Unpaid' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Max Days:</strong> {{ $vacationType->max_days_allowed ?? 'Unlimited' }}</p>
                                <p class="mb-0"><strong>Employees with Balance:</strong> {{ $vacationType->employeeVacationBalances()->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('vacation-types.update', $vacationType->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Vacation Type Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ old('name') ?? $vacationType->name }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="is_paid" class="form-label">Payment Type</label>
                                    <select class="form-select" id="is_paid" name="is_paid" required>
                                        <option value="">Select Payment Type</option>
                                        <option value="1" {{ (old('is_paid') ?? $vacationType->is_paid) == '1' ? 'selected' : '' }}>Paid</option>
                                        <option value="0" {{ (old('is_paid') ?? $vacationType->is_paid) == '0' ? 'selected' : '' }}>Unpaid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_days_allowed" class="form-label">Maximum Days Allowed</label>
                                    <input type="number" class="form-control" id="max_days_allowed" name="max_days_allowed" 
                                           value="{{ old('max_days_allowed') ?? $vacationType->max_days_allowed }}" min="1" placeholder="Leave empty for unlimited">
                                    <div class="form-text">Changes here can update existing employee balances</div>
                                </div>
                            </div>
                        </div>

                        <!-- Balance Update Options -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bx bx-refresh me-2 text-warning"></i>
                                    Update Employee Balances
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="update_existing_balances" 
                                                   name="update_existing_balances" value="1">
                                            <label class="form-check-label" for="update_existing_balances">
                                                <strong>Update existing employee balances</strong>
                                            </label>
                                        </div>
                                        <div class="form-text">
                                            Update all existing balances to match the new max days value
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="target_year" class="form-label">Target Year</label>
                                        <select class="form-select" id="target_year" name="target_year">
                                            <option value="{{ now()->year }}" selected>{{ now()->year }} (Current)</option>
                                            <option value="{{ now()->year + 1 }}">{{ now()->year + 1 }} (Next Year)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Warning Section -->
                        <div id="warning-section" class="alert alert-warning" style="display: none;">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading">Balance Update Warning</h6>
                                    <p class="mb-0" id="warning-text"></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('vacation-types.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Vacation Type</button>
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
            const maxDaysInput = document.getElementById('max_days_allowed');
            const updateCheckbox = document.getElementById('update_existing_balances');
            const targetYearSelect = document.getElementById('target_year');
            const warningSection = document.getElementById('warning-section');
            const warningText = document.getElementById('warning-text');
            const originalMaxDays = {{ $vacationType->max_days_allowed ?? 'null' }};

            function updateWarning() {
                const newMaxDays = parseInt(maxDaysInput.value);
                const updateBalances = updateCheckbox.checked;
                const targetYear = targetYearSelect.value;

                if (updateBalances && newMaxDays && newMaxDays !== originalMaxDays) {
                    if (newMaxDays > originalMaxDays) {
                        warningText.textContent = `This will INCREASE all existing employee balances from ${originalMaxDays || 0} to ${newMaxDays} days for ${targetYear}.`;
                    } else {
                        warningText.textContent = `This will DECREASE all existing employee balances from ${originalMaxDays || 0} to ${newMaxDays} days for ${targetYear}.`;
                    }
                    warningSection.style.display = 'block';
                } else {
                    warningSection.style.display = 'none';
                }
            }

            maxDaysInput.addEventListener('input', updateWarning);
            updateCheckbox.addEventListener('change', updateWarning);
            targetYearSelect.addEventListener('change', updateWarning);
        });
    </script>
@endsection