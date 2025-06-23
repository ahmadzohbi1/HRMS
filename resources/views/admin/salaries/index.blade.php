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
                                    <th>Total Advances</th>
                                    <th>Total Bonuses</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                    <th>Effective Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salaries as $salary)
                                    @php
                                        $totalAdvances = $salary->advances->whereIn('status', ['approved', 'paid'])->sum('amount');
                                        $totalBonuses = $salary->bonuses->whereIn('status', ['approved', 'paid'])->sum('amount');
                                        $netSalary = $salary->fixed_salary + $totalBonuses - $totalAdvances;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($salary->employee->image_url)
                                                    <img src="{{ $salary->employee->image_url }}" 
                                                         class="rounded-circle me-2" 
                                                         width="32" height="32" alt="Avatar">
                                                @else
                                                    <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 32px; height: 32px;">
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
                                        </td>
                                        <td>
                                            <span class="text-danger">
                                                ${{ number_format($totalAdvances, 2) }}
                                            </span>
                                            @if($salary->advances->count() > 0)
                                                <small class="d-block text-muted">
                                                    {{ $salary->advances->count() }} advance(s)
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-success">
                                                ${{ number_format($totalBonuses, 2) }}
                                            </span>
                                            @if($salary->bonuses->count() > 0)
                                                <small class="d-block text-muted">
                                                    {{ $salary->bonuses->count() }} bonus(es)
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold {{ $netSalary >= 0 ? 'text-success' : 'text-danger' }}">
                                                ${{ number_format($netSalary, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $salary->status === 'active' ? 'success' : ($salary->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($salary->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $salary->effective_date->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('salaries.show', $salary) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('salaries.edit', $salary) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('salaries.destroy', $salary) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this salary record?')">
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        border-top: none;
        font-weight: 600;
    }
    .btn-group .btn {
        border-radius: 0.25rem !important;
        margin-right: 2px;
    }
</style>
@endpush