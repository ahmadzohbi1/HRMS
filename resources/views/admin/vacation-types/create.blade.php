@extends('layouts.master')

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Vacation Types @endslot
        @slot('title') Create Vacation Type @endslot
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

                    <form action="{{ route('vacation-types.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Vacation Type Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ old('name') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="is_paid" class="form-label">Payment Type</label>
                                    <select class="form-select" id="is_paid" name="is_paid" required>
                                        <option value="">Select Payment Type</option>
                                        <option value="1" {{ old('is_paid') == '1' ? 'selected' : '' }}>Paid</option>
                                        <option value="0" {{ old('is_paid') == '0' ? 'selected' : '' }}>Unpaid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_days_allowed" class="form-label">Maximum Days Allowed</label>
                                    <input type="number" class="form-control" id="max_days_allowed" name="max_days_allowed" 
                                           value="{{ old('max_days_allowed') }}" min="1" placeholder="Leave empty for unlimited">
                                    <div class="form-text">This will be the default balance for employees</div>
                                </div>
                            </div>
                        </div>

                        <!-- Auto Balance Assignment -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bx bx-calendar-check me-2 text-primary"></i>
                                    Auto Balance Assignment
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="auto_assign_balance" 
                                                   name="auto_assign_balance" value="1" {{ old('auto_assign_balance') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="auto_assign_balance">
                                                <strong>Auto-assign balance to all employees</strong>
                                            </label>
                                        </div>
                                        <div class="form-text">
                                            When checked, all employees will automatically get the max days as their balance
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

                        <!-- Preview Section -->
                        <div id="preview-section" class="alert alert-info" style="display: none;">
                            <h6 class="alert-heading">Preview</h6>
                            <p class="mb-0" id="preview-text"></p>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('vacation-types.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Create Vacation Type</button>
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
            const autoAssignCheckbox = document.getElementById('auto_assign_balance');
            const targetYearSelect = document.getElementById('target_year');
            const previewSection = document.getElementById('preview-section');
            const previewText = document.getElementById('preview-text');

            function updatePreview() {
                const maxDays = maxDaysInput.value;
                const autoAssign = autoAssignCheckbox.checked;
                const targetYear = targetYearSelect.value;

                if (maxDays && autoAssign) {
                    previewText.textContent = `All employees will receive ${maxDays} days of vacation balance for ${targetYear} when this vacation type is created.`;
                    previewSection.style.display = 'block';
                } else {
                    previewSection.style.display = 'none';
                }
            }

            maxDaysInput.addEventListener('input', updatePreview);
            autoAssignCheckbox.addEventListener('change', updatePreview);
            targetYearSelect.addEventListener('change', updatePreview);
        });
    </script>
@endsection