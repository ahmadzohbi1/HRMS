@extends('layouts.master')

@section('title', 'Edit Deduction')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Deduction for {{ $salary->employee->name }}
                    </h3>
                </div>
                
                <form action="{{ route('salaries.deductions.update', [$salary, $deduction]) }}" method="POST">
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

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Salary:</strong> ${{ number_format($salary->fixed_salary, 2) }} 
                            (Effective: {{ $salary->effective_date->format('F Y') }})
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="amount" class="form-label">Deduction Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-danger text-white">-$</span>
                                        <input type="number" step="0.01" name="amount" id="amount" 
                                               class="form-control @error('amount') is-invalid @enderror" 
                                               value="{{ old('amount', $deduction->amount) }}" required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="deduction_date" class="form-label">Deduction Date *</label>
                                    <input type="date" name="deduction_date" id="deduction_date" 
                                           class="form-control @error('deduction_date') is-invalid @enderror" 
                                           value="{{ old('deduction_date', $deduction->deduction_date->format('Y-m-d')) }}" required>
                                    @error('deduction_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="reason" class="form-label">Reason *</label>
                                    <input type="text" name="reason" id="reason" 
                                           class="form-control @error('reason') is-invalid @enderror" 
                                           value="{{ old('reason', $deduction->reason) }}" 
                                           required>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="approved" {{ old('status', $deduction->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="pending" {{ old('status', $deduction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="deducted" {{ old('status', $deduction->status) == 'deducted' ? 'selected' : '' }}>Deducted</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Additional details...">{{ old('description', $deduction->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Deduction
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

