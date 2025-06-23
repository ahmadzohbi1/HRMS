@extends('layouts.master')

@section('title', 'Edit Bonus')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Bonus - {{ $salary->employee->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('salaries.show', $salary) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Salary
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('salaries.bonuses.update', [$salary, $bonus]) }}" method="POST">
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

                        <!-- Employee Info -->
                        <div class="alert alert-success">
                            <h5><i class="fas fa-user"></i> Employee Information</h5>
                            <strong>Name:</strong> {{ $salary->employee->name }}<br>
                            <strong>Fixed Salary:</strong> ${{ number_format($salary->fixed_salary, 2) }}<br>
                            <strong>Bonus Created:</strong> {{ $bonus->created_at->format('M d, Y') }}
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="amount" class="form-label">Bonus Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="amount" id="amount" 
                                               class="form-control @error('amount') is-invalid @enderror" 
                                               value="{{ old('amount', $bonus->amount) }}" required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="bonus_date" class="form-label">Bonus Date *</label>
                                    <input type="date" name="bonus_date" id="bonus_date" 
                                           class="form-control @error('bonus_date') is-invalid @enderror" 
                                           value="{{ old('bonus_date', $bonus->bonus_date->format('Y-m-d')) }}" required>
                                    @error('bonus_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="type" class="form-label">Bonus Type *</label>
                                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="performance" {{ old('type', $bonus->type) == 'performance' ? 'selected' : '' }}>Performance</option>
                                        <option value="annual" {{ old('type', $bonus->type) == 'annual' ? 'selected' : '' }}>Annual</option>
                                        <option value="project" {{ old('type', $bonus->type) == 'project' ? 'selected' : '' }}>Project</option>
                                        <option value="attendance" {{ old('type', $bonus->type) == 'attendance' ? 'selected' : '' }}>Attendance</option>
                                        <option value="special" {{ old('type', $bonus->type) == 'special' ? 'selected' : '' }}>Special</option>
                                        <option value="other" {{ old('type', $bonus->type) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ old('status', $bonus->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ old('status', $bonus->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ old('status', $bonus->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="paid" {{ old('status', $bonus->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="reason" class="form-label">Reason</label>
                            <textarea name="reason" id="reason" rows="3" 
                                      class="form-control @error('reason') is-invalid @enderror" 
                                      placeholder="Reason for this bonus...">{{ old('reason', $bonus->reason) }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Bonus Information -->
                        <div class="card bg-light">
                            <div class="card-header">
                                <h5>Bonus Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Original Amount:</strong><br>
                                        <span class="text-success">${{ number_format($bonus->amount, 2) }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Current Status:</strong><br>
                                        <span class="badge bg-{{ $bonus->status === 'approved' ? 'success' : ($bonus->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($bonus->status) }}
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Type:</strong><br>
                                        <span class="badge bg-info">{{ ucfirst($bonus->type) }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Created:</strong><br>
                                        <span class="text-muted">{{ $bonus->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Updated Salary Impact Preview -->
                        <div class="card bg-success text-white mt-3">
                            <div class="card-header">
                                <h5>Updated Salary Impact</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Fixed Salary:</strong><br>
                                        <span class="h5">${{ number_format($salary->fixed_salary, 2) }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Updated Bonus:</strong><br>
                                        <span id="bonus-preview" class="h5">${{ number_format($bonus->amount, 2) }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Total with Bonus:</strong><br>
                                        <span id="total-preview" class="h5">${{ number_format($salary->fixed_salary + $bonus->amount, 2) }}</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <strong>Percentage Increase:</strong>
                                    <span id="percentage-increase" class="h6">{{ number_format(($bonus->amount / $salary->fixed_salary * 100), 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update Bonus
                        </button>
                        <a href="{{ route('salaries.show', $salary) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const fixedSalary = {{ $salary->fixed_salary }};
    
    function updatePreview() {
        const bonusAmount = parseFloat(amountInput.value) || 0;
        const totalSalary = fixedSalary + bonusAmount;
        const percentageIncrease = bonusAmount > 0 ? (bonusAmount / fixedSalary * 100) : 0;
        
        document.getElementById('bonus-preview').textContent = '$' + bonusAmount.toFixed(2);
        document.getElementById('total-preview').textContent = '$' + totalSalary.toFixed(2);
        document.getElementById('percentage-increase').textContent = percentageIncrease.toFixed(1) + '%';
    }
    
    amountInput.addEventListener('input', updatePreview);
});
</script>
@endpush