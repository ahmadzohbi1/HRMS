@extends('layouts.master')

@section('title', 'Employee Salaries')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Employee Salaries</h3>
                    <a href="{{ route('salaries.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Salary
                    </a>
                </div>
                
                <!-- Month Navigation for Advances/Bonuses Display -->
                <div class="card-body border-bottom bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <span class="me-3"><strong>Viewing Advances/Bonuses for:</strong></span>
                                <a href="{{ route('salaries.index', ['month' => $previousMonth]) }}" 
                                   class="btn btn-outline-secondary btn-sm me-2">
                                    <i class="fas fa-chevron-left"></i> {{ Carbon\Carbon::createFromFormat('Y-m', $previousMonth)->format('M Y') }}
                                </a>
                                
                                <form method="GET" action="{{ route('salaries.index') }}" class="d-flex align-items-center">
                                    <input type="month" name="month" value="{{ $selectedMonth }}" 
                                           class="form-control form-control-sm me-2 w-auto"
                                           onchange="this.form.submit()">
                                    <button type="submit" class="btn btn-outline-primary btn-sm me-2">Go</button>
                                </form>
                                
                                <a href="{{ route('salaries.index', ['month' => $nextMonth]) }}" 
                                   class="btn btn-outline-secondary btn-sm">
                                    {{ Carbon\Carbon::createFromFormat('Y-m', $nextMonth)->format('M Y') }} <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-info fs-6">
                                {{ $currentDate->format('F Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                All employee salaries are shown below. Advances and bonuses columns show data for <strong>{{ $currentDate->format('F Y') }}</strong> only.
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Employee</th>
                                    <th>Fixed Salary</th>
                                    <th>Advances ({{ $currentDate->format('M Y') }})</th>
                                    <th>Bonuses ({{ $currentDate->format('M Y') }})</th>
                                    <th>Deductions ({{ $currentDate->format('M Y') }})</th>
                                    <th>Net Salary ({{ $currentDate->format('M Y') }})</th>
                                    <th>Status</th>
                                    <th>Effective Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salaries as $salary)
                                    @php
                                        // Calculate advances, bonuses, and deductions for the selected month only
                                        $monthlyAdvances = $salary->advances()
                                            ->whereMonth('advance_date', $currentDate->month)
                                            ->whereYear('advance_date', $currentDate->year)
                                            ->whereIn('status', ['approved', 'paid'])
                                            ->sum('amount');
                                        
                                        $monthlyBonuses = $salary->bonuses()
                                            ->whereMonth('bonus_date', $currentDate->month)
                                            ->whereYear('bonus_date', $currentDate->year)
                                            ->whereIn('status', ['approved', 'paid'])
                                            ->sum('amount');
                                        
                                        $monthlyDeductions = $salary->deductions()
                                            ->whereMonth('deduction_date', $currentDate->month)
                                            ->whereYear('deduction_date', $currentDate->year)
                                            ->whereIn('status', ['approved', 'deducted'])
                                            ->sum('amount');
                                        
                                        $netSalary = $salary->fixed_salary + $monthlyBonuses - $monthlyAdvances - $monthlyDeductions;
                                        
                                        // Count advances, bonuses, and deductions for the month
                                        $advancesCount = $salary->advances()
                                            ->whereMonth('advance_date', $currentDate->month)
                                            ->whereYear('advance_date', $currentDate->year)
                                            ->count();
                                            
                                        $bonusesCount = $salary->bonuses()
                                            ->whereMonth('bonus_date', $currentDate->month)
                                            ->whereYear('bonus_date', $currentDate->year)
                                            ->count();
                                        
                                        $deductionsCount = $salary->deductions()
                                            ->whereMonth('deduction_date', $currentDate->month)
                                            ->whereYear('deduction_date', $currentDate->year)
                                            ->count();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($salary->employee->image_url)
                                                    <img src="{{ asset('uploads/' . $salary->employee->image_url) }}"
                                                         class="rounded-circle me-2" 
                                                         width="32" height="32" alt="Avatar">
                                                         
                                                @else
                                                    <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                         class="avatar-sm">
                                                        <span class="text-white fw-bold">
                                                            {{ substr($salary->employee->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $salary->employee->name }}</div>
                                                    <small class="text-muted">{{ $salary->employee->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">
                                                ${{ number_format($salary->fixed_salary, 2) }}
                                            </span>
                                            @if($salary->version > 1)
                                                <small class="d-block">
                                                    <span class="badge bg-info version-badge">
                                                        Version {{ $salary->version }}
                                                    </span>
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-danger">
                                                ${{ number_format($monthlyAdvances, 2) }}
                                            </span>
                                            @if($advancesCount > 0)
                                                <small class="d-block text-muted">
                                                    {{ $advancesCount }} advance(s)
                                                </small>
                                            @else
                                                <small class="d-block text-muted">No advances</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-success">
                                                ${{ number_format($monthlyBonuses, 2) }}
                                            </span>
                                            @if($bonusesCount > 0)
                                                <small class="d-block text-muted">
                                                    {{ $bonusesCount }} bonus(es)
                                                </small>
                                            @else
                                                <small class="d-block text-muted">No bonuses</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-warning">
                                                ${{ number_format($monthlyDeductions, 2) }}
                                            </span>
                                            @if($deductionsCount > 0)
                                                <small class="d-block text-muted">
                                                    {{ $deductionsCount }} deduction(s)
                                                </small>
                                            @else
                                                <small class="d-block text-muted">No deductions</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold {{ $netSalary >= 0 ? 'text-success' : 'text-danger' }}">
                                                ${{ number_format($netSalary, 2) }}
                                            </span>
                                            <small class="d-block text-muted">
                                                = ${{ number_format($salary->fixed_salary, 2) }} 
                                                @if($monthlyBonuses > 0) +${{ number_format($monthlyBonuses, 2) }} @endif
                                                @if($monthlyAdvances > 0) -${{ number_format($monthlyAdvances, 2) }} @endif
                                                @if($monthlyDeductions > 0) -${{ number_format($monthlyDeductions, 2) }} @endif
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $salary->status === 'active' ? 'success' : ($salary->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($salary->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $salary->effective_date->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('salaries.show', ['salary' => $salary, 'month' => $selectedMonth]) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('salaries.edit', $salary) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('salaries.destroy', $salary) }}" 
                                                      method="POST" class="d-inline" id="delete-salary-{{ $salary->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            title="Delete"
                                                            onclick="confirmDelete('Salary Record').then(result => { if(result) document.getElementById('delete-salary-{{ $salary->id }}').submit(); })">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-money-bill-wave fa-2x mb-3"></i>
                                                <p>No salary records found.</p>
                                                <a href="{{ route('salaries.create') }}" class="btn btn-primary">
                                                    Create First Salary Record
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($salaries->count() > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-light border">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <i class="fas fa-info-circle me-2 text-info"></i>
                                            <strong>Summary:</strong> Showing {{ $salaries->count() }} employee salaries. 
                                            Advances and bonuses are filtered for <strong>{{ $currentDate->format('F Y') }}</strong>.
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <strong>Total Net Salaries: </strong>
                                            @php
                                                $totalNet = $salaries->sum(function($salary) use ($currentDate) {
                                                    $monthlyAdvances = $salary->advances()
                                                        ->whereMonth('advance_date', $currentDate->month)
                                                        ->whereYear('advance_date', $currentDate->year)
                                                        ->whereIn('status', ['approved', 'paid'])
                                                        ->sum('amount');
                                                    
                                                    $monthlyBonuses = $salary->bonuses()
                                                        ->whereMonth('bonus_date', $currentDate->month)
                                                        ->whereYear('bonus_date', $currentDate->year)
                                                        ->whereIn('status', ['approved', 'paid'])
                                                        ->sum('amount');
                                                    
                                                    return $salary->fixed_salary + $monthlyBonuses - $monthlyAdvances;
                                                });
                                            @endphp
                                            <span class="badge bg-primary fs-6">
                                                ${{ number_format($totalNet, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection