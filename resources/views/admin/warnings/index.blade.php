@extends('layouts.master')

@section('title')
    Warnings
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
    @slot('li_1')
    Warnings
    @endslot
    @slot('li_2')
    {{ route('warnings.index') }}
    @endslot
    @slot('title')
    Employee Warnings
    @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-end mb-4" id="action_btns">
                        <a href="{{ route('warnings.create') }}" class="btn btn-rounded btn-success waves-effect waves-light"
                            style="margin-right: 10px;">
                            <i class="bx bx-plus font-size-16 me-2 align-middle"></i> Add Warning
                        </a>
                    </div>

                    <table id="datatable" class="table-hover table-bordered nowrap w-100 table">
                        <thead>
                            <tr class="table-light">
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Total Warnings</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach ($employees as $employee)
                                <tr>
                                    <td>{{ $employee->id }}</td>
                                    <td>{{ $employee->name }}</td>
                                    <td>
                                        <span class="badge badge-soft-danger font-size-12">
                                            {{ $employee->warnings_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('warnings.employee', $employee->id) }}" 
                                           class="btn btn-primary btn-md">
                                            <i class="bx bx-show font-size-16 me-1"></i>
                                            View Warnings ({{ $employee->warnings_count }})
                                        </a>
                                    </td>
                                </tr>
                           @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection