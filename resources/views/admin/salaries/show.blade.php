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
            <div class="small-box bg-warning">
                <div class="inner">
                    @php
                        $totalDeductions = $salary->deductions->whereIn('status', ['approved', 'deducted'])->sum('amount');
                    @endphp
                    <h3>${{ number_format($totalDeductions, 2) }}</h3>
                    <p>Total Deductions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-minus-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-12">
            @if(isset($breakdown))
            <div class="alert alert-primary">
                <div class="row text-center">
                    <div class="col">
                        <h5 class="mb-0">NET SALARY for {{ $currentDate->format('F Y') }}</h5>
                        <h3 class="mb-0 mt-2 text-primary"><strong>${{ number_format($breakdown['net_salary'], 2) }}</strong></h3>
                        <small class="text-muted">
                            Fixed: ${{ number_format($breakdown['fixed_salary'], 2) }} 
                            + Bonuses: ${{ number_format($breakdown['bonuses_total'], 2) }} 
                            - Advances: ${{ number_format($breakdown['advances_total'], 2) }}
                            - Deductions: ${{ number_format($breakdown['deductions_total'], 2) }}
                        </small>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-lg-3 col-6 d-none">
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

    <!-- Deductions Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-minus-circle me-2"></i>
                        Deductions for {{ $currentDate->format('F Y') }}
                    </h4>
                    <a href="{{ route('salaries.deductions.create', $salary) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Add Deduction
                    </a>
                </div>
                
                <div class="card-body">
                    @if($salary->deductions && $salary->deductions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Reason</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salary->deductions as $deduction)
                                        <tr>
                                            <td class="text-danger"><strong>-${{ number_format($deduction->amount, 2) }}</strong></td>
                                            <td>{{ $deduction->deduction_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-warning">
                                                    {{ $deduction->reason }}
                                                </span>
                                            </td>
                                            <td>{{ $deduction->description ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $deduction->status === 'approved' ? 'success' : ($deduction->status === 'pending' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($deduction->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('salaries.deductions.edit', [$salary, $deduction]) }}" 
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('salaries.deductions.destroy', [$salary, $deduction]) }}" 
                                                          method="POST" class="d-inline" id="delete-deduction-{{ $deduction->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                onclick="confirmDelete('Deduction').then(result => { if(result) document.getElementById('delete-deduction-{{ $deduction->id }}').submit(); })">
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
                            <i class="fas fa-minus-circle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No deductions found.</p>
                            <a href="{{ route('salaries.deductions.create', $salary) }}" class="btn btn-danger">
                                <i class="fas fa-plus"></i> Create First Deduction
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Net Salary Summary -->
    @if(isset($breakdown))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Salary Summary for {{ $currentDate->format('F Y') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Fixed Salary</strong></td>
                                    <td class="text-end">${{ number_format($breakdown['fixed_salary'], 2) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>+ Bonuses</strong></td>
                                    <td class="text-end text-success">+${{ number_format($breakdown['bonuses_total'], 2) }}</td>
                                </tr>
                                <tr class="table-danger">
                                    <td><strong>- Advances</strong></td>
                                    <td class="text-end text-danger">-${{ number_format($breakdown['advances_total'], 2) }}</td>
                                </tr>
                                <tr class="table-danger">
                                    <td><strong>- Deductions</strong></td>
                                    <td class="text-end text-danger">-${{ number_format($breakdown['deductions_total'], 2) }}</td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>NET SALARY</strong></td>
                                    <td class="text-end"><strong class="fs-4">${{ number_format($breakdown['net_salary'], 2) }}</strong></td>
                                </tr>
                            </table>
                            
                            <div class="text-center mt-3">
                                <a href="{{ route('salaries.report.monthly', ['salary' => $salary, 'month' => $selectedMonth]) }}" 
                                   class="btn btn-primary me-2" target="_blank">
                                    <i class="fas fa-print"></i> Print Monthly Report
                                </a>
                                <a href="{{ route('salaries.report.yearly', ['salary' => $salary]) }}" 
                                   class="btn btn-info" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Yearly Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('css')
<link href="{{ asset('assets/css/admin/salaries.css') }}" rel="stylesheet">
@endsection