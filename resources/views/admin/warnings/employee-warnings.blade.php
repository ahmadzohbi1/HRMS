@extends('layouts.master')

@section('title')
    {{ $employee->name }} - Warnings
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1')
    <a href="{{ route('warnings.index') }}">Warnings</a>
    @endslot
    @slot('li_2')
    {{ route('warnings.employee', $employee->id) }}
    @endslot
    @slot('title')
    {{ $employee->name }} Warnings
    @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="card-title mb-2">
                                Warnings for: <span class="text-primary">{{ $employee->name }}</span>
                            </h4>
                            <p class="text-muted mb-0">
                                Total Warnings: 
                                <span class="badge badge-soft-danger font-size-14 ms-1">
                                    {{ $warningsCount }}
                                </span>
                            </p>
                        </div>
                        <div id="action_btns">
                            <a href="{{ route('warnings.index') }}" 
                               class="btn btn-secondary waves-effect waves-light me-2">
                                <i class="bx bx-arrow-back font-size-16 me-1"></i> Back to Employees
                            </a>
                            <a href="{{ route('warnings.create') }}" 
                               class="btn btn-rounded btn-success waves-effect waves-light">
                                <i class="bx bx-plus font-size-16 me-2 align-middle"></i> Add Warning
                            </a>
                        </div>
                    </div>

                    @if($warnings->count() > 0)
                        <table id="datatable" class="table-hover table-bordered nowrap w-100 table">
                            <thead>
                                <tr class="table-light">
                                    <th>Warning ID</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Date Issued</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($warnings as $warning)
                                    <tr>
                                        <td>{{ $warning->id }}</td>
                                        <td>{{ $warning->warning_title }}</td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 300px;" 
                                                 title="{{ $warning->warning_description }}">
                                                {{ Str::limit($warning->warning_description, 100) }}
                                            </div>
                                        </td>
                                        <td>{{ $warning->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('warnings.show', $warning->id) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="bx bx-show"></i> View
                                                </a>
                                                <a href="{{ route('warnings.edit', $warning->id) }}" 
                                                   class="btn btn-warning btn-sm">
                                                    <i class="bx bx-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('warnings.destroy', $warning->id) }}" 
                                                      method="POST" style="display:inline;"
                                                      onsubmit="return confirm('Are you sure you want to delete this warning?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="bx bx-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                               @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar-md mx-auto mb-4">
                                <div class="avatar-title bg-light rounded-circle text-primary h1">
                                    <i class="bx bx-info-circle"></i>
                                </div>
                            </div>
                            <h5 class="font-size-16">No Warnings Found</h5>
                            <p class="text-muted">{{ $employee->name }} has no warnings issued yet.</p>
                            <a href="{{ route('warnings.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i> Add First Warning
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection