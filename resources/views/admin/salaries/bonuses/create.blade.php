@extends('layouts.master')

@section('title', 'Create Bonus')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Bonus for {{ $salary->employee->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('salaries.show', $salary) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Salary
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('salaries.bonuses.store', $salary) }}" method="POST">
                    @csrf
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
                            <strong>Status:</strong> 
                            <span class="badge bg-{{ $salary->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($salary->status) }}
                            </span>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="amount" class="form-label">Bonus Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="amount" id="amount" 
                                               class="form-control @error('amount') is-invalid @enderror" 
                                               value="{{ old('amount') }}" required>
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
                                           value="{{ old('bonus_date', date('Y-m-d')) }}" required>
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
                                        <option value="">Select Bonus Type</option>
                                        <option value="performance" {{ old('type') == 'performance' ? 'selected' : '' }}>Performance</option>
                                        <option value="annual" {{ old('type') == 'annual' ? 'selected' : '' }}>Annual</option>
                                        <option value="project" {{ old('type') == 'project' ? 'selected' : '' }}>Project</option>
                                        <option value="attendance" {{ old('type') == 'attendance' ? 'selected' : '' }}>Attendance</option>
                                        <option value="special" {{ old('type') == 'special' ? 'selected' : '' }}>Special</option>
                                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
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
                                        <option value="approved" {{ old('status', 'approved') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
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
                                      placeholder="Reason for this bonus...">{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bonus Type Descriptions -->
                        <div class="card bg-light">
                            <div class="card-header">
                                <h5>Bonus Type Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li><strong>Performance:</strong> Merit-based bonus for excellent work</li>
                                            <li><strong>Annual:</strong> Yearly bonus or 13th month salary</li>
                                            <li><strong>Project:</strong> Bonus for successful project completion</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li><strong>Attendance:</strong> Perfect attendance reward</li>
                                            <li><strong>Special:</strong> One-time special recognition</li>
                                            <li><strong>Other:</strong> Other types of bonuses</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Salary Impact Preview -->
                        <div class="card bg-success text-white mt-3">
                            <div class="card-header">
                                <h5>Salary Impact Preview</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Current Fixed Salary:</strong><br>
                                        <span class="h5">${{ number_format($salary->fixed_salary, 2) }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Bonus Amount:</strong><br>
                                        <span id="bonus-preview" class="h5">$0.00</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Total with Bonus:</strong><br>
                                        <span id="total-preview" class="h5">${{ number_format($salary->fixed_salary, 2) }}</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <strong>Percentage Increase:</strong>
                                    <span id="percentage-increase" class="h6">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-gift"></i> Create Bonus
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