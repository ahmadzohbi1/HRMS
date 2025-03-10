@extends('layouts.master')

@section('title')
Employees Hour Rate
@endsection

@section('css')
<!-- DataTables -->
<link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')
Employees
@endslot
@slot('li_2')
{{ route('employees.index') }}
@endslot
@slot('title')
Employees Hour Rate
@endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-end mb-4" id="action_btns">
                    <a href="{{ route('hour_rate.create') }}"
                        class="btn btn-rounded btn-success waves-effect waves-light">
                        <i class="bx bx-plus font-size-16 me-2 align-middle"></i>Add Employee Rate
                    </a>
                </div>
                <table id="datatable" class="table-hover table-bordered nowrap w-100 table">
                    <thead>
                        <tr class="table-light">
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Hour Rate</th>
                            <th>Currency</th>
                            <th>Actions</th>
                        </tr>

                        @foreach ($hour_data as $data)

                            <tr class="odd">
                                <td>{{$data->employee_id}}</td>
                                <td>
                                    @if ($data->employee)
                                        {{$data->employee->name}}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{$data->hour_rate}}</td>
                                <td>{{$data->currency}}</td>
                                <td>
                                    <a href="{{ route('hour_rate.edit', $data->id) }}" class="btn btn-warning btn-sm">Edit
                                        Hour Rate</a>
                                </td>
                            </tr>
                        @endforeach

                    </thead>
                    <tbody></tbody>
                </table>

            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
@endsection