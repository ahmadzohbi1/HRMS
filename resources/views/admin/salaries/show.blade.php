@extends('layouts.master')

@section('title', 'Salary Details')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Salary Information Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Salary Details</h3>
                    <div>
                        <a href="{{ route('salaries.edit', $salary) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit Salary
                        </a>
                        <a href="{{ route('salaries.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <h4>Employee Information</h4>
                                    <p><strong>Name:</strong> {{ $salary->employee->name }}</p>
                                    <p><strong>Email:</strong> {{ $salary->employee->email }}</p>
                                    <p><strong>Phone:</strong> {{ $salary->employee->phone ?? 'N/A' }}</p>
                                    @if($salary->employee->position)
                                        <p><strong>Position:</strong> {{ $salary->employee->position->name ?? 'N/A' }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <h4>Salary Information</h4>
                                    <p><strong>Fixed Salary:</strong> <span class="text-success">${{ number_format($salary->fixed_salary, 2) }}</span></p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-{{ $salary->status === 'active' ? 'success' : ($salary->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($salary->status) }}
                                        </span>
                                    </p>
                                    <p><strong>Effective Date:</strong> {{ $salary->effective_date->format('M d, Y') }}</p>
                                    <p><strong>Created:</strong> {{ $salary->created_at->format('M d, Y') }}</p>
                                    @if($salary->notes)
                                        <p><strong>Notes:</strong> {{ $salary->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>${{ number_format($salary->fixed_salary, 2) }}</h3>
                    <p>Fixed Salary</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    @php
                        $totalBonuses = $salary->bonuses->whereIn('status', ['approved', 'paid'])->sum('amount');
                    @endphp
                    <h3>${{ number_format($totalBonuses, 2) }}</h3>
                    <p>Total Bonuses</p>
                </div>
                <div class="icon">
                    <i class="fas fa-gift"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    @php
                        $totalAdvances = $salary->advances->whereIn('status', ['approved', 'paid'])->sum('amount');
                    @endphp
                    <h3>${{ number_format($totalAdvances, 2) }}</h3>
                    <p>Total Advances</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            @php
                $netSalary = $salary->fixed_salary + $totalBonuses - $totalAdvances;
            @endphp
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>${{ number_format($netSalary, 2) }}</h3>
                    <p>Net Salary</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calculator"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Advances Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Advances</h4>
                    <a href="{{ route('salaries.advances.create', $salary) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Advance
                    </a>
                </div>
                
                <div class="card-body">
                    @if($salary->advances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Installments</th>
                                        <th>Remaining</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salary->advances as $advance)
                                        <tr>
                                            <td>${{ number_format($advance->amount, 2) }}</td>
                                            <td>{{ $advance->advance_date->format('M d, Y') }}</td>
                                            <td>{{ $advance->reason ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $advance->status === 'approved' ? 'success' : ($advance->status === 'pending' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($advance->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $advance->installments }}</td>
                                            <td>${{ number_format($advance->remaining_amount ?? $advance->amount, 2) }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('salaries.advances.edit', [$salary, $advance]) }}" 
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('salaries.advances.destroy', [$salary, $advance]) }}" 
                                                          method="POST" class="d-inline" id="delete-advance-{{ $advance->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                onclick="confirmDelete('Advance').then(result => { if(result) document.getElementById('delete-advance-{{ $advance->id }}').submit(); })">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-hand-holding-usd fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No advances found.</p>
                            <a href="{{ route('salaries.advances.create', $salary) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Advance
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bonuses Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Bonuses</h4>
                    <a href="{{ route('salaries.bonuses.create', $salary) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Add Bonus
                    </a>
                </div>
                
                <div class="card-body">
                    @if($salary->bonuses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salary->bonuses as $bonus)
                                        <tr>
                                            <td>${{ number_format($bonus->amount, 2) }}</td>
                                            <td>{{ $bonus->bonus_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ ucfirst($bonus->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $bonus->reason ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $bonus->status === 'approved' ? 'success' : ($bonus->status === 'pending' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($bonus->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('salaries.bonuses.edit', [$salary, $bonus]) }}" 
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('salaries.bonuses.destroy', [$salary, $bonus]) }}" 
                                                          method="POST" class="d-inline" id="delete-bonus-{{ $bonus->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                onclick="confirmDelete('Bonus').then(result => { if(result) document.getElementById('delete-bonus-{{ $bonus->id }}').submit(); })">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No bonuses found.</p>
                            <a href="{{ route('salaries.bonuses.create', $salary) }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Create First Bonus
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .small-box {
        border-radius: 0.25rem;
        position: relative;
        display: block;
        margin-bottom: 20px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    }
    .small-box > .inner {
        padding: 10px;
    }
    .small-box .icon {
        position: absolute;
        top: auto;
        bottom: 5px;
        right: 5px;
        z-index: 0;
        font-size: 70px;
        color: rgba(0,0,0,0.15);
    }
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
        color: #fff;
    }
    .small-box p {
        font-size: 1rem;
        color: #fff;
        margin: 0;
    }
    .info-box {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 1rem;
        height: 100%;
    }
    .info-box h4 {
        margin-bottom: 1rem;
        color: #495057;
        border-bottom: 2px solid #007bff;
        padding-bottom: 0.5rem;
    }
</style>
@endpush