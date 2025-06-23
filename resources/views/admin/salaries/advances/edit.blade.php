@extends('layouts.master')

@section('title', 'Edit Advance')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Advance - {{ $salary->employee->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('salaries.show', $salary) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Salary
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('salaries.advances.update', [$salary, $advance]) }}" method="POST">
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
                        <div class="alert alert-info">
                            <h5><i class="fas fa-user"></i> Employee Information</h5>
                            <strong>Name:</strong> {{ $salary->employee->name }}<br>
                            <strong>Fixed Salary:</strong> ${{ number_format($salary->fixed_salary, 2) }}<br>
                            <strong>Advance Created:</strong> {{ $advance->created_at->format('M d, Y') }}
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="amount" class="form-label">Advance Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="amount" id="amount" 
                                               class="form-control @error('amount') is-invalid @enderror" 
                                               value="{{ old('amount', $advance->amount) }}" required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="advance_date" class="form-label">Advance Date *</label>
                                    <input type="date" name="advance_date" id="advance_date" 
                                           class="form-control @error('advance_date') is-invalid @enderror" 
                                           value="{{ old('advance_date', $advance->advance_date->format('Y-m-d')) }}" required>
                                    @error('advance_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ old('status', $advance->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ old('status', $advance->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ old('status', $advance->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="paid" {{ old('status', $advance->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="completed" {{ old('status', $advance->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="installments" class="form-label">Number of Installments *</label>
                                    <input type="number" name="installments" id="installments" 
                                           class="form-control @error('installments') is-invalid @enderror" 
                                           value="{{ old('installments', $advance->installments) }}" min="1" required>
                                    @error('installments')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">How many months to deduct this advance</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="deduction_start_date" class="form-label">Deduction Start Date</label>
                                    <input type="date" name="deduction_start_date" id="deduction_start_date" 
                                           class="form-control @error('deduction_start_date') is-invalid @enderror" 
                                           value="{{ old('deduction_start_date', $advance->deduction_start_date ? $advance->deduction_start_date->format('Y-m-d') : '') }}">
                                    @error('deduction_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">When to start deducting this advance from salary</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="remaining_amount" class="form-label">Remaining Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="remaining_amount" id="remaining_amount" 
                                               class="form-control @error('remaining_amount') is-invalid @enderror" 
                                               value="{{ old('remaining_amount', $advance->remaining_amount ?? $advance->amount) }}">
                                    </div>
                                    @error('remaining_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Amount still to be deducted</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="reason" class="form-label">Reason</label>
                            <textarea name="reason" id="reason" rows="3" 
                                      class="form-control @error('reason') is-invalid @enderror" 
                                      placeholder="Reason for this advance...">{{ old('reason', $advance->reason) }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Status -->
                        <div class="card bg-light">
                            <div class="card-header">
                                <h5>Advance Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Original Amount:</strong><br>
                                        <span class="text-primary">${{ number_format($advance->amount, 2) }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Monthly Deduction:</strong><br>
                                        <span id="monthly-deduction" class="text-info">${{ number_format($advance->amount / $advance->installments, 2) }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Deducted So Far:</strong><br>
                                        <span class="text-success">${{ number_format(($advance->amount - ($advance->remaining_amount ?? $advance->amount)), 2) }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Progress:</strong><br>
                                        @php
                                            $progress = (($advance->amount - ($advance->remaining_amount ?? $advance->amount)) / $advance->amount) * 100;
                                        @endphp
                                        <span class="text-warning">{{ number_format($progress, 1) }}%</span>
                                    </div>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Advance
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
    const installmentsInput = document.getElementById('installments');
    
    function updateCalculation() {
        const amount = parseFloat(amountInput.value) || 0;
        const installments = parseInt(installmentsInput.value) || 1;
        
        const monthlyDeduction = amount / installments;
        document.getElementById('monthly-deduction').textContent = ' + monthlyDeduction.toFixed(2);
    }
    
    amountInput.addEventListener('input', updateCalculation);
    installmentsInput.addEventListener('input', updateCalculation);
});
</script>
@endpush