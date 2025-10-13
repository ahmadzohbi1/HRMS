@extends('layouts.master')

@section('title', 'Edit Salary')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Salary - {{ $salary->employee->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('salaries.show', $salary) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <a href="{{ route('salaries.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('salaries.update', $salary) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="employee_id" class="form-label">Employee *</label>
                                    <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ (old('employee_id', $salary->employee_id) == $employee->id) ? 'selected' : '' }}>
                                                {{ $employee->name }} - {{ $employee->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="fixed_salary" class="form-label">Fixed Salary *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="fixed_salary" id="fixed_salary" 
                                               class="form-control @error('fixed_salary') is-invalid @enderror" 
                                               value="{{ old('fixed_salary', $salary->fixed_salary) }}" required>
                                    </div>
                                    @error('fixed_salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="effective_month" class="form-label">Effective Month & Year *</label>
                                    <input type="month" name="effective_month" id="effective_month" 
                                           class="form-control @error('effective_date') is-invalid @enderror" 
                                           value="{{ old('effective_month', $salary->effective_date->format('Y-m')) }}" required>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Changing to a future month will create a new salary version (history preserved)
                                    </small>
                                    @error('effective_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', $salary->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $salary->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="pending" {{ old('status', $salary->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="form-control @error('notes') is-invalid @enderror" 
                                      placeholder="Additional notes about this salary...">{{ old('notes', $salary->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($salary->status !== 'active')
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Warning:</strong> Setting this salary as "Active" will automatically deactivate any other active salary for the selected employee.
                            </div>
                        @endif

                        <!-- Current Summary -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5>Current Salary Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Total Advances:</strong><br>
                                                <span class="text-danger">${{ number_format($salary->advances->where('status', 'approved')->sum('amount'), 2) }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Total Bonuses:</strong><br>
                                                <span class="text-success">${{ number_format($salary->bonuses->where('status', 'approved')->sum('amount'), 2) }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Net Calculation:</strong><br>
                                                @php
                                                    $net = $salary->fixed_salary + $salary->bonuses->where('status', 'approved')->sum('amount') - $salary->advances->where('status', 'approved')->sum('amount');
                                                @endphp
                                                <span class="fw-bold {{ $net >= 0 ? 'text-success' : 'text-danger' }}">${{ number_format($net, 2) }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Created:</strong><br>
                                                {{ $salary->created_at->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Salary
                        </button>
                        <a href="{{ route('salaries.show', $salary) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <a href="{{ route('salaries.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection